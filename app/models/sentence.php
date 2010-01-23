<?php
/**
    Tatoeba Project, free collaborativ creation of languages corpuses project
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


App::import('Core', 'Sanitize');

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

class Sentence extends AppModel
{

    var $name = 'Sentence';
    var $actsAs = array("Containable");

    // This is not much in use. Should probably remove it someday
    const MAX_CORRECTNESS = 6; 
    
    var $languages = array(
        'ara' ,'bul' ,'deu' ,'ell' ,'eng',
        'epo' ,'spa' ,'fra' ,'heb' ,'ind',
        'jpn' ,'kor' ,'nld' ,'por' ,'rus',
        'vie' ,'cmn' ,'ces' ,'fin' ,'ita',
        'tur' ,'ukr' ,'wuu' ,'swe' ,'zsm',
        'nob' ,'est' ,'kat' ,'pol' ,null
        );    
    var $validate = array(
        'lang' => array(
            'rule' => array()     
            // The rule will be defined in the constructor. 
            // I would have declared a const LANGUAGES array 
            // to use it here, but apprently you can't declare 
            // const arrays in PHP.
        ),
        'text' => array(
            'rule' => array('minLength', '1')
        )
    );    

    var $hasMany = array('Contribution', 'SentenceComment', 
            'Favorites_users' => array ( 
                    'classname'  => 'favorites',
                    'foreignKey' => 'favorite_id'  )
             );
    
    var $belongsTo = array('User');
    
    var $hasAndBelongsToMany = array(
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
        'SentencesList'
    );
    
    /**
     * The constructor is here only to set the rule for languages.
     */
    function __construct() 
    {
        parent::__construct();
        $this->validate['lang']['rule'] = array('inList', $this->languages);
    }

    /**
     * called after a sentence is saved
     * 
     * @param bool $created true if a new line has been created
     *                      false if a line has been updated
     * 
     * @return void
     */

    function afterSave($created)
    {
        if (isset($this->data['Sentence']['text'])) {
            $whoWhenWhere = array(
                  'user_id' => $this->data['Sentence']['user_id']
                , 'datetime' => date("Y-m-d H:i:s")
                , 'ip' => $_SERVER['REMOTE_ADDR']
            );
            
            $data['Contribution'] = $whoWhenWhere;
            $data['Contribution']['sentence_id'] = $this->id;
            $data['Contribution']['sentence_lang'] = $this->data['Sentence']['lang'];
            $data['Contribution']['text'] = $this->data['Sentence']['text'];
            $data['Contribution']['type'] = 'sentence';

            
            if ($created) {
                $data['Contribution']['action'] = 'insert';
                // increase stats
                $this->incrementStatistics($this->data['Sentence']['lang']);
                

                if (isset($this->data['Translation'])) {
                    // Translation logs
                    $data2['Contribution'] = $whoWhenWhere;
                    $data2['Contribution']['sentence_id'] = $this->data['Translation']['Translation'][0];
                    $data2['Contribution']['sentence_lang'] = $this->data['Sentence']['sentence_lang'];
                    $data2['Contribution']['translation_id'] = $this->id;
                    $data2['Contribution']['translation_lang'] = $this->data['Sentence']['lang'];
                    $data2['Contribution']['action'] = 'insert';
                    $data2['Contribution']['type'] = 'link';
                    $contributions[] = $data2;
                }
                if (isset($this->data['InverseTranslation'])) {
                    // Inverse translation logs
                    $data2['Contribution'] = $whoWhenWhere;
                    $data2['Contribution']['sentence_id'] = $this->id;
                    $data2['Contribution']['sentence_lang'] = $this->data['Sentence']['lang'];
                    $data2['Contribution']['translation_id'] = $this->data['Translation']['Translation'][0];
                    $data2['Contribution']['translation_lang'] = $this->data['Sentence']['sentence_lang'];
                    $data2['Contribution']['action'] = 'insert';
                    $data2['Contribution']['type'] = 'link';
                    $contributions[] = $data2;
                }
                if (isset($contributions)) {
                    $this->Contribution->saveAll($contributions);
                }
                
            } else {
                $data['Contribution']['action'] = 'update';
            }
            $this->Contribution->save($data);
        }
    }
    
    /**
     * call after a deletion
     *
     * @return void
     */
     
    function afterDelete()
    {
        

        $this->decrementStatistics($this->data['Sentence']['lang']);

        $data['Contribution']['sentence_id'] = $this->data['Sentence']['id'];
        $data['Contribution']['sentence_lang'] = $this->data['Sentence']['lang'];
        $data['Contribution']['text'] = $this->data['Sentence']['text'];
        $data['Contribution']['action'] = 'delete';
        $data['Contribution']['user_id'] = $this->data['User']['id'];
        $data['Contribution']['datetime'] = date("Y-m-d H:i:s");
        $data['Contribution']['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['Contribution']['type'] = 'sentence';
        $this->Contribution->save($data);
        
        foreach ($this->data['Translation'] as $translation) {
            $data2['Contribution']['sentence_id'] = $this->data['Sentence']['id'];
            $data2['Contribution']['sentence_lang'] = $this->data['Sentence']['lang'];
            $data2['Contribution']['translation_id'] = $translation['id'];
            $data2['Contribution']['translation_lang'] = $translation['lang'];
            $data2['Contribution']['action'] = 'delete';
            $data2['Contribution']['user_id'] = $this->data['User']['id'];
            $data2['Contribution']['datetime'] = date("Y-m-d H:i:s");
            $data2['Contribution']['ip'] = $_SERVER['REMOTE_ADDR'];
            $data2['Contribution']['type'] = 'link';
            $contributions[] = $data2;
            
            $data2['Contribution']['sentence_id'] = $translation['id'];
            $data2['Contribution']['sentence_lang'] = $translation['lang'];
            $data2['Contribution']['translation_id'] = $this->data['Sentence']['id'];
            $data2['Contribution']['translation_lang'] = $this->data['Sentence']['lang'];
            $data2['Contribution']['action'] = 'delete';
            $data2['Contribution']['user_id'] = $this->data['User']['id'];
            $data2['Contribution']['datetime'] = date("Y-m-d H:i:s");
            $data2['Contribution']['ip'] = $_SERVER['REMOTE_ADDR'];
            $data2['Contribution']['type'] = 'link';
            $contributions[] = $data2;
        }
        if (isset($contributions)) {
            $this->Contribution->saveAll($contributions);
        }
    }

    /**
     * search one random chinese/japanese sentence containing $sinogram
     *
     * @param string $sinogram sinogram to search an example sentence
                               containing it
     *
     * @return int the id of this sentence
     */
    function searchOneExampleSentenceWithSinogram($sinogram)
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
       
        return $results[0]['Sentence']['id'] ;  
    }
    
    /**
     * get the highest id for sentences
     *
     * @return int the highest sentence id
     */
    function getMaxId()
    {
        $resultMax = $this->query('SELECT MAX(id) FROM sentences');
        return $resultMax[0][0]['MAX(id)'];
    }
    
    /**
     * get the id of a random sentence, from a particular language if $lang is set
     *
     * @param string $lang restrict random id from the specified code lang
     * @param string $type not use anymore imho
     *
     * @return int a random id
     */
    function getRandomId($lang = null,$type = null )
    {
        /*
        ** this query take constant time when lang=null
        ** and linear time when lang is set, so do not touch this request
        */
        if ( $lang == "und" ) {
            $lang = null ;
        }

        if ($lang == 'jpn' OR $lang == 'eng') {
        
            $min = ($lang == 'eng') ? 15700 : 74000;
            $max = ($lang == 'eng') ? 74000 : 127300;
            $randId =  rand($min, $max);
            $query = ( "SELECT Sentence.id FROM sentences AS Sentence
                WHERE Sentence.id 
                    IN (". ($randId - 1) .",". $randId . ",". ($randId +1) . ")
                AND Sentence.lang = '$lang'
                LIMIT 1 ;
                ;"
            );


        } elseif ( $lang != null AND $lang !='any' ) {
            
            $query= ("SELECT Sentence.id FROM sentences AS Sentence
                WHERE Sentence.lang = '$lang'
                ORDER BY RAND(".rand(). ") LIMIT  1"
                );

        } else {

            $query = 'SELECT Sentence.id  FROM sentences AS Sentence
                JOIN ( 
                    SELECT (
                        RAND('. rand() .') * (SELECT MAX(id) FROM sentences)
                        ) AS id
                    ) AS r2
                WHERE Sentence.id >= r2.id
                ORDER BY Sentence.id ASC LIMIT 1' ;
        }

        $results = $this->query($query);
        /*
        while( !isset($results[0])) {
            $results = $this->query($query);
        }
        */
        return $results[0]['Sentence']['id']; 
    }
    
    /**
     * request for several random sentence id
     *
     * @param string $lang             lang of the sentences we want
     * @param int    $numberOfIdWanted number of ids needed
     *
     * @return array an array of ids
     */

    function getSeveralRandomIds($lang = null , $numberOfIdWanted = 10)
    {
        $ids = array ();
        // exit if we don't have good params
        if (!is_numeric($numberOfIdWanted)) {
            return $ids ;
        }
        
        // if we don't a specific lang
        if ($lang == null OR $lang == "any" OR $lang == 'und') { 

            $query= "SELECT Sentence.id FROM sentences AS Sentence
                ORDER BY RAND(". rand() .") LIMIT  $numberOfIdWanted";
        
       
        } else { // restrict to a specific lang

            $query= "SELECT Sentence.id FROM sentences AS Sentence
            WHERE Sentence.lang = '$lang'
            ORDER BY RAND(".rand(). ") LIMIT  $numberOfIdWanted";
                                    
        }

        $results =  $this->query($query);

        // transform results in simple array of ids
        foreach ($results as $i=>$result) {
            $ids[$i] = $result['Sentence']['id'];
        }
         
        
        return $ids ; 

    }


    /**
     * get all the informations needed to display a sentences in show section
     *
     * @param int $id id of the sentence asked
     *
     * @return array informations about the sentence
     */
    function getSentenceWithId($id)
    {
        $result = $this->find(
            'first',
            array(
                    'conditions' => array ('Sentence.id' => $id),
                    'contain'  => array (
                        'Favorites_users' => array(),
                        'User'            => array(),
                        'SentencesList'   => array()
                )
            )
        );
        // TODO : need to replace it by something more general
        // like $romanisableArray, that way we will not need to
        // change several time the same things 
        if (in_array($result['Sentence']['lang'], array('wuu','cmn','jpn'))) {
                $result['Sentence']['romanization'] = $this->getRomanization(
                    $result['Sentence']['text'],
                    $result['Sentence']['lang']
                );
        }
         
        return $result;
    }

    /**
     * delete the sentence with the given id
     *
     * @param int $id     id of the sentence to be deleted
     * @param int $userId TODO ???
     *
     * @return void
     */

    function delete($id, $userId)
    {
        //TODO  why ?
        $this->id = $id;
        
        // for the logs
        $this->data = $this->find(
            'first',
            array(
                'condition' => array('Sentence.id' => $id)
                , 'contain' => array ('Translation', 'User')
            )
        );

        $this->data['User']['id'] = $userId; 
        
        //$this->Sentence->del($id, true); 
        // TODO : Deleting with del does not delete the right entries in
        // sentences_translations.
        // But I didn't figure out how to solve that =_=;
        // So I'm just going to do something not pretty but whatever, I'm tired!!!
        $this->query('DELETE FROM sentences WHERE id='.$id);
        $this->query('DELETE FROM sentences_translations WHERE sentence_id='.$id);
        $this->query('DELETE FROM sentences_translations WHERE translation_id='.$id);
        
        // need to call afterDelete() manually for the logs
        $this->afterDelete();

    }

    /**
     * Count number of sentences in each language
     *
     * @return array  of each lang => numbr of sentences in this lang
     */
    function getStatistics()
    {
        $query = "
            SELECT ifnull(lang, 'unknown_lang') as lang , numberOfSentences
                FROM langStats 
                ORDER BY numberOfSentences DESC ;
        ";

        $results = $this->query($query);

        // cakephp doesn't like use of AS 
        foreach ($results as $i=>$result) {
            $results[$i]['langStats']['lang'] = $result[0]['lang'];
        }
        return $results ;
    }


    /**
     * add one in stats of a given language
     *
     * @param string $lang language to be incremented
     * 
     * @return void
     */
    
    function incrementStatistics($lang)
    {

        $endOfQuery = "lang = '$lang'";

        if ($lang == '' or $lang == null) {
            $endOfQuery = 'lang is null';
        }

        // TODO sanitize lang
        $query = "
            UPDATE langStats SET numberOfSentences = numberOfSentences + 1
                WHERE $endOfQuery ;
        ";
        $this->query($query);
    }

    /**
     * decrement stats of a given language
     *
     * @param string $lang language to be decremented
     *
     * @return void
     */
    function decrementStatistics($lang)
    {

        $endOfQuery = "lang = '$lang'";

        if ($lang == '' or $lang == null) {
            $endOfQuery = 'lang is null';
        }

        // TODO sanitize lang
        $query = "
            UPDATE langStats SET numberOfSentences = numberOfSentences - 1
                WHERE $endOfQuery ;
        ";
        $this->query($query);
    }

    /**
     * get number of sentences owned by a given user
     *
     * @param int $userId id of the user we want number of sentences of
     *
     * @return array TODO should return an int
     */
    function numberOfSentencesOwnedBy($userId)
    {
        return $this->find(
            'count',
            array(
                 'conditions' => array( 'Sentence.user_id' => $userId)
            )
        );
    }

    /**
     * get translations of a given sentence
     * and translations of translations
     *
     * @param int   $id        id of the sentence we want translations of
     * @param array $excludeId not used anymore imho
     *
     * @return array of translations direct and undirect
     */
    function getTranslationsOf($id,$excludeId = null)
    {
        if ( ! is_numeric($id) ) {
            return array();
        }

        $conditions = array (
            'Sentence.id' => $id
        );
        // DA ultimate Query 
        $query = "
                SELECT p1.text AS text, 
                  p2.text AS translation_text,
                  p2.id   AS translation_id,
                  p2.lang AS translation_lang,
                  p2.user_id AS translation_user_id,
                  'Translation' as distance
                FROM sentences AS p1
                LEFT OUTER JOIN sentences_translations AS t ON p1.id = t.sentence_id
                  LEFT OUTER JOIN sentences AS p2 ON t.translation_id = p2.id
                WHERE 
                 p1.id = '$id' 

                UNION

                SELECT p1.text AS text,
                  p2.text AS translation_text,
                  p2.id   AS translation_id,
                  p2.lang AS translation_lang,
                  p2.user_id AS translation_user_id,
                  'IndirectTranslation'  as distance
                FROM sentences AS p1
                    LEFT OUTER JOIN sentences_translations AS t
                        ON p1.id = t.sentence_id
                    LEFT OUTER JOIN sentences_translations AS t2
                        ON t2.sentence_id = t.translation_id
                    LEFT OUTER JOIN sentences AS p2
                        ON t2.translation_id = p2.id
                WHERE 
                    p1.id != p2.id
                    AND p2.id NOT IN (
                        SELECT sentences_translations.translation_id
                        FROM sentences_translations
                        WHERE sentences_translations.sentence_id = '$id' 
                    )
                    AND p1.id = '$id'
        ";

        $results = $this->query($query);
        //pr ( $results ) ;

        $orderedResults = array(
            "Translation" => array() ,
            "IndirectTranslation" => array()
        );
        foreach ($results as $result) {
            $result = $result[0] ;
            if ($result['translation_id']) { // need to check this because
                // for sentences without translations it would otherwise
                // return an empty translation array.

                $translation = array(
                    'id' => $result['translation_id'],
                    'text' => $result['translation_text'],
                    'user_id' => $result['translation_user_id'],
                    'lang' => $result['translation_lang']
                );

                // TODO : need to replace it by something more general
                // like $romanisableArray, that way we will not need to
                // change several time the same things
                if (in_array($translation['lang'], array('wuu','cmn','jpn'))) {
                    $translation['romanization'] = $this->getRomanization(
                        $translation['text'],
                        $translation['lang']
                    );
                }

                array_push(
                    $orderedResults[$result['distance']],
                    $translation 
                );     
            }
        }

        return $orderedResults;
    }

    
    
    /**
     * Count number of sentences with unknown language
     *
     * @param int $userId id of the user we want the unknown language sentences
     * 
     * @return int number of sentences
     */
    function numberOfUnknownLanguageForUser($userId)
    {
        // Need to do custom query because there is no way to say 
        //  `Sentence`.`lang` = '' OR `Sentence`.`lang` IS NULL
        // with CakePHP, it seems.
        $count = $this->query(
            "
            SELECT COUNT(*) AS `count` 
            FROM `sentences` AS `Sentence` 
            WHERE `Sentence`.`user_id` = $userId 
              AND (`Sentence`.`lang` = '' 
              OR `Sentence`.`lang` IS NULL)
            "
        );
        return $count[0][0]['count'];
    }
    
    /**
     * Retrieve sentences with unknown language.
     *
     * @param int $userId the user id
     *
     * @return array array of the sentences with uknown languages of the user
     */

    function sentencesWithUnknownLanguageForUser($userId)
    {
        // Need to do custom query because there is no way to say 
        //  `Sentence`.`lang` = '' OR `Sentence`.`lang` IS NULL
        // with CakePHP, it seems.
        $sentences = $this->query(
            "
            SELECT * FROM `sentences` AS `Sentence` 
            WHERE `Sentence`.`user_id` = $userId 
              AND (`Sentence`.`lang` = '' 
              OR `Sentence`.`lang` IS NULL)
        "
        );
        return $sentences;
        
    }

    /**
     * get how sentences are clustered in the database according to their language
     *
     * @param int $page do nothing yet
     *
     * @return void
     */

    function getMap($page = 1)
    {

    }

    /**
     * get romanization or equivalent of a sentences
     *
     * @param string $text text to be romanized
     * @param string $lang lang to know which method apply
     *
     * @return string romanisation of the text
     */

    function getRomanization($text,$lang)
    {

        $romanization = '';

        if ($lang == "wuu") {
                        
            //$romanization = "test" ;

        } elseif ($lang == "jpn") {
            $romanization = $this->getJapaneseRomanization($text, 'romaji'); 
            
        } elseif ($lang == "cmn") {
            escapeshellarg($text); 
            $romanization =  exec("echo `adso.sh -i $text -y`");
           

        }
        return $romanization;
    }

    /**
     * get "romanisation" of the $text sentences in japanese
     * into romaji or furigana depending of $type value
     *
     * @param string $text text to romanized
     * @param string $type type of romanization to apply
     *
     * @return string romanized japanese text
     */

    function getJapaneseRomanization($text, $type)
    {
        Sanitize::html($text);
        //$text = escapeshellarg(nl2br($text)); 
        // somehow that doesn't work anymore... 
        // and I found out it's probably because escapeshellarg() 
        // doesn't process UTF-8 anymore...
        
        // escaping manually... until there is a better a solution...
        $text = preg_replace("!\(!", "\\(", $text);
        $text = preg_replace("!\)!", "\\)", $text);
        $text = preg_replace("!\*!", "\\*", $text); 
        $text = preg_replace("!\|!", "\\|", $text);
        $text = preg_replace("!\>!", "\\>", $text);
        $text = preg_replace("!\<!", "\\<", $text);
        $text = preg_replace("!\[!", "\\[", $text);
        $text = preg_replace("!\]!", "\\]", $text);
        $text = preg_replace('!"!', '\\"', $text);
        $text = preg_replace("!'!", "\\'", $text);
        $text = preg_replace("!&!", "\\&", $text);
        $text = preg_replace("!#!", "\\#", $text);

        // TODO HACK SPOTTED! use nl1br instead
        // because \r\n is windows only
        // \n => linux based system
        // \r => mac os based system
        // 25 % of tatoeba visit !

        $text = preg_replace("!\\r\\n!", "\\<br/\\>", $text); // to handle new lines
                
        
        $options = '';
        
        // need to figure out something better...
        // otherwise it displays "konnichiha"
        $text = preg_replace("!今日は!", "kyou wa", $text);
        // otherwise it displays "shi wa u" 
        $text = preg_replace("!死は生!", "shi wa sei", $text); 
        // otherwise it display "uno"
        $text = preg_replace("!生の!", "nama no", $text); 
        // otherwise it display "itta"... 
        //although sometimes "itta" would be correct...
        $text = preg_replace("!入った!", " haitta ", $text); 
        // otherwise it display "kore ba"...
        $text = preg_replace("!来れば!", " kureba ", $text); 
        
        switch($type) {

        case 'romaji':
            $options = ' -Ja -Ha -Ka -Ea -s';
            $sedlist = 'sedlist';
            break;
                
        case 'furigana':
            $options = ' -JH -s -f ';
            $sedlist = 'sedlist2';
            break;
        }
        
        $romanization = exec(
            "echo $text | iconv -f UTF8 -t SHIFT_JISX0213 ".
            "| /home/tatoeba/kakasi/bin/kakasi $options ".
            "|iconv -f SHIFT_JISX0213 -t UTF8".
            "| sed -f /home/tatoeba/www/app/webroot/$sedlist"
        );


        return $romanization;
    }



}
?>
