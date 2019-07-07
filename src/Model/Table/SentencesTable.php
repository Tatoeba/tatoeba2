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

use Cake\ORM\Table;
use Cake\Core\Configure;
use Cake\Database\Schema\TableSchema;
use Cake\Event\Event;
use Cake\Validation\Validator;
use App\Lib\LanguagesLib;
use App\Model\CurrentUser;
use App\Model\Entity\User;
use App\Event\ContributionListener;
use Cake\Utility\Hash;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Cache\Cache;

class SentencesTable extends Table
{
    const MIN_CORRECTNESS = -1;
    const MAX_CORRECTNESS = 0;
    
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('text', 'text');
        $schema->setColumnType('created', 'string');
        $schema->setColumnType('modified', 'string');
        $schema->setColumnType('hash', 'string');
        return $schema;
    }

    public function initialize(array $config)
    {
        $this->belongsTo('Users');
        $this->belongsTo('Languages');
        $this->belongsTo('TagsSentences');
        $this->belongsToMany('SentencesLists', [
            'dependent' => true
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
        $this->hasMany('Links');
        $this->hasMany('ReindexFlags');
        $this->hasMany('UsersSentences');
        $this->hasMany('Favorites_users', [
            'classname'  => 'favorites',
            'foreignKey' => 'favorite_id'
        ]);
        $this->hasMany('SentenceComments');
        $this->hasMany('SentenceAnnotations');
        
        $this->addBehavior('Hashable');
        $this->addBehavior('Timestamp');
        if (Configure::read('AutoTranscriptions.enabled')) {
            $this->addBehavior('Transcriptable');
        }
        $this->addBehavior('Sphinx', ['alias' => $this->getAlias()]);

        $this->getEventManager()->on(new ContributionListener());
        //$this->getEventManager()->attach(new UsersLanguagesListener());
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmpty('text');
            
        $validator
            ->add('license', [
                'inList' => [
                    'rule' => ['inList', ['CC0 1.0', 'CC BY 2.0 FR']],
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
            ->allowEmpty('license');

        $languages = array_keys(LanguagesLib::languagesInTatoeba());
        $validator
            ->allowEmpty('lang')
            ->add('lang', [
                'inList' => [
                    'rule' => ['inList', $languages]
                ]
            ]);
            
        return $validator;
    }


    private function clean($text)
    {
        $text = trim($text);
        // Strip out any byte-order mark that might be present.
        $text = preg_replace("/\xEF\xBB\xBF/", '', $text);
        // Replace any series of spaces, newlines, tabs, or other
        // ASCII whitespace characters with a single space.
        $text = preg_replace('/\s+/', ' ', $text);
        // MySQL will truncate to a byte length of 1500, which may split
        // a multibyte character. To avoid this, we preemptively
        // truncate to a maximum byte length of 1500. If a multibyte
        // character would be split, the entire character will be
        // truncated.
        $text = mb_strcut($text, 0, 1500, "UTF-8");
        return $text;
    }

    public function beforeSave($event, $entity, $options)
    {
        if ($entity->text) {
            $cleanedText = $this->clean($entity->text);
            if ($cleanedText !== $entity->text) {
                $entity->text = $cleanedText;
            }
        }        

        if ($entity->isNew()) { // creating a new sentence
            if (!$entity->license && $entity->user_id) {
                $user = $this->Users->get($entity->user_id);
                if ($user) {
                    $userDefaultLicense = $user->settings['default_license'];
                    $entity->license = $userDefaultLicense;
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
        $result = $this->find('all')
            ->where(['id' => $ids], ['id' => 'integer[]'])
            ->select(['id', 'lang'])
            ->toList();
        $sentences = [];
        foreach ($result as $sentence) {
            $sentences[] = [
                'sentence_id' => $sentence->id,
                'lang' => $sentence->lang
            ];
        }
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

        // Add the sentence to the kill-list
        // so that it won't appear in search results anymore
        $reindexFlag = $this->ReindexFlags->newEntity([
            'sentence_id' => $sentenceId,
            'lang' => $sentenceLang
        ]);
        $this->ReindexFlags->save($reindexFlag);

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

    public function afterFind($results, $primary = false) {
        foreach ($results as &$result) {
            /* Work around afterFind() not being called by Containable */
            if (isset($result['Translation'])) {
                $result['Translation'] = $this->Behaviors->Transcriptable->afterFind(
                    $this->Translation,
                    $result['Translation'],
                    false
                );
            }
        }
        return $results;
    }

    public function findFilteredTranslations($query, $options) {
        $translationLanguages = CurrentUser::getLanguages();
        if (!empty($options['translationLang']) && $options['translationLang'] != 'und') {
            $translationLanguages[] = $options['translationLang'];
        }
        return $query->formatResults(function($results) use ($translationLanguages) {
            return $results->map(function($result) use ($translationLanguages) {
                
                $directTranslations = [];
                $indirectTranslations = [];
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
                
                $result['translations'] = [$directTranslations, $indirectTranslations];

                return $result;
            });
        });
    }

    public function findWithSphinx($query, $options)
    {
        $query
            ->counter(function($query) {
                return $this->getTotal();
            })
            ->offset(0);

        return $this->findFilteredTranslations($query, $options);
    }

    /**
     * Custom ->find('random', ...) function.
     */
    public function _findRandom($state, $query, $results = array())
    {
        if ($state == 'before') {
            $ids = $this->getSeveralRandomIds($query['lang'], $query['number']);
            $query['conditions'][$this->alias.'.'.$this->primaryKey] = $ids;
            unset($query['lang']);
            unset($query['number']);
            return $query;
        } else {
            return $results;
        }
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
        if (!$lang || $lang == 'und') {
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
    public function getSeveralRandomIds($lang = 'und',  $numberOfIdWanted = 10)
    {
        if(Configure::read('Search.enabled') == false) {
            return null;
        }

        if(empty($lang)) {
            $lang = 'und';
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
     * @param string $lang             In which language takes the ids, 'und' if from all
     * @param int    $numberOfIdWanted Size of the array we will return
     *
     * @return array An array of int
     */
    private function _getRandomsToCached($lang, $numberOfIdWanted) {
        $index = $lang == 'und' ?
                 array('und_index') :
                 array($lang . '_main_index', $lang . '_delta_index');
        $sphinx = array(
            'index' => $index,
            'sortMode' => array(SPH_SORT_EXTENDED => "@random"),
            'filter' => array(
                array('user_id', 0, true), // exclude orphans
                array('ucorrectness', 127, true), // exclude unapproved
            ),
            'limit' => $numberOfIdWanted
        );

        $results = $this->find('all', [
            'fields' => ['id'],
            'sphinx' => $sphinx,
            'search' => ''
        ])->toList();

        return Hash::extract($results, '{n}.id');
    }

    /**
     * Returns the fields names typically needed to display a sentence.
     */
    public function fields()
    {
        return array(
            'id',
            'text',
            'lang',
            'user_id',
            'correctness',
            'script',
            'license',
            'based_on_id',
        );
    }

    /**
     * Returns the appropriate value for the 'contain' parameter
     * of a typical ->find('all', ...). It makes it return everything
     * we need to display typical sentence groups.
     */
    public function contain()
    {
        $contain = array(
            'Favorites_users' => array(
                'fields' => array()
            ),
            'Users' => array(
                'fields' => array('id', 'username', 'role', 'level')
            ),
            'SentencesLists' => array(
                'fields' => array('id', 'SentencesSentencesLists.sentence_id')
            ),
            'Transcriptions'   => array(
                'Users' => array('fields' => array('username')),
            ),
            'Translations' => array(
                'IndirectTranslations' => array(
                    'Transcriptions' => array(
                        'Users' => array('fields' => array('username')),
                    ),
                    'Audios' => array(
                        'Users' => array('fields' => array('username')),
                        'fields' => array('user_id', 'external', 'sentence_id'),
                    ),
                ),
                'Transcriptions' => array(
                    'Users' => array('fields' => array('username')),
                ),
                'Audios' => array(
                    'Users' => array('fields' => array('username')),
                    'fields' => array('user_id', 'external', 'sentence_id'),
                ),
            ),
            'Audios' => array(
                'Users' => array('fields' => array(
                    'username',
                    'audio_license',
                    'audio_attribution_url',
                )),
                'fields' => array('user_id', 'external', 'sentence_id'),
            ),
        );

        return $contain;
    }

    /**
     * Returns the appropriate value for the 'contain' parameter
     * for the most basic display of the sentence groups.
     */
    public function minimalContain() {
        $contain = array(
            'Users' => array(
                'fields' => array('id', 'username', 'role', 'level')
            ),
            'Translations' => array(
                'IndirectTranslations' => array(),
            ),
        );

        return $contain;
    }

    /**
     * Returns the appropriate value for the 'contain' parameter
     * of typical a pagination of sentences.
     */
    public function paginateContain()
    {
        if (CurrentUser::isMember()) {
            $params = $this->contain();
        } else {
            $params = $this->minimalContain();
        }
        return $params;
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
     * Get all the informations needed to display a sentences in show section.
     *
     * @param int $id Id of the sentence asked.
     *
     * @return array Information about the sentence.
     */
    public function getSentenceWithId($id)
    {
        $result = $this->find('filteredTranslations')
            ->where(['Sentences.id' => $id])
            ->contain($this->contain())
            ->select($this->fields())
            ->first();

        if ($result == null) {
            return;
        }

        return $result;
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
     * @return boolean
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
            $sentenceId
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
     * @param string $text        The text of the sentence.
     * @param string $lang        The lang of the sentence.
     * @param int    $userId      The id of the user who added this sentence.
     * @param int    $correctness Correctness level of sentence.
     * @param in     $basedOnId   The ID of the sentence this sentence is translated from,
     *                            or 0 if it's an original sentence, or null if unknown.
     * @param string $license     The license of the sentence.
     *
     * @return bool
     */
    public function saveNewSentence($text, $lang, $userId, $correctness = 0, $basedOnId = 0, $license = null)
    {
        $hash = $this->makeHash($lang, $text);
        $hash = $this->padHashBinary($hash);

        $sentences = $this->findAllByHash($hash)->toList();
        foreach ($sentences as $sentence) {
            if ($this->confirmDuplicate($text, $lang, $sentence)) {
                $sentence->isDuplicate = true;
                return $sentence;
            }
        }

        $data['text'] = $text;
        $data['user_id'] = $userId;
        $data['correctness'] = $correctness;
        $data['hash'] = $hash;
        $data['license'] = $license;
        $data['based_on_id'] = $basedOnId;

        if (!empty($lang)) {
            $data['lang'] = $lang;
        }

        $sentence = $this->newEntity($data);

        return $this->save($sentence);
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
     * @return bool
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
     * @return bool
     */
    public function setOwner($sentenceId, $userId, $currentUserRole)
    {
        $sentence = $this->get($sentenceId, ['fields' => ['id', 'user_id']]);
        $currentOwner = $this->getOwnerInfoOfSentence($sentenceId);
        $ownerId = $currentOwner['id'];
        $ownerRole = $currentOwner['role'];

        $isOwnerInactive = in_array($ownerRole, [User::ROLE_SPAMMER, User::ROLE_INACTIVE]);
        $isCurrentUserTrusted = in_array($currentUserRole, User::ROLE_ADV_CONTRIBUTOR_OR_HIGHER);
        $isAdoptable = $ownerId == 0 || ($isOwnerInactive && $isCurrentUserTrusted);

        if ($isAdoptable) {
            $sentence->user_id = $userId;
            $this->save($sentence);
            return true;
        }
        return false;
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

        if ($ownerId == $currentUserId || CurrentUser::isModerator()) {

            // Making sure the language is not saved as an empty string but as NULL.
            if ($newLang == '' ) {
                $newLang = null;
            }

            $sentence->lang = $newLang;
            $result = $this->save($sentence);

            $this->Links->updateLanguage($sentenceId, $newLang);
            $this->Contributions->updateLanguage($sentenceId, $newLang);
            $this->Languages->incrementCountForLanguage($newLang);
            $this->Languages->decrementCountForLanguage($prevLang);

            // In the previous language, add the sentence to the kill-list
            // so that it doesn't appear in results any more.
            $reindexFlag = $this->ReindexFlags->newEntity([
                'sentence_id' => $sentenceId,
                'lang' => $prevLang,
            ]);
            $this->ReindexFlags->save($reindexFlag);

            return $result->lang;
        }

        return $prevLang;
    }


    /**
     * Return text of a sentence for given id.
     *
     * @param int $sentenceId Id of the sentence
     *
     * @return void
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
     * @param array $data We're taking the data from the AJAX request. It has an
     *                    'id' and a 'value', but the 'id' actually contains the
     *                    language followed by the id, separated by an underscore.
     *
     * @return array
     */
    public function editSentence($data)
    {
        $text = $this->_getEditFormText($data);
        $idLangArray = $this->_getEditFormIdLang($data);
        if (!$text || !$idLangArray) {
            return array();
        }

        // Set $id and $lang
        extract($idLangArray);
        try {
            $sentence = $this->get($id);
        } catch (RecordNotFoundException $e) {
            return array();
        }
        
        if ($this->_cantEditSentence($sentence)) {
            return $sentence;
        }
        
        if ($this->hasAudio($id)) {
            return $sentence;
        }

        $hash = $this->makeHash($lang, $text);
        $hash = $this->padHashBinary($hash);
        $this->patchEntity($sentence, [
            'text' => $text,
            'hash' => $hash,
            'lang' => $lang
        ]);

        $sentenceSaved = $this->save($sentence);
        if ($sentenceSaved) {
            $this->UsersSentences->makeDirty($id);
        }

        return $sentenceSaved;
    }

    /**
     * Get text from edit form params.
     *
     * @param  array $params Form parameters.
     *
     * @return string|boolean
     */
    private function _getEditFormText($params)
    {
        if (isset($params['value'])) {
            return trim($params['value']);
        }

        return false;
    }

    /**
     * Get id and lang from edit form params.
     *
     * @param  array $params Form parameters.
     *
     * @return array|boolean
     */
    private function _getEditFormIdLang($params)
    {
        if (isset($params['id'])) {
            // ['form']['id'] contains both the sentence id and its language.
            // Do not sanitize it directly.
            $sentenceId = $params['id'];

            $dirtyArray = explode("_", $sentenceId);

            return [
                'id' => (int)$dirtyArray[1],
                'lang' => $dirtyArray[0]
            ];
        }

        return false;
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

    private function orderby($expr, $order)
    {
        return $expr . ($order ? ' ASC' : ' DESC');
    }

    public function sphinxOptions($query, $from, $sort, $sort_reverse)
    {
        if ($sort == 'random') {
            $sortOrder = '@random';
        } elseif (empty($query)) {
            // When the query is empty, Sphinx does not perform any
            // ranking, so we need to rely on ordering instead
            if ($sort == 'created' || $sort == 'modified') {
                $sortOrder = $this->orderby($sort, $sort_reverse);
            } else {
                $sortOrder = $this->orderby('text_len', !$sort_reverse);
            }
        } else {
            // When there are keywords, Sphinx will perform ranking
            $sortOrder = $this->orderby('@rank', $sort_reverse);
            if ($sort == 'words') {
                $rankingExpr = '-text_len';
            } elseif ($sort == 'relevance') {
                $rankingExpr = '-text_len+top(lcs+exact_order*100)*100';
            } elseif ($sort == 'created' || $sort == 'modified') {
                $rankingExpr = $sort;
            }
        }
        $index = $from == 'und' ?
                 array('und_index') :
                 array($from . '_main_index', $from . '_delta_index');
        $sphinx = array(
            'index' => $index,
            'matchMode' => SPH_MATCH_EXTENDED2,
            'sortMode' => array(SPH_SORT_EXTENDED => $sortOrder),
        );
        if (isset($rankingExpr)) {
            $sphinx['rankingMode'] = array(SPH_RANK_EXPR => $rankingExpr);
        };

        return $sphinx;
    }
}
