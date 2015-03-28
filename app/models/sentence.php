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

class Sentence extends AppModel
{

    public $name = 'Sentence';
    public $actsAs = array("Containable", "Autotranscriptable");
    public static $romanji = array('furigana' => 1, 'mix' => 2, 'romanji' => 3);

    const MIN_CORRECTNESS = -1;
    const MAX_CORRECTNESS = 0;
    
    public $languages = array(
        'ara', 'bul', 'deu', 'ell', 'eng',
        'epo', 'spa', 'fra', 'heb', 'ind',
        'jpn', 'kor', 'nld', 'por', 'rus',
        'vie', 'cmn', 'ces', 'fin', 'ita',
        'tur', 'ukr', 'wuu', 'swe', 'zsm',
        'nob', 'est', 'kat', 'pol', 'swh',
        'lat', 'arz', 'bel', 'hun', 'isl',
        'sqi', 'yue', 'afr', 'fao', 'fry',
        'uig', 'uzb', 'bre', 'ron', 'non',
        'srp', 'yid', 'tat', 'pes', 'nan',
        'eus', 'slk', 'dan', 'hye', 'acm',
        'san', 'urd', 'hin', 'ben', 'cycl',
        'cat', 'kaz', 'lvs', 'hrv', 'bos',
        'orv', 'cha', 'tgl', 'que', 'mon',
        'lit', 'glg', 'gle', 'ina', 'jbo',
        'toki', 'ain', 'scn', 'mal', 'nds',
        'tlh', 'slv', 'tha', 'lzh', 'oss',
        'roh', 'vol', 'gla', 'ido', 'ast',
        'ile', 'oci', 'xal', 'ang', 'kur',
        'dsb', 'hsb', 'ksh', 'cym', 'ewe',
        'sjn', 'tel', 'nov', 'tpi', 'qya',
        'mri', 'lld', 'ber', 'xho', 'pnb',
        'mlg', 'grn', 'lad', 'pms', 'avk',
        'mar', 'tgk', 'tpw', 'prg', 'npi',
        'mlt', 'ckt', 'cor', 'aze', 'khm',
        'lao', 'bod', 'hil', 'arq', 'pcd',
        'grc',
        'amh',
        'awa',
        'bho',
        'cbk',
        'enm',
        'frm',
        'hat',
        'jdt',
        'kal',
        'mhr',
        'nah',
        'pdc',
        'sin',
        'tuk',
        'wln',
        'bak',
        'hau',
        'ltz',
        'mgm',
        'som',
        'zul',
        'haw',
        'kir',
        'mkd',
        'mrj',
        'ppl',
        'yor',
        'kin',
        'shs',
        'chv',
        'lkt',
        'ota',
        'sna',
        'mnw',
        'nog',
        'sah',
        'abk',
        'tet',
        'tam',
        'udm',
        'kum',
        'crh',
        'nya',
        'liv',
        'nav',
        'chr',
        'guj', 
        'pan', 
        'kha', 
        'jav', //@lang
        null
    );

    public $validate = array(
        'lang' => array(
            'rule' => array()
            // The rule will be defined in the constructor.
            // I would have declared a const LANGUAGES array
            // to use it here, but apparently you can't declare
            // const arrays in PHP.
        ),
        'text' => array(
            'rule' => array('minLength', '1')
        )
    );

    public $hasMany = array(
        'Link',
        'Contribution',
        'SentenceComment',
        'Favorites_users' => array (
            'classname'  => 'favorites',
            'foreignKey' => 'favorite_id'
        ),
        'SentenceAnnotation'
    );

    public $belongsTo = array(
        'User',
        'Language' => array(
            'classname' => 'Language',
            'foreignKey' => 'lang_id'
        ),
    );

    public $hasAndBelongsToMany = array(
        'Translation' => array(
            'className' => 'Translation',
            'joinTable' => 'sentences_translations',
            'foreignKey' => 'translation_id',
            'associationForeignKey' => 'sentence_id'
        ),
        'InverseTranslation' => array(
            'className' => 'InverseTranslation',
            'joinTable' => 'sentences_translations',
            'foreignKey' => 'sentence_id',
            'associationForeignKey' => 'translation_id'
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
     * The constructor is here only to set the rule for languages.
     *
     * @return void
     */
    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->validate['lang']['rule'] = array('inList', $this->languages);
        if (Configure::read('Search.enabled')) {
            $this->Behaviors->attach('Sphinx');
        }
    }

    private function clean($text)
    {
        $text = trim($text);
        // Strip out any initial byte-order mark that might be present.
        $text = preg_replace("/^\xEF\xBB\xBF/", '', $text);
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

    public function beforeValidate()
    {
        if (isset($this->data['Sentence']['text']))
        {
            $text = $this->data['Sentence']['text'];
            $this->data['Sentence']['text'] = $this->clean($text);
        }
    }

    public function beforeSave()
    {
        if (isset($this->data['Sentence']['lang']))
        {
            $lang = $this->data['Sentence']['lang'];
            $langId = $this->Language->getIdFromLang($lang);
            $this->data['Sentence']['lang_id'] = $langId;
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
    public function afterSave($created)
    {
        $this->logSentenceEdition($created);
        $this->updateTags($created);
    }

    private function logSentenceEdition($created)
    {
        if (isset($this->data['Sentence']['text'])) {
            // --- Logs for sentence ---
            $sentenceLang =  $this->data['Sentence']['lang'];
            $sentenceAction = 'update';
            $sentenceText = $this->data['Sentence']['text'];
            if ($created) {
                $sentenceAction = 'insert';
                $this->Language->incrementCountForLanguage($sentenceLang);
            }

            $this->Contribution->saveSentenceContribution(
                $this->id, $sentenceLang, $sentenceText, $sentenceAction
            );
        }
    }

    private function updateTags($created)
    {
        if (!$created) {
            $OKTagId = $this->Tag->getIdFromName($this->Tag->getOKTagName());
            $this->TagsSentences->removeTagFromSentence($OKTagId, $this->id);
        }
    }

    /**
     * Call after a deletion.
     *
     * @return void
     */
    public function afterDelete()
    {
        $action = 'delete';

        // --- Logs for sentence ---
        $sentenceLang = $this->data['Sentence']['lang'];
        $sentenceId = $this->data['Sentence']['id'];
        $sentenceText = $this->data['Sentence']['text'];
        $this->Contribution->saveSentenceContribution(
            $sentenceId, $sentenceLang, $sentenceText, 'delete'
        );

        // --- Logs for links ---
        foreach ($this->data['Translation'] as $translation) {
            $this->Contribution->saveLinkContribution(
                $sentenceId, $translation['id'], $action
            );
            $this->Contribution->saveLinkContribution(
                $translation['id'], $sentenceId, $action
            );
        }

        // Decrement statistics
        $this->Language->decrementCountForLanguage($sentenceLang);
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

        return $results[0]['Sentence']['id'];
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
        if (empty($arrayIds) || empty($arrayIds[0])) {
            return 1;
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
            return array(1);
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
            'sortMode' => array(SPH_SORT_EXTENDED => "@random")
        );

        $results = $this->find(
            'list',
            array(
                'fields' => array('id'),
                'sphinx' => $sphinx,
                'contain' => array(),
                'search' => '',
                'limit' => 100,
            )
        );

        return array_keys($results);

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
                    'conditions' => array ('Sentence.id' => $id),
                    'contain'  => array (
                        'Favorites_users' => array(
                            'fields' => array()
                        ),
                        'User'            => array(
                            'fields' => array('username')
                        ),
                        'SentencesList'   => array(
                            'fields' => array('id')
                        ),
                    ),
                    'fields' => array(
                        'text',
                        'lang',
                        'user_id',
                        'hasaudio',
                        'correctness'
                    )
            )
        );

        if ($result == null) {
            return;
        }

        return $result;
    }

    /**
     * Get sentences with specified ids as well as their translations.
     *
     * @param array  $ids              Ids of the sentences.
     * @param string $translationsLang Language of the translations.
     *
     * @return array
     */
    public function getSentencesWithIds($ids, $translationsLang = null)
    {
        $translationsConditions = array();
        if ($translationsLang != null) {
            $translationsConditions["Translation.lang"] = $translationsLang;
        }

        $sentences = $this->find(
            'all',
            array(
                "conditions" => array("Sentence.id" => $ids),
                "contain" => array(
                    "Favorites_users" => array(
                        'fields' => array()
                    ),
                    "User" => array(
                        "fields" => array("username")
                    ),
                    "Translation" => array(
                        "fields" => array(
                            "id",
                            "lang",
                            "text",
                            "hasaudio",
                            "correctness"
                        ),
                        "conditions" => $translationsConditions
                    )
                )
            )
        );

        return $results;
    }

    /**
     * Delete the sentence with the given id.
     *
     * @param int $id     Id of the sentence to be deleted.
     * @param int $userId Id of the user who deleted the sentence. Used for logs.
     *
     * @return void
     */
    public function delete($id, $userId)
    {
        $id = Sanitize::paranoid($id);
        // for the logs
        $this->data = $this->find(
            'first',
            array(
                'conditions' => array('Sentence.id' => $id),
                'contain' => array ('Translation', 'User')
            )
        );
        
        $this->data['User']['id'] = $userId;

        $isDeleted = false;
        
        if ($this->data['Sentence']['hasaudio'] == 'no')
        {
            $this->query('DELETE FROM sentences WHERE id='.$id);
            $this->query('DELETE FROM sentences_translations WHERE sentence_id='.$id);
            $this->query('DELETE FROM sentences_translations WHERE translation_id='.$id);
            $isDeleted = true;
        }

        // need to call afterDelete() manually for the logs
        $this->afterDelete();

        return $isDeleted;
    }


    /**
     * Get number of sentences owned by a given user.
     *
     * @param int $userId Id of the user we want number of sentences of
     *
     * @return array TODO should return an int
     */
    public function numberOfSentencesOwnedBy($userId)
    {
        return $this->find(
            'count',
            array(
                'conditions' => array(
                    'Sentence.user_id' => $userId
                ),
                'contain' => array()
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

        return $this->Translation->find($id, $languages);
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
                'contain'=> array()
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

        return $sentence['User']['email'];
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
     * @param int    $sentenceId
     * @param int    $sentenceLang
     * @param string $translationText
     * @param string $translationLang
     * @TODO finish the doc plz
     *
     * @return boolean
     */
    public function saveTranslation(
        $sentenceId, $sentenceLang, $translationText, $translationLang, $translationCorrectness
    ) {
        $userId = CurrentUser::get('id');
        
        // saving translation
        $sentenceSaved = $this->saveNewSentence(
            $translationText,
            $translationLang,
            $userId,
            $translationCorrectness
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
     * @param string $text   The text of the sentence
     * @param string $lang   The lang of the sentence
     * @param int    $userId The id of the user who added this sentence
     *
     * @return bool
     */
    public function saveNewSentence($text, $lang, $userId, $correctness = 0)
    {
        if ($lang == "") {
            $lang = null;
        }

        //if ($userId == 1314 || $userId == 6070) { return; }
        $data['Sentence']['id'] = null;
        $data['Sentence']['text'] = trim($text);
        $data['Sentence']['lang'] = $lang;
        $data['Sentence']['user_id'] = $userId;
        $data['Sentence']['correctness'] = $correctness;
        $sentenceSaved = $this->save($data);

        return $sentenceSaved;
    }

    /**
     * Add a new sentence and a translation in the database
     *
     * @param string $sentenceText    The text of the sentence
     * @param string $sentenceLang    The lang of the sentence
     * @param string $translationText The text of the translation
     * @param string $translationLang The lang of the translation
     * @param int    $userId          The id of the user who added them
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
        $correctness = $this->User->getLevelOfUser($userId);
        
        // saving sentence
        $sentenceSaved = $this->saveNewSentence(
            $sentenceText,
            $sentenceLang,
            $userId,
            $correctness
        );
        $sentenceId = $this->id;
        // saving translation
        $translationSaved = $this->saveNewSentence(
            $translationText,
            $translationLang,
            $userId,
            $correctness
        );

        $translationId = $this->id;
        // saving links
        if ($sentenceSaved && $translationSaved) {
            $this->Link->add($sentenceId, $translationId, 
                             $sentenceLang, $translationLang);
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
     * @param int $sentenceId Id of the sentence.
     * @param int $userId     Id of the user.
     *
     * @return bool
     */
    public function setOwner($sentenceId, $userId)
    {
        $this->id = $sentenceId;
        $currentOwner = $this->getOwnerIdOfSentence($sentenceId);
        if (empty($currentOwner)) {
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
        $currentOwner = $this->getOwnerIdOfSentence($sentenceId);
        if ($currentOwner == $userId) {
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
     * @return int
     */
    public function getOwnerIdOfSentence($sentenceId)
    {
        $sentence = $this->find(
            'first',
            array(
                'conditions' => array(
                    'id' => $sentenceId
                ),
                'fields' => array('user_id'),
                'contain' => array()
            )
        );

        return $sentence['Sentence']['user_id'];
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
            'recursive' => -1,
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
            $newLangId = $this->Language->getIdFromLang($newLang);

            $data['Sentence'] = array(
                'lang' => $newLang,
                'lang_id' => $newLangId
            );
            $this->id = $sentenceId;
            $this->save($data);

            $this->Contribution->updateLanguage($sentenceId, $newLang);
            $this->Language->incrementCountForLanguage($newLang);
            $this->Language->decrementCountForLanguage($prevLang);
            
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
        $numSentences = $this->find(
            'count',
            array(
                'contain' => array()
            )
        );

        return $numSentences;
    }


    /**
     * Return number of sentencse with audio.
     *
     * @return array
     */
    public function getTotalNumberOfSentencesWithAudio()
    {
        $results = $this->query(
            "SELECT lang, COUNT(*) total FROM sentences AS `Sentence`
              WHERE hasaudio IN ('shtooka', 'from_users')
              GROUP BY lang ORDER BY total DESC;"
        );

        $stats = array();
        foreach ($results as $result) {
            $stats[] = array(
                'lang' => $result['Sentence']['lang'],
                'total' => $result[0]['total']
            );
        }

        return $stats;
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
                'contain' => array()
            )
        );

        return $result['Sentence']['text'];
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
                'contain' => array()
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

    public function getSentencesLang($sentencesIds, $langId = false) {
        $field = $langId ? 'lang_id' : 'lang';
        $result = $this->find('all', array(
            'fields' => array($field, 'id'),
            'conditions' => array('Sentence.id' => $sentencesIds),
            'recursive' => -1
        ));
        return Set::combine($result, '{n}.Sentence.id', '{n}.Sentence.'.$field);
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

    public function editAudio($sentenceId, $hasaudio)
    {
        $this->id = $sentenceId;
        return $this->saveField('hasaudio', $hasaudio);
    }
}
?>
