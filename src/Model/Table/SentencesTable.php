<?php
/**
    Tatoeba Project, free collaborative creation of languages corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang (tranglich@gmail.com)
    Copyright (C) 2009  Allan SIMON (allan.simon@supinfo.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace App\Model\Table;

use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\Query;
use Cake\Core\Configure;
use Cake\Database\Schema\TableSchema;
use Cake\Event\Event;
use Cake\Validation\Validator;
use App\Lib\LanguagesLib;
use App\Lib\Licenses;
use App\Model\CurrentUser;
use App\Model\Entity\User;
use App\Model\Exception\InvalidValueException;
use App\Model\Search;
use App\Event\ContributionListener;
use App\Event\DenormalizationListener;
use Cake\Utility\Hash;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Cache\Cache;
use Cake\ORM\RulesChecker;

class SentencesTable extends Table
{
    use ExposedFieldsTrait;

    const MIN_CORRECTNESS = -1;
    const MAX_CORRECTNESS = 0;
    
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('text', 'text');
        return $schema;
    }

    public function initialize(array $config)
    {
        $this->belongsTo('Users');
        $this->belongsTo('Languages');
        $this->belongsTo('TagsSentences');
        $this->belongsToMany('SentencesLists', [
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
        $this->belongsToMany('Tags', [
            'dependent' => true,
            'joinTable' => 'tags_sentences'
        ]);
        $this->belongsToMany('Translations', [
            'dependent' => false,
            'joinTable' => 'sentences_translations',
            'foreignKey' => 'sentence_id',
            'targetForeignKey' => 'translation_id',
        ]);
        $this->hasMany('Contributions');
        $this->hasMany('Transcriptions');
        $this->hasMany('Audios');
        $this->hasMany('DisabledAudios');
        $this->hasMany('Links');
        $this->hasMany('ReindexFlags');
        $this->hasMany('UsersSentences');
        $this->hasMany('Favorites_users', [
            'classname'  => 'favorites',
            'foreignKey' => 'favorite_id'
        ]);
        $this->hasMany('SentenceComments');
        $this->hasMany('SentenceAnnotations');
        $this->hasOne(
            'Base',
            [
                'className' => 'Sentences',
                'foreignKey' => 'id',
                'bindingKey' => 'based_on_id',
            ]
        )->setConditions(['Sentences.based_on_id >' => '0']);

        $this->addBehavior('Timestamp');
        if (Configure::read('AutoTranscriptions.enabled')) {
            $this->addBehavior('Transcriptable');
        }
        if (Configure::read('Search.enabled')) {
            $this->addBehavior('Sphinx', ['alias' => $this->getAlias()]);
        }
        $this->addBehavior('LimitResults');
        $this->addBehavior('NativeFinder');

        $this->getEventManager()->on(new ContributionListener());
        $this->getEventManager()->on(new DenormalizationListener());
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmpty('text');

        $sentenceLicenses = array_keys(Licenses::getSentenceLicenses());
        $validator
            ->add('license', [
                'inList' => [
                    'rule' => ['inList', $sentenceLicenses],
                    /* @translators: This string will be preceded by "Unable to
                    change the license to “{newLicense}” because:" */
                    'message' => __('This is not a valid license.')
                ],
                'isChanging' => [
                    'rule' => [$this, 'isChanging'],
                    'on' => 'update',
                    /* @translators: This string will be preceded by "Unable to
                    change the license to “{newLicense}” because:" */
                    'message' => __('This sentence is already under that license.'),
                ],
                'canSwitchLicense' => [
                    'rule' => [$this, 'canSwitchLicense'],
                    'on' => 'update',
                ]
            ])
            ->allowEmptyString(
                'license',
                function ($context) {
                    return $context['newRecord'] || CurrentUser::isAdmin();
                },
                __('This is not a valid license.')
            );

        $languages = array_keys(LanguagesLib::languagesInTatoeba());
        $validator
            ->allowEmpty('lang')
            ->add('lang', [
                'inList' => [
                    'rule' => ['inList', $languages]
                ]
            ]);

        $validator->dateTime('created');

        $validator->dateTime('modified');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->addCreate(
            function ($entity, $options) {
                if ($entity->based_on_id != 0) {
                    $baseSentence = $this->findById($entity->based_on_id)->first();
                    return !empty($baseSentence->license);
                } else {
                    return true;
                }
            },
            'isTranslatable'
        );

        $rules->addCreate(
            function ($entity, $options) {
                if (!empty($entity->license)) {
                    return CurrentUser::getSetting('can_switch_license') ||
                           $entity->license == CurrentUser::getSetting('default_license');
                } else {
                    return true;
                }
            },
            'hasCorrectLicense'
        );

        return $rules;
    }

    public function beforeSave($event, $entity, $options)
    {
        if ($entity->isNew()) { // creating a new sentence
            if (!$entity->license && $entity->user_id) {
                $user = $this->Users->get($entity->user_id);
                if ($user) {
                    $userDefaultLicense = $user->settings['default_license'];
                    $entity->license = $entity->based_on_id === 0 ?
                                       $userDefaultLicense :
                                       'CC BY 2.0 FR';
                }
            }
        }
    }

    public function isChanging($check, $context) {
        $id = $context['data']['id'];
        $newValue = $check;
        $currentValue = $this->get($id)->license;
        return $newValue !== $currentValue;
    }

    public function canSwitchLicense($check, $context) {
        if (CurrentUser::isAdmin()) {
            return true;
        }

        $sentenceId = $context['data']['id'];
        $sentence = $this->get($sentenceId, ['fields' => ['based_on_id', 'user_id', 'license']]);
        $isOriginal = !is_null($sentence->based_on_id) && $sentence->based_on_id == 0;
        if (!$isOriginal) {
            /* @translators: This string will be preceded by "Unable to
               change the license to “{newLicense}” because:" */
            return __('The sentence needs to be original (not initially derived from translation).');
        }

        $currentOwner = $sentence->user_id;
        $currentUser = CurrentUser::get('id');
        if ($currentUser != $currentOwner) {
            /* @translators: This string will be preceded by "Unable to
               change the license to “{newLicense}” because:" */
            return __('You\'re not the owner of this sentence.');
        }

        $originalCreator = $this->Contributions->getOriginalCreatorOf($sentenceId);
        if ($originalCreator !== $currentOwner) {
            /* @translators: This string will be preceded by "Unable to
               change the license to “{newLicense}” because:" */
            return __('The owner of the sentence needs to be its original creator.');
        }

        $newLicense = $check;
        $currentLicense = $sentence->license;
        $perms = array(null, 'CC BY 2.0 FR', 'CC0 1.0');
        $currentPermissiveness = array_search($currentLicense, $perms);
        $newPermissiveness = array_search($newLicense, $perms);
        if ($currentPermissiveness === false ||
            $newPermissiveness === false ||
            $newPermissiveness < $currentPermissiveness) {
            /* @translators: This string will be preceded by "Unable to
               change the license to “{newLicense}” because:" */
            return __('You can only switch to a more permissive license.');
        }

        return empty($sentence->getErrors());
    }

    /**
     * Called after a sentence is saved.
     */
    public function afterSave($event, $entity, $options = array())
    {
        $created = $entity->isNew();
        $event = new Event('Model.Sentence.saved', $this, array(
            'id' => $entity->id,
            'created' => $created,
            'data' => $entity
        ));
        $this->getEventManager()->dispatch($event);

        if (!$created && $entity->isDirty('lang')) {
            $oldLang = $entity->getOriginal('lang');
            $this->Contributions->updateLanguage($entity->id, $entity->lang);
            $this->Languages->incrementCountForLanguage($entity->lang);
            $this->Languages->decrementCountForLanguage($oldLang);

            // In the old language, add the sentence to the kill-list
            // so that it doesn't appear in results any more.
            // In addition an unknown language shouldn't be added.
            if ($oldLang) {
                $reindexFlag = $this->ReindexFlags->newEntity([
                    'sentence_id' => $entity->id,
                    'lang' => $oldLang,
                    'type' => 'removal',
                ]);
                $this->ReindexFlags->save($reindexFlag);
            }
        }

        $this->updateTags($entity);

        if ($entity->isDirty('modified')) {
            $this->needsReindex($entity->id);
        }
        $transNeedsReindex = $entity->isDirty('lang') || $entity->isDirty('user_id');
        if ($transNeedsReindex) {
            $this->flagTranslationsToReindex($entity->id);
        }
    }

    public function flagSentenceAndTranslationsToReindex($id) {
        $this->needsReindex($id);
        $this->flagTranslationsToReindex($id);
    }

    private function flagTranslationsToReindex($id)
    {
        $transIds = $this->Links->findDirectAndIndirectTranslationsIds($id);
        $this->needsReindex($transIds);
    }

    private function updateTags($entity)
    {
        if (!$entity->isNew() && $entity->isDirty('text')) {
            $OKTagId = $this->Tags->getIdFromName($this->Tags->getOKTagName());
            $this->TagsSentences->removeTagFromSentence($OKTagId, $entity->id);
        }
    }

    public function needsReindex($ids)
    {
        if (empty($ids)) {
            return;
        }
        $sentences = $this->find('all')
            ->where(['id' => $ids, 'lang IS NOT' => null], ['id' => 'integer[]'])
            ->select(['sentence_id' => 'id', 'lang'])
            ->formatResults(function ($results) {
                return $results->map(function ($row) {
                    $row['type'] = 'change';
                    return $row;
                });
            })
            ->disableHydration()
            ->toList();
        $data = $this->ReindexFlags->newEntities($sentences);
        $this->ReindexFlags->saveMany($data);
    }

    public function beforeDelete($event, $entity, $options)
    {
        $hasAudio = $this->hasAudio($entity->id);
        if ($hasAudio) {
            return false;
        }

        return true;
    }

    /**
     * Call after a deletion.
     *
     * @return void
     */
    public function afterDelete($event, $entity, $options)
    {
        $sentenceId = $entity->id;
        $sentenceLang = $entity->lang;
        // --- Logs for sentence ---
        $this->Contributions->saveSentenceContribution(
            $sentenceId,
            $sentenceLang,
            $entity->script,
            $entity->text,
            'delete'
        );

        // Reindex translations
        $translationsIds = $this->Links->findDirectAndIndirectTranslationsIds($entity->id);
        $this->needsReindex($translationsIds);

        // Add the sentence to the kill-list so that it won't appear in search results anymore
        if ($sentenceLang) {
            $reindexFlag = $this->ReindexFlags->newEntity([
                'sentence_id' => $sentenceId,
                'lang' => $sentenceLang,
                'type' => 'removal',
            ]);
            $this->ReindexFlags->save($reindexFlag);
        }

        // Remove links
        $conditions = ['OR' => [
            'sentence_id' => $sentenceId,
            'translation_id'=> $sentenceId
        ]];
        $links = $this->Links->find('all')->where($conditions)->toList();
        $deleted = $this->Links->deleteAll($conditions);

        // --- Logs for links ---
        foreach ($links as $link) {
            $this->Contributions->saveLinkContribution(
                $link->sentence_id, $link->translation_id, 'delete'
            );
        }

        // Remove transcriptions
        $this->Transcriptions->deleteAll(['sentence_id' => $sentenceId]);

        // Decrement statistics
        $this->Languages->decrementCountForLanguage($sentenceLang);
    }

    private function applyHideFields($entity, $hide)
    {
        if (is_array($entity)) {
            foreach ($entity as $e) {
                $this->applyHideFields($e, $hide);
            }
        } elseif ($entity instanceof Entity) {
            foreach ($hide as $key => $value) {
                if ($key == 'fields') {
                    $entity->setHidden($value, true);
                } else {
                    $contained = $entity->get($key);
                    if (!is_null($contained)) {
                        $this->applyHideFields($contained, $value);
                    }
                }
            }
        }
        return $entity;
    }

    /**
     * This allows to hide some extra fields
     * in json in a similar fashion as contain().
     */
    public function findHideFields(Query $query, array $options)
    {
        $hide = $this->hideFields();
        return $query->formatResults(function($results) use ($hide) {
            return $results->map(function($result) use ($hide) {
                return $this->applyHideFields($result, $hide);
            });
        });
    }

    private function sortOutTranslations($result, $translationLanguages) {
        $directTranslations = [];
        $indirectTranslations = [];

        if (isset($result->translations)) {
            $parentIds = [$result->id];
            $indirectIds = [];

            foreach ($result->translations as $translation) {
                $parentIds[] = $translation->id;
                if ($translation->indirect_translations) {
                    foreach ($translation->indirect_translations as $indirectTranslation) {
                        if (!in_array($indirectTranslation->id, $indirectIds)) {
                            $indirectTranslations[] = $indirectTranslation;
                            $indirectIds[] = $indirectTranslation->id;
                        }
                    }
                    unset($translation->indirect_translations);
                    $translation->isDirect = true;
                    $directTranslations[] = $translation;
                }
            }

            if (!empty($translationLanguages)) {
                $filter = function ($item) use ($translationLanguages) {
                    return in_array($item->lang, $translationLanguages);
                };
                $directTranslations = array_filter($directTranslations, $filter);
                $indirectTranslations = array_filter($indirectTranslations, $filter);
            }

            $indirectTranslations = array_filter($indirectTranslations, function ($item) use ($parentIds) {
                return !in_array($item->id, $parentIds);
            });

            $directTranslations = Hash::sort($directTranslations, '{n}.lang', 'asc');
            $indirectTranslations = Hash::sort($indirectTranslations, '{n}.lang', 'asc');
        }

        return [$directTranslations, $indirectTranslations];
    }

    public function findFilteredTranslations($query, $options) {
        if (!empty($options['translationLang']) && $options['translationLang'] != 'und') {
            $translationLanguages = (array)$options['translationLang'];
        } else {
            $translationLanguages = CurrentUser::getLanguages();
        }
        return $query->formatResults(function($results) use ($translationLanguages) {
            return $results->map(function($result) use ($translationLanguages) {
                $result['translations'] = $this->sortOutTranslations($result, $translationLanguages);
                return $result;
            });
        });
    }

    /**
     * Fast random sentence id selection
     * when not selecting any particular language
     */
    public function getRandomIdAmongAllLanguages()
    {
        $res = $this->find()
                    ->select(['min' => 'min(id)', 'max' => 'max(id)'])
                    ->first();
        if ($res) {
            $potentialIds = [];
            for ($i = 0; $i < 100; $i++) {
                $potentialIds[] = mt_rand($res->min, $res->max);
            }
            $res = $this->find()
                        ->select(['id'])
                        ->where(['id in' => $potentialIds])
                        ->where(['user_id !=' => 0, 'correctness' => 0])
                        ->order(['rand()'])
                        ->first();
            if ($res) {
                return $res->id;
            }
        }

        return null;
    }

    /**
     * Get the id of a random sentence, from a particular language if $lang is set.
     *
     * @param string $lang Restrict random id from the specified code lang.
     *
     * @return int A random id.
     */
    public function getRandomId($lang = null)
    {
        if (!$lang) {
            return $this->getRandomIdAmongAllLanguages();
        } else {
            $arrayIds = $this->getSeveralRandomIds($lang, 1);
            if (is_bool($arrayIds)) {
                return $arrayIds;
            }

            return $arrayIds[0];
        }
    }

    /**
     * Request for several random sentence id.
     *
     * @param string $lang             Language of the sentences we want.
     * @param int    $numberOfIdWanted Number of ids needed.
     *
     * @return array An array of ids.
     */
    public function getSeveralRandomIds($lang = null, $numberOfIdWanted = 10)
    {
        if(Configure::read('Search.enabled') == false) {
            return null;
        }

        $returnIds = array ();
        // exit if we don't have good params
        if (!is_numeric($numberOfIdWanted)) {
            return $returnIds ;
        }

        $cacheKey = "rand_array_$lang";


        $arrayRandom = Cache::read($cacheKey);
        if (!is_array($arrayRandom) || empty($arrayRandom)) {
            $arrayRandom = $this->_getRandomsToCached($lang, 500);
        }

        if(is_array($arrayRandom)){
            for ($i = 0; $i < $numberOfIdWanted; $i++) {

                $id = array_pop($arrayRandom);
                // if we have take all the cached ids, then we request a new bunch
                if ($id === NULL) {
                    $arrayRandom = $this->_getRandomsToCached($lang, 500);
                    $id = array_pop($arrayRandom);
                }
                array_push(
                    $returnIds,
                    $id
                );

            }
        // we cache the random ids array less all the poped value, for latter use
        Cache::write($cacheKey, $arrayRandom);

            return $returnIds;
        }

        return null;

    }


    /**
     * Get from the random id source an array of X elements that will
     * cached after, this way we do not need to request the random id source
     * each time we need a random id
     *
     * @param string $lang             In which language takes the ids, null if from all
     * @param int    $numberOfIdWanted Size of the array we will return
     *
     * @return array An array of int
     */
    private function _getRandomsToCached($lang, $numberOfIdWanted) {
        $search = new Search();
        try {
            $search->filterByLanguage([$lang]);
        } catch (InvalidValueException $e) {
            // normal outcome when $lang == 'und'
        }
        $search->sort('random');
        $search->filterByOrphanship(false); // exclude orphans
        $search->filterByCorrectness(false); // exclude unapproved
        $sphinx = $search->asSphinx();
        $sphinx['limit'] = $numberOfIdWanted;

        $results = $this
            ->find('all', [
                'fields' => ['id'],
                'sphinx' => $sphinx,
            ])
            ->all()
            ->extract('id')
            ->toArray();

        return $results;
    }

    /**
     * Returns the appropriate value for the fields() parameter
     * to be used on the this model.
     *
     * @param array $what What to include in the fields. By default it contains
     *                    the necessary to display a sentence block. Array keys:
     *
     *       sentenceDetails: include details for the sentence page
     */
    public function fields($what = [])
    {
        $fields = [
            'id',
            'text',
            'lang',
            'user_id',
            'correctness',
            'script',
            'license',
        ];

        if (isset($what['sentenceDetails'])) {
            $fields[] = 'based_on_id';
        }

        return $fields;
    }

    /**
     * Returns the appropriate value for the contain() parameter
     * to be used on the this model.
     *
     * @param array $what What to include in the containment. By default it is
     *                    what is needed to display a sentence block without
     *                    translations. Array keys:
     *
     *       translations: include translations for sentence block
     *       sentenceDetails: include details for the sentence page
     */
    public function contain($what = [])
    {
        $audioContainment = function (Query $q) use ($what) {
            $q = $q->select(['id', 'external', 'sentence_id']);

            $usersFields = ['username'];
            if (isset($what['sentenceDetails'])) {
                $usersFields[] = 'audio_license';
                $usersFields[] = 'audio_attribution_url';
            }
            return $q->contain(['Users' => ['fields' => $usersFields]]);
        };

        $transcriptionsContainment = [
            'Users' => ['fields' => ['username']],
        ];
        $contain = [
            'Users' => [
                'fields' => ['id', 'username', 'role', 'level']
            ],
            'Audios' => $audioContainment,
            'Transcriptions' => $transcriptionsContainment,
        ];

        if (CurrentUser::isAdmin() && isset($what['sentenceDetails'])) {
            $contain['DisabledAudios'] = $audioContainment;
        }

        if (CurrentUser::isMember()) {
            $contain += [
                'Favorites_users' => function (Query $q) {
                    return $q->select(['id', 'favorite_id'])
                             ->where(['user_id' => CurrentUser::get('id')]);
                },
                'SentencesLists' => function (Query $q) {
                    return $q->select(['id', 'SentencesSentencesLists.sentence_id'])
                            ->where([
                                'OR' => [
                                    'user_id' => CurrentUser::get('id'),
                                    'visibility' => 'public',
                               ]
                            ]);
                },
                'UsersSentences' => function (Query $q) {
                    return $q->select(['sentence_id', 'correctness'])
                             ->where(['user_id' => CurrentUser::get('id')]);
                },
            ];
        }

        if (isset($what['translations']) && $what['translations']) {
            $translationFields = [
                'id', 'text', 'lang', 'correctness', 'script',
                'SentencesTranslations.sentence_id'
            ];
            $contain['Translations'] = [
                'fields' => $translationFields,
                'IndirectTranslations' => [
                    'fields' => $translationFields,
                    'Audios' => $audioContainment,
                    'Transcriptions' => $transcriptionsContainment,
                ],
                'Audios' => $audioContainment,
                'Transcriptions' => $transcriptionsContainment,
            ];
        }

        if (isset($what['sentenceDetails'])) {
            $contain['Base']['fields'] = ['text'];
        }

        return $contain;
    }

    /**
     * Override standard paginateCount method to eliminate unnecessary joins.
     * If $conditions is empty, as in Sphinx search, return default behavior.
     *
     * @param  array   $conditions
     * @param  integer $recursive
     * @param  array   $extra
     *
     * @return integer
     */
    public function paginateCount(
        $conditions = null,
        $recursive = 0,
        $extra = array()
    ) {
        $parameters = compact('conditions');
        $extra['contain'] = [];

        return $this->find('count', array_merge($parameters, $extra));
    }

    /**
     * Value for the hideFields finder option.
     * See findHideFields().
     */
    private function hideFields()
    {
        $hideAudio = ['fields' => ['user', 'external', 'sentence_id']];
        return [
            'fields' => ['user_id'],
            'user' => ['fields' => ['id', 'role', 'level']],
            'audios' => $hideAudio,
            'translations' => ['audios' => $hideAudio],
        ];
    }

    /**
     * Get all the informations needed to display a sentence.
     *
     * @param int $id Id of the sentence.
     * @param array $what parameter to $this->fields() and $this->contain().
     *
     * @return ResultSet Information about the sentence.
     */
    public function getSentenceWith($id, $what = [], $translationLang = null)
    {
        return $this->find('filteredTranslations', [
                'translationLang' => $translationLang
            ])
            ->find('nativeMarker')
            ->find('hideFields')
            ->where(['Sentences.id' => $id])
            ->contain($this->contain($what))
            ->select($this->fields($what))
            ->first();
    }

    /**
     * Get number of sentences owned by a given user.
     *
     * @param int $userId Id of the user we want number of sentences of
     *
     * @return int
     */
    public function numberOfSentencesOwnedBy($userId)
    {
        return $this->find()
            ->where(['user_id' => $userId])
            ->count();
    }

    /**
     * Get translations of a given sentence and translations of translations.
     *
     * @param int    $id   Id of the sentence we want translations of.
     * @param string $lang To filter translations only in a language.
     *
     * @return array Array of translations (direct and indirect).
     */
    public function getTranslationsOf($id,$lang = null)
    {
        if ( ! is_numeric($id) ) {
            return array();
        }

        if (!empty($lang) && $lang != "und") {
            $languages = array($lang);
        } else {
            $languages = CurrentUser::getLanguages();
        }

        $translations = $this->Translations->getTranslationsOf($id, $languages);
        $results = [0 => [], 1 => []];
        foreach($translations as $translation) {
            $translation->isDirect = $translation->type == 0;
            $results[$translation->type][] = $translation;
        }

        return $results;
    }


    /**
     * Return previous and following sentence id
     *
     * @param int    $sourceId The sentence id to take as starting point
     * @param string $lang     Will return the next and following sentence id
     *                         in this language
     *
     * @return array
     */
    public function getNeighborsSentenceIds($sourceId, $lang = null)
    {
        $langCondition = [];
        if (!empty($lang) && $lang != 'und') {
            $langCondition = ['lang' => $lang];
        }

        $prev = $this->find()
            ->select('id')
            ->order(['id' => 'DESC'])
            ->where(['id <' => $sourceId] + $langCondition)
            ->first();
        $next = $this->find()
            ->select('id')
            ->orderAsc('id')
            ->where(['id >' => $sourceId] + $langCondition)
            ->first();

        $neighbors = [
            'prev' => $prev ? $prev->id : null,
            'next' => $next ? $next->id : null,
        ];

        return $neighbors;
    }

    /**
     * Return all tags on a given sentence
     *
     * @param int $sentenceId The sentence which we want the tags
     *
     * @return array
     */
    public function getAllTagsOnSentence($sentenceId)
    {
        return $this->TagsSentences->getAllTagsOnSentence($sentenceId);
    }

    /**
     * Add translation to sentence with given id. Adding a translation means adding
     * a new sentence, and two links.
     *
     * @param int    $sentenceId      Id of the sentence that is translated.
     * @param int    $sentenceLang    Language of the sentence that is translated.
     * @param string $translationText Text of the translation.
     * @param string $translationLang Language of the translation.
     *
     * @return Cake\ORM\Entity|false
     */
    public function saveTranslation(
        $sentenceId,
        $sentenceLang,
        $translationText,
        $translationLang,
        $translationCorrectness = 0
    ) {
        $userId = CurrentUser::get('id');

        // saving translation
        $sentenceSaved = $this->saveNewSentence(
            $translationText,
            $translationLang,
            $userId,
            $translationCorrectness,
            $sentenceId,
            'CC BY 2.0 FR'
        );

        // saving links
        if ($sentenceSaved) {
            $this->Links->add($sentenceId, $sentenceSaved->id, $sentenceLang, $translationLang);
        }

        return $sentenceSaved; // The most important is that the sentence is saved.
                               // Never mind the links.
    }

    /**
     * Add a new sentence in the database
     *
     * @param string   $text        The text of the sentence.
     * @param string   $lang        The lang of the sentence.
     * @param int      $userId      The id of the user who added this sentence.
     * @param int      $correctness Correctness level of sentence.
     * @param int|null $basedOnId   The ID of the sentence this sentence is translated from,
     *                              or 0 if it's an original sentence, or null if unknown.
     * @param string   $license     The license of the sentence.
     *
     * @return Cake\ORM\Entity|false
     */
    public function saveNewSentence($text, $lang, $userId, $correctness = 0, $basedOnId = 0, $license = null)
    {
        $newSentence = $this->newEntity(
            [
                'text' => $text,
                'lang' => $lang,
                'user_id' => $userId,
                'correctness' => $correctness,
                'based_on_id' => $basedOnId,
                'license' => $license
            ]
        );
        if ($newSentence->hasErrors()) {
            return false;
        }

        $sentence = $this->find('all')
                         ->where(['text' => $newSentence->text, 'lang' => $lang])
                         ->first();

        // Duplicate sentence found
        if ($sentence != null) {
            // If sentence is orphan
            if(empty($sentence->user_id)) {
                $sentence->user_id = $userId;
                return $this->save($sentence);
            } else {
                // If sentence is owned by spammer/inactive user, and you are an advanced contributor (or higher), 
                // adopt sentence
                if ($sentence->user_id == $userId) {
                    $sentence->isDuplicate = true;
                    return $sentence;
                }

                $sentence = $this->setOwner($sentence->id, $userId, CurrentUser::get('role'));

                if ($sentence->user_id != $userId) {
                    $sentence->isDuplicate = true;
                    return $sentence;
                }
                return $sentence;
            }
        }
        
        return $this->save($newSentence);
    }

    /**
     * Add a new sentence and a translation in the database.
     *
     * @param string $sentenceText    The text of the sentence.
     * @param string $sentenceLang    The lang of the sentence.
     * @param string $translationText The text of the translation.
     * @param string $translationLang The lang of the translation.
     * @param int    $userId          The id of the user who added them.
     *
     * @return void
     */
    public function saveNewSentenceWithTranslation(
        $sentenceText,
        $sentenceLang,
        $translationText,
        $translationLang,
        $userId
    ) {
        // saving sentence
        $sentenceSaved = $this->saveNewSentence(
            $sentenceText,
            $sentenceLang,
            $userId
        );
        $sentenceId = $this->id;
        // saving translation
        $translationSaved = $this->saveNewSentence(
            $translationText,
            $translationLang,
            $userId,
            0,
            $sentenceId
        );

        $translationId = $this->id;
        // saving links
        if ($sentenceSaved && $translationSaved) {
            $this->Link->add(
                $sentenceId,
                $translationId,
                $sentenceLang,
                $translationLang
            );
        }
    }

    /**
     * wrapping function
     *
     * @param int $id Sentence id.
     *
     * @return array
     */
    public function getContributionsRelatedToSentence($id)
    {
        return $this->Contributions->getContributionsRelatedToSentence($id);

    }

    /**
     * wrapping function
     *
     * @param int $id Sentence id.
     *
     * @return array
     */
    public function getCommentsForSentence($id)
    {
        return $this->SentenceComments->getCommentsForSentence($id);
    }


    /**
     * Set owner for a sentence.
     *
     * @param int $sentenceId         Id of the sentence.
     * @param int $userId             Id of the user.
     * @param int $currentUserRole    Role of the currently logged-in user.
     *
     * @return Cake\ORM\Entity
     */
    public function setOwner($sentenceId, $userId, $currentUserRole)
    {
        $sentence = $this->get($sentenceId);
        $currentOwner = $this->getOwnerInfoOfSentence($sentenceId);
        $ownerId = $currentOwner['id'];
        $ownerRole = $currentOwner['role'];

        $isOwnerInactive = in_array($ownerRole, [User::ROLE_SPAMMER, User::ROLE_INACTIVE]);
        $isCurrentUserTrusted = in_array($currentUserRole, User::ROLE_ADV_CONTRIBUTOR_OR_HIGHER);
        $isAdoptable = $ownerId == 0 || ($isOwnerInactive && $isCurrentUserTrusted);

        if ($isAdoptable) {
            $sentence->user_id = $userId;
            return $this->save($sentence);
        }
        return $sentence;
    }


    /**
     * Unset owner for a sentence.
     *
     * @param int $sentenceId Id of the sentence.
     * @param int $userId     Id of the user.
     *
     * @return bool
     */
    public function unsetOwner($sentenceId, $userId)
    {
        $sentence = $this->get($sentenceId, ['fields' => ['id', 'user_id']]);
        $currentOwner = $this->getOwnerInfoOfSentence($sentenceId);
        if ($currentOwner->id == $userId) {
            $sentence->user_id = null;
            $this->save($sentence);
            return true;
        }
        return false;
    }


    /**
     * Return sentence owner's id.
     *
     * @param int $sentenceId Id of the sentence.
     *
     * @return array
     */
    public function getOwnerInfoOfSentence($sentenceId)
    {
        $sentence = $this->get($sentenceId, ['contain' => 'Users']);

        return $sentence->user;
    }


    /**
     * Change language of a sentence.
     *
     * @param int $sentenceId Id of the sentence.
     * @param int $newLang    New Language.
     *
     * @return string
     */
    public function changeLanguage($sentenceId, $newLang)
    {
        try {
            $sentence = $this->get($sentenceId, [
                'fields' => ['id', 'lang', 'text', 'user_id']
            ]);
        } catch (RecordNotFoundException $e) {
            return false;
        }

        $ownerId = $sentence->user_id;
        $prevLang = $sentence->lang;
        $currentUserId = CurrentUser::get('id');

        if (($ownerId == $currentUserId || CurrentUser::isModerator()) && !$this->hasAudio($sentence->id)) {
            $this->patchEntity($sentence, ['lang' => $newLang]);
            $this->save($sentence);
            return $sentence->lang;
        }

        return $prevLang;
    }


    /**
     * Return text of a sentence for given id.
     *
     * @param int $sentenceId Id of the sentence
     *
     * @return string
     */
    public function getSentenceTextForId($sentenceId)
    {
        try {
            $result = $this->get($sentenceId, ['fields' => 'text']);
            return $result->text;
        } catch (RecordNotFoundException $e) {
            return '';
        }
    }

    /**
    * Return language code for sentence with given id.
    *
    * @param int $sentenceId Id of the sentence
    *
    * @return void
    */
    public function getLanguageCodeFromSentenceId($sentenceId)
    {
        try {
            $result = $this->get($sentenceId, ['fields' => ['lang']]);
            return $result->lang;
        } catch (RecordNotFoundException $e) {
            return null;
        }
    }


    /**
     * Save the correctness of a sentence. Only corpus
     * maintainers or admins can change this value.
     *
     * @param int $sentenceId  Id of the sentence.
     * @param int $correctness Correctness of the sentence.
     *
     * @return bool
     */
    public function editCorrectness($sentenceId, $correctness)
    {
        $sentence = $this->get($sentenceId);
        $sentence->correctness = $correctness;
        return $this->save($sentence);
    }

    /**
     * Marks all sentences of the user as incorrect.
     * Only admins can perform this action.
     *
     * @param string $username User name of the user.
     *
     * @return array|false
     */
    public function markUnreliable($username)
    {
        $user = $this->Users->getInformationOfUser($username);
        if(empty($user) || !CurrentUser::canMarkSentencesOfUser($user)) {
            return false;
        }

        $sentences = $this->find('all')
            ->where(['user_id' => $user['id'], 'correctness' => 0])
            ->select(['id', 'correctness'])
            ->toList();

        $editValues = array();
        foreach($sentences as $sentence) {
            array_push($editValues,  [ 'id' => $sentence['id'], 'correctness' => -1 ]);
        }

        $this->patchEntities($sentences, $editValues);
        return $this->saveMany($sentences);
    }

    public function getSentencesLang($sentencesIds) {
        if (empty($sentencesIds)) {
            return [];
        }

        $result = $this->find('all')
        ->where(['id' => $sentencesIds], ['id' => 'integer[]'])
        ->select(['lang', 'id'])
        ->toList();

        return Hash::combine($result, '{n}.id', '{n}.lang');
    }

    public function sphinxAttributesChanged(&$attributes, &$values, &$isMVA, $entity) {
        $sentenceId = $entity->id;
        $values[$sentenceId] = array();
        if ($entity->isDirty('user_id')) {
            $attributes[] = 'user_id';
            $sentenceOwner = $entity->user_id;
            $values[$sentenceId][] = $sentenceOwner;
        }
        if ($entity->isDirty('correctness')) {
            $attributes[] = 'ucorrectness';
            $sentenceUCorrectness = $entity->correctness + 128;
            $values[$sentenceId][] = $sentenceUCorrectness;
        }
        if (count($values[$sentenceId]) == 0)
            unset($values[$sentenceId]);
    }

    /**
     * Edit the sentence.
     *
     * @param array $data We're taking the data from the AJAX request. It should contain
     *                    the key 'id' for the sentence ID and either 'text' or
     *                    'lang' or both.
     *
     * @return Entity|false
     */
    public function editSentence($data)
    {
        $id = (int)$data['id'] ?? null;
        try {
            $sentence = $this->get($id);
        } catch (RecordNotFoundException $e) {
            return false;
        }

        if ($this->_cantEditSentence($sentence)) {
            return $sentence;
        }

        if ($this->hasAudio($id)) {
            return $sentence;
        }

        $this->patchEntity($sentence, $data, ['fields' => ['text', 'lang']]);
        if ($sentence->isDirty()) {
            $sentenceSaved = $this->save($sentence);
            if ($sentenceSaved) {
                $this->UsersSentences->makeDirty($id);
            }
            return $sentenceSaved;
        }
        return $sentence;
    }

    /**
     * Return true if user can't edit sentence.
     *
     * @param  array $sentence Sentence to edit.
     *
     * @return boolean
     */
    private function _cantEditSentence($sentence)
    {
        return !$sentence ||
            !CurrentUser::canEditSentenceOfUserId($sentence->user_id);
    }

    /**
     * Return true if sentence has audio.
     *
     * @return boolean
     */
    public function hasAudio($id)
    {
        $count = $this->Audios->findBySentenceId($id)->count();
        return $count > 0;
    }

    public function deleteSentence($id)
    {
        if (empty($id)) {
            return false;
        }

        $sentence = $this->get($id);
        if (!$sentence) {
            return false;
        }

        if (!CurrentUser::canRemoveSentence($sentence->id, $sentence->user_id)) {
            return false;
        }

        return $this->delete($sentence);
    }

    /**
     * Efficiently compute the list of all languages
     * having at least one sentence.
     *
     * @return array Array of language codes,
     *               including null for "unknown language"
     */
    public function languagesHavingSentences()
    {
        return $this->find()
            ->select(['lang'])
            ->distinct(['lang'])
            ->all()
            ->extract('lang')
            ->toArray();
    }
}
