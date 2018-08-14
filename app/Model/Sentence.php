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
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   Allan Simon <allan.simon@supinfo.com>
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Model Class which represent sentences
 *
 * @category PHP
 * @package  Tatoeba
 * @author   Allan Simon <allan.simon@supinfo.com>
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
*/

App::import('Model', 'CurrentUser');
App::import('Sanitize');
App::import('Lib', 'LanguagesLib');

class Sentence extends AppModel
{
    public $name = 'Sentence';
    public $actsAs = array('Containable', 'Transcriptable', 'Hashable');

    const MIN_CORRECTNESS = -1;
    const MAX_CORRECTNESS = 0;

    public $validate = array(
        'lang' => array(
            'rule' => array(),
            'allowEmpty' => true,
            // The rule will be defined in beforeValidate().
        ),
        'text' => array(
            'rule' => array('minLength', '1')
        ),
    );

    public $hasMany = array(
        'Audio',
        'Contribution',
        'SentenceComment',
        'Favorites_users' => array (
            'classname'  => 'favorites',
            'foreignKey' => 'favorite_id'
        ),
        'SentenceAnnotation',
        'Transcription',
        'Translation' => array(
            'className' => 'Translation',
            'foreignKey' => 'sentence_id',
        ),
        'ReindexFlag',
    );

    public $belongsTo = array(
        'User',
        'Language' => array(
            'classname' => 'Language',
            /* Our foreign key is 'lang' but it doesn't correspond
               to the primary key of the `languages` table.
               It is just set here so that we can access the Language
               model while true linking doesn't work. */
            'foreignKey' => 'lang',
        ),
    );

    public $hasAndBelongsToMany = array(
        'Link' => array(
            'className' => 'Link',
            'joinTable' => 'sentences_translations',
            'foreignKey' => 'translation_id',
            'associationForeignKey' => 'sentence_id'
        ),
        'SentencesList',
        'Tag' => array(
            'className' => 'Tag',
            'joinTable' => 'tags_sentences',
            'foreignKey' => 'sentence_id',
            'associationForeignKey' => 'tag_id',
            'with' => 'TagsSentences',
        ),
    );


    /**
     * The constructor is here only to conditionally attach Sphinx.
     *
     * @return void
     */
    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);

        if (!Configure::read('AutoTranscriptions.enabled')) {
            $this->Behaviors->disable('Transcriptable');
        }
        if (Configure::read('Search.enabled')) {
            $this->Behaviors->attach('Sphinx');
        }

        $this->findMethods['random'] = true;

        $this->validate['license'] = array(
            'validLicense' => array(
                'rule' => array('inList', array(
                    'CC0 1.0',
                    'CC BY 2.0 FR',
                )),
            ),
            'canSwitchLicense' => array(
                'rule' => array('canSwitchLicense'),
                'on' => 'update',
            ),
        );

        $this->linkWithTranslationModel();
    }

    /**
     * Links the Sentence and Translation models with restrictions
     * on the language of translated sentences according to the
     * profile setting 'lang'.
     */
    private function linkWithTranslationModel() {
        $userLangs = CurrentUser::getLanguages();
        $conditions = $userLangs ?
                      array('Translation.lang' => $userLangs) :
                      array();
        $this->linkTranslationModel($conditions);
    }

    public function linkTranslationModel($conditions = array())
    {
        $this->hasMany['Translation']['finderQuery']
            = $this->Translation->hasManyTranslationsLikeSqlQuery($conditions);
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

    public function beforeValidate($options = array())
    {
        // Set this array as late as possible, because languagesInTatoeba()
        // makes uses of __(), which relies on 'Config.language', which is set
        // quite late, in AppController::beforeFilter().
        if (!$this->validate['lang']['rule']) {
            $this->validate['lang']['rule'] = array('inList',
                array_keys(LanguagesLib::languagesInTatoeba())
            );
        }
        if (isset($this->data['Sentence']['text']))
        {
            $text = $this->data['Sentence']['text'];
            $this->data['Sentence']['text'] = $this->clean($text);
        }
        if (!isset($this->data['Sentence']['id'])) { // creating a new sentence
            if (!isset($this->data['Sentence']['license'])) {
                if (isset($this->data['Sentence']['user_id'])) {
                    $userId = $this->data['Sentence']['user_id'];
                    $user = $this->User->findById($userId, 'settings');
                    if ($user) {
                        $userDefaultLicense = $user['User']['settings']['default_license'];
                        $this->data['Sentence']['license'] = $userDefaultLicense;
                    }
                }
            }
        }
    }

    public function canSwitchLicense() {
        $sentenceId = $this->id;
        $sentence = $this->findById($sentenceId, array('based_on_id'));
        $isOriginal = !is_null($sentence['Sentence']['based_on_id']) && $sentence['Sentence']['based_on_id'] == 0;
        if (!$isOriginal) {
            /* @translators: This string will be preceded by "Unable to
               change the license to “{newLicense}” because:" */
            $this->invalidate('license', __('The sentence needs to be original (not initially derived from translation).'));
        }

        return true;
    }

    /**
     * Called after a sentence is saved.
     *
     * @param bool $created true if a new line has been created.
     *                      false if a line has been updated.
     *
     * @return void
     */
    public function afterSave($created, $options = array())
    {
        $this->logSentenceEdition($created);
        $this->updateTags($created);
        if (isset($this->data['Sentence']['modified'])) {
            $this->needsReindex($this->id);
        }
        $transIndexedAttr = array('lang', 'user_id');
        $transNeedsReindex = array_intersect_key(
            $this->data['Sentence'],
            array_flip($transIndexedAttr)
        );
        if ($transNeedsReindex) {
            $this->flagTranslationsToReindex($this->id);
        }
    }

    public function flagSentenceAndTranslationsToReindex($id) {
        $this->needsReindex($id);
        $this->flagTranslationsToReindex($id);
    }

    private function flagTranslationsToReindex($id)
    {
        $transIds = $this->Link->findDirectAndIndirectTranslationsIds($id);
        $this->needsReindex($transIds);
    }

    private function logSentenceEdition($created)
    {
        if (isset($this->data['Sentence']['text'])) {
            // --- Logs for sentence ---
            $sentenceLang = null;
            if (isset($this->data['Sentence']['lang'])) {
                $sentenceLang = $this->data['Sentence']['lang'];
            }
            $sentenceScript = null;
            if (isset($this->data['Sentence']['script'])) {
                $sentenceScript = $this->data['Sentence']['script'];
            }
            $sentenceAction = 'update';
            $sentenceText = $this->data['Sentence']['text'];
            if ($created) {
                $sentenceAction = 'insert';
                $this->Language->incrementCountForLanguage($sentenceLang);
            }

            $this->Contribution->saveSentenceContribution(
                $this->id,
                $sentenceLang,
                $sentenceScript,
                $sentenceText,
                $sentenceAction
            );
        }
    }

    private function updateTags($created)
    {
        $edited = array_key_exists('text', $this->data[$this->alias]);
        if (!$created && $edited) {
            $OKTagId = $this->Tag->getIdFromName($this->Tag->getOKTagName());
            $this->TagsSentences->removeTagFromSentence($OKTagId, $this->id);
        }
    }

    public function needsReindex($ids)
    {
        $sentences = $this->find('all', array(
            'conditions' => array('id' => $ids),
            'fields' => array('id as sentence_id', 'lang'),
        ));
        foreach ($sentences as &$rec) {
            unset($rec['Sentence']['script']); // TODO get this removed
            $rec = $rec['Sentence'];
        }
        $this->ReindexFlag->saveAll($sentences);
    }

    /**
     * Called before every deletion operation.
     *
     * @param boolean $cascade If true records that depend on this record will also be deleted
     * @return boolean True if the operation should continue, false if it should abort
     */
    public function beforeDelete($cascade = true) {
        // Retrieve data before deleting it, so that we can log things
        // in afterDelete()
        $this->data = $this->find(
            'first',
            array(
                'conditions' => array('Sentence.id' => $this->id),
                'contain' => array('Link', 'User', 'Audio')
            )
        );

        if (count($this->data['Audio']) > 0) {
            return false;
        }

        $this->data['ReindexFlag'] =
            $this->Link->findDirectAndIndirectTranslationsIds($this->id);

        return true;
    }

    /**
     * Call after a deletion.
     *
     * @return void
     */
    public function afterDelete()
    {
        // --- Logs for sentence ---
        $sentenceLang = $this->data['Sentence']['lang'];
        $sentenceScript = $this->data['Sentence']['script'];
        $sentenceId = $this->data['Sentence']['id'];
        $sentenceText = $this->data['Sentence']['text'];
        $this->Contribution->saveSentenceContribution(
            $sentenceId,
            $sentenceLang,
            $sentenceScript,
            $sentenceText,
            'delete'
        );

        // --- Logs for links ---
        $action = 'delete';
        foreach ($this->data['Link'] as $translation) {
            $this->Contribution->saveLinkContribution(
                $sentenceId, $translation['id'], $action
            );
            $this->Contribution->saveLinkContribution(
                $translation['id'], $sentenceId, $action
            );
        }

        // Reindex translations
        $this->needsReindex($this->data['ReindexFlag']);

        // Add the sentence to the kill-list
        // so that it won't appear in search results anymore
        $this->ReindexFlag->create();
        $this->ReindexFlag->save(array(
            'sentence_id' => $sentenceId,
            'lang' => $this->data['Sentence']['lang'],
        ));

        // Remove links
        $conditions = array(
            'Link.sentence_id' => $sentenceId,
            // Note that deleting Link.translation_id == $sentenceId
            // is unnecessary because CakePHP already deleted them
            // during delete() thanks to the HABTM relation
        );
        $this->Link->deleteAll($conditions, false);

        // Decrement statistics
        $this->Language->decrementCountForLanguage($sentenceLang);
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

    /**
     * Search one random chinese/japanese sentence containing $sinogram.
     *
     * @param string $sinogram Sinogram to search an example sentence containing it.
     *
     * @return int The id of this sentence.
     */
    public function searchOneExampleSentenceWithSinogram($sinogram)
    {
        $results = $this->query(
            "SELECT Sentence.id  FROM sentences AS Sentence
                JOIN ( SELECT (RAND() *(SELECT MAX(id) FROM sentences)) AS id) AS r2
                WHERE Sentence.id >= r2.id
                    AND Sentence.lang IN ( 'jpn','cmn','wuu')
                    AND Sentence.text LIKE ('%$sinogram%')
                ORDER BY Sentence.id ASC LIMIT 1
            "
        );

        return !empty($results) ? $results[0]['Sentence']['id'] : null;
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
     * Get the highest id for sentences.
     *
     * @return int The highest sentence id.
     */
    public function getMaxId()
    {
        $resultMax = $this->query('SELECT MAX(id) FROM sentences');
        return $resultMax[0][0]['MAX(id)'];
    }

    /**
     * Get the id of a random sentence, from a particular language if $lang is set.
     *
     * @param string $lang Restrict random id from the specified code lang.
     *
     * @return int A random id.
     */
    public function getRandomId($lang = 'und')
    {
        $arrayIds = $this->getSeveralRandomIds($lang, 1);
        if (is_bool($arrayIds)) {
            return $arrayIds;
        }

        return  $arrayIds[0];//$results['Sentence']['id'];
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
        if (!is_array($arrayRandom)) {
            $arrayRandom = $this->_getRandomsToCached($lang, 3);
        }

        if(is_array($arrayRandom)){
            for ($i = 0; $i < $numberOfIdWanted; $i++) {

                $id = array_pop($arrayRandom);
                // if we have take all the cached ids, then we request a new bunch
                if ($id === NULL) {
                    $arrayRandom = $this->_getRandomsToCached($lang, 5);
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
        );

        $results = $this->find(
            'list',
            array(
                'fields' => array('id'),
                'sphinx' => $sphinx,
                'search' => '',
                'limit' => 100,
            )
        );

        if(is_array($results)){
            return array_keys($results);
        }

        return 1;
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
        return array(
            'Favorites_users' => array(
                'fields' => array()
            ),
            'User' => array(
                'fields' => array('id', 'username', 'group_id', 'level')
            ),
            'SentencesList' => array(
                'fields' => array('id')
            ),
            'Transcription'   => array(
                'User' => array('fields' => array('username')),
            ),
            'Translation' => array(
                'Transcription' => array(
                    'User' => array('fields' => array('username')),
                ),
                'Audio' => array(
                    'User' => array('fields' => array('username')),
                    'fields' => array('user_id', 'external'),
                ),
            ),
            'Audio' => array(
                'User' => array('fields' => array(
                    'username',
                    'audio_license',
                    'audio_attribution_url',
                )),
                'fields' => array('user_id', 'external'),
            ),
        );
    }

    /**
     * Returns the appropriate value for the 'contain' parameter
     * for the most basic display of the sentence groups.
     */
    public function minimalContain() {
        return array(
            'User' => array(
                'fields' => array('id', 'username', 'group_id', 'level')
            ),
            'Translation' => array(),
        );
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
        $params['fields'] = $this->fields();
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
        $result = $this->find(
            'first',
            array(
                'conditions' => array('Sentence.id' => $id),
                'contain' => $this->contain(),
                'fields' => $this->fields(),
            )
        );

        if ($result == null) {
            return;
        } else if (CurrentUser::getSetting('native_indicator')) {
            $UsersLanguages = ClassRegistry::init('UsersLanguages');
            $isUserLevelNative = $UsersLanguages->isUserNative(
                $result['User']['id'], $result['Sentence']['lang']
            );
            $isUserReliable = $result['User']['group_id'] != 6
                && $result['User']['level'] > -1;
            $result['User']['is_native'] = $isUserLevelNative && $isUserReliable;
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
        return $this->find(
            'count',
            array(
                'conditions' => array(
                    'Sentence.user_id' => $userId
                ),
            )
        );
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
        $id = Sanitize::paranoid($id);
        $lang = Sanitize::paranoid($lang);
        if ( ! is_numeric($id) ) {
            return array();
        }

        if (!empty($lang) && $lang != "und") {
            $languages = array($lang);
        } else {
            $languages = CurrentUser::getLanguages();
        }

        return $this->Translation->getTranslationsOf($id, $languages);
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
        $conditions = array();
        if (!empty($lang) && $lang != 'und') {
            $conditions["Sentence.lang"] = $lang;
        }

        $this->id = $sourceId;
        $neighborsCake = $this->find(
            'neighbors',
            array(
                'fields' => array("id"),
                'conditions' => $conditions,
            )
        );

        $neighbors = array(
            "prev" => $neighborsCake['prev']['Sentence']['id'],
            "next" => $neighborsCake['next']['Sentence']['id'],
        );

        return $neighbors;
    }

    /**
     * Return email of owner of the sentence.
     *
     * @param int $sentenceId Id of the sentence.
     *
     * @return array
     */
    public function getEmailFromSentence($sentenceId)
    {
        $sentence = $this->find(
            'first',
            array(
                'fields' => array(),
                'conditions' => array('Sentence.id' => $sentenceId),
                'contain' => array(
                    'User' => array(
                        'fields' => array('email'),
                        'conditions' => array('send_notifications' => 1)
                    )
                )
            )
        );

        return $sentence ? $sentence['User']['email'] : null;
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
            $this->Link->add($sentenceId, $this->id, $sentenceLang, $translationLang);
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
        $text = $this->clean($text);

        $hash = $this->makeHash($lang, $text);

        $sentences = $this->findAllByBinary($hash, 'hash');

        foreach ($sentences as $sentence) {
            if ($this->confirmDuplicate($text, $lang, $sentence['Sentence'])) {
                $this->id = $sentence['Sentence']['id'];

                return $this->duplicate = true;
            }
        }

        $this->create();

        $this->duplicate = false;

        $data['Sentence']['text'] = $text;
        $data['Sentence']['user_id'] = $userId;
        $data['Sentence']['correctness'] = $correctness;
        $data['Sentence']['hash'] = $hash;
        $data['Sentence']['license'] = $license;
        $data['Sentence']['based_on_id'] = $basedOnId;

        if (!empty($lang)) {
            $data['Sentence']['lang'] = $lang;
        }

        return $this->save($data);
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
        return $this->Contribution->getContributionsRelatedToSentence($id);

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
        return $this->SentenceComment->getCommentsForSentence($id);
    }


    /**
     * Set owner for a sentence.
     *
     * @param int $sentenceId         Id of the sentence.
     * @param int $userId             Id of the user.
     * @param int $currentUserGroupId Group id of the user.
     *
     * @return bool
     */
    public function setOwner($sentenceId, $userId, $currentUserGroupId)
    {
        $this->id = $sentenceId;

        $currentOwner = $this->getOwnerInfoOfSentence($sentenceId);
        $ownerId = $currentOwner['id'];
        $ownerGroupId = $currentOwner['group_id'];

        $isAdoptable = $ownerId == 0 || ($ownerGroupId > 4
                && in_array($currentUserGroupId, range(1, 3)));

        if ($isAdoptable) {
            $this->saveField('user_id', $userId);
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
        $this->id = $sentenceId;
        $currentOwner = $this->getOwnerInfoOfSentence($sentenceId);
        $ownerId = $currentOwner['id'];
        if ($ownerId == $userId) {
            $this->saveField('user_id', null);
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
        $sentence = $this->find(
            'first',
            array(
                'conditions' => array(
                    'Sentence.id' => $sentenceId
                ),
                'fields' => array('user_id'),
                'contain' => array('User' => array('group_id'))
            )
        );

        return $sentence['User'];
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
        $sentence = $this->find('first', array(
            'conditions' => array('id' => $sentenceId),
            'fields' => array('lang', 'user_id'),
        ));
        if (!$sentence) {
            return false;
        }
        $ownerId = $sentence['Sentence']['user_id'];
        $prevLang = $sentence['Sentence']['lang'];
        $currentUserId = CurrentUser::get('id');

        if ($ownerId == $currentUserId || CurrentUser::isModerator()) {

            // Making sure the language is not saved as an empty string but as NULL.
            if ($newLang == "" ) {
                $newLang = null;
            }

            $data['Sentence'] = array(
                'lang' => $newLang,
            );
            $this->id = $sentenceId;
            $this->save($data);

            $this->Link->updateLanguage($sentenceId, $newLang);
            $this->Contribution->updateLanguage($sentenceId, $newLang);
            $this->Language->incrementCountForLanguage($newLang);
            $this->Language->decrementCountForLanguage($prevLang);

            // In the previous language, add the sentence to the kill-list
            // so that it doesn't appear in results any more.
            $this->ReindexFlag->create();
            $this->ReindexFlag->save(array(
                'sentence_id' => $sentenceId,
                'lang' => $prevLang,
            ));

            return $newLang;
        }

        return $prevLang;
    }


    /**
     * Get total number of sentences.
     *
     * @return int
     */
    public function getTotalNumberOfSentences()
    {
        return $this->find('count');
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
        $result = $this->find(
            'first',
            array(
                'fields' => array('text'),
                'conditions' => array('id' => $sentenceId),
            )
        );

        return !empty($result) ? $result['Sentence']['text'] : "";
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
        $result = $this->find(
            'first',
            array(
                'fields' => array('lang'),
                'conditions' => array('id' => $sentenceId),
            )
        );

        return $result['Sentence']['lang'];
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
        $this->id = $sentenceId;
        return $this->saveField('correctness', $correctness);
    }

    public function getSentencesLang($sentencesIds) {
        $result = $this->find('all', array(
            'fields' => array('lang', 'id'),
            'conditions' => array('Sentence.id' => $sentencesIds),
        ));
        return Set::combine($result, '{n}.Sentence.id', '{n}.Sentence.lang');
    }

    public function sphinxAttributesChanged(&$attributes, &$values, &$isMVA) {
        $sentenceId = $this->id;
        $values[$sentenceId] = array();
        if (array_key_exists('user_id', $this->data['Sentence'])) {
            $attributes[] = 'user_id';
            $sentenceOwner = $this->data['Sentence']['user_id'];
            $values[$sentenceId][] = $sentenceOwner;
        }
        if (array_key_exists('correctness', $this->data['Sentence'])) {
            $attributes[] = 'ucorrectness';
            $sentenceUCorrectness = $this->data['Sentence']['correctness'] + 128;
            $values[$sentenceId][] = $sentenceUCorrectness;
        }
        if (count($values[$sentenceId]) == 0)
            unset($values[$sentenceId]);
    }

    /**
     * Edit the sentence.
     *
     * @param  int $id      Sentence id.
     * @param  string $text New sentence text.
     * @param  string $lang New sentnece lang.
     *
     * @return boolean
     */
    public function editSentence($id, $text, $lang)
    {
        $this->id = $id;

        if ($this->hasAudio($id)) {
            return false;
        }

        $hash = $this->makeHash($lang, $text);

        $data['Sentence']['text'] = $text;
        $data['Sentence']['hash'] = $hash;

        if (!empty($lang)) {
            $data['Sentence']['lang'] = $lang;
        }

        return $this->save($data);
    }

    /**
     * Return true if sentence has audio.
     *
     * @return boolean
     */
    public function hasAudio($id)
    {
        return $this->Audio->findBySentenceId($id, 'sentence_id');
    }
}
