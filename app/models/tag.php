<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 SIMON   Allan   <allan.simon@supinfo.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   SIMON   Allan   <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Model for Tags on sentence.
 *
 * @category Tags
 * @package  Models
 * @author   SIMON   Allan   <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class Tag extends AppModel
{
    public $name = 'Tag';
    
    public $actsAs = array('Containable');


    public $belongsTo = array('User',);
    public $hasMany = array('TagsSentences');       
 
    public $hasAndBelongsToMany = array(
        'Sentence' => array(
            'className' => 'Sentence',
            'joinTable' => 'tags_sentences',
            'foreignKey' => 'sentence_id',
            'associationForeignKey' => 'tag_id'
        ),
         'Tagger' => array(
            'className' => 'User',
            'joinTable' => 'tags_sentences',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'tag_id'
        ),
    );
  
    /**
     * Cakephp callback before each saving operation
     *
     * @return bool True if the saving operation can continue
     *              False if we have to abort it
     */

    public function beforeSave()
    {
        $tagName = $this->data['Tag']['name'];
        $result = $this->find(
            'first',
            array(
                'fields' => 'Tag.id',
                'conditions' => array("Tag.name" => $tagName),
                'contain' => array()
            )
        );

        return empty($result);
    }
  
    /**
     *
     *
     */ 
    public function addTag($tagName, $userId, $sentenceId = null)
    {
        if (trim($tagName) == '') {
            return false;
        }
        
        // Special case: don't allow the owner of a sentence to give it an OK tag.
        if (trim($tagName) == 'OK') {
            $ownerId = $this->Sentence->getOwnerIdOfSentence($sentenceId);
            if ($userId == $ownerId) {
                return false;
            }
        }
        
        $data = array(
            "Tag" => array(
                "name" => $tagName,
                // Until we get rid of the internal_name field altogether, at least make sure 
                // that it's empty. TODO: Remove the field entirely.
                "internal_name" => '', 
                "user_id" => $userId,
                "created" => date("Y-m-d H:i:s")
            )
        );
        // try to add it as a new tag 
        $added = $this->save($data);
        if ($added) {
            $tagId = $this->id;
        } else {
            // This is mildly inefficient because the query within getIdFormName() has already
            // been performed in beforeSave().
            $tagId = $this->getIdFromName($tagName);
        }
        // Send a request to suggestd (the auto-suggest daemon) to update its internal
        // table. 
        // TODO only do this if we add a new ("dirty") tag.
        // See views/helpers/tags.php.
        // $dirty = fopen("http://127.0.0.1:8080/add?str=".urlencode($tagName)."&value=1", 'r');
        // if ($dirty != null) {
            // fclose($dirty);
        // }

        if ($sentenceId != null) {
            $this->TagsSentences->tagSentence(
                $sentenceId,
                $tagId,
                $userId
            );
        }
        
        return true; // TODO This function was not returning anything but the
                     // return value is needed in TagsController::add_tag().
                     // I'm making it return true for now.
    }

    public function removeTagFromSentence($tagId, $sentenceId) {
        return $this->TagsSentences->removeTagFromSentence(
            $tagId,
            $sentenceId
        );
    }

    public function paramsForPaginate($tagId, $limit, $lang = null)
    {
        $conditions = array('Tag.id' => $tagId);
        $contain =  array(
            'Tag' => array(
                'fields' => array()
            )
        );

        if (!empty($lang) && $lang != 'und') {
             $conditions = array(
                "AND" => array(
                    'Tag.id' => $tagId,
                    'Sentence.lang' => $lang
                )
            );

            $contain =  array(
                'Tag' => array(
                    'fields' => array()
                ),
                'Sentence' => array(
                    'fields' => array()
                ),
            );
     
        }
        $params = array(
            'TagsSentences' => array(
                'limit' => $limit,
                'fields' => array('user_id','sentence_id'), 
                'conditions' => $conditions,
                'contain' => $contain
            )
        );
        
        return $params;

    }

    /**
     * Give all tags ordered by number of sentences they tag
     *
     * @return array All the tags
     */
    public function getAllTagsOrdered(){
        return $this->find(
            'all',
            array(
                'fields' => array('name', 'id', 'nbrOfSentences'),
                'contain' => array(),
                'order' => 'nbrOfSentences DESC',
            )
        );
    }

    public function getIdFromName($tagName) {
        $result = $this->find(
            'first',
            array(
                'conditions' => array('Tag.name'=>$tagName),
                'contain' => array(),
                'fields' => 'id'
            )
        );
        return $result['Tag']['id'];
    }

    public function getNameFromId($tagId) {
        $result = $this->find(
            'first',
            array(
                'conditions' => array('Tag.id'=>$tagId),
                'contain' => array(),
                'fields' => 'name'
            )
        );
        return $result['Tag']['name'];
    }
}
