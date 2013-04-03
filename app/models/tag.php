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
     * Cakephp callback before each saving operations
     *
     * @return bool True if the saving operation can continue
     *              False if we have to abort it
     */

    public function beforeSave()
    {

        $internalName = $this->data['Tag']['internal_name'];
        $result = $this->find(
            'first',
            array(
                'fields' => 'Tag.id',
                'conditions' => array("Tag.internal_name" => $internalName),
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
        
        if (trim($tagName) == 'OK') {
            $ownerId = $this->Sentence->getOwnerIdOfSentence($sentenceId);
            if ($userId == $ownerId) {
                return false;
            }
        }
        
        $internalName = $this->_tag_to_internal_name($tagName);      
        
        $data = array(
            "Tag" => array(
                "name" => $tagName,
                "internal_name" => $internalName,
                "user_id" => $userId,
                "created" => date("Y-m-d H:i:s")
            )
        );
        // try to add it as a new tag 
        $this->save($data); 
        
        //send a request to suggestd to update its internal
        // table 
        // TODO only do this if we add a new tag 
        // $dirty = fopen("http://127.0.0.1:8080/add?str=".urlencode($tagName)."&value=1", 'r');
        // if ($dirty != null) {
            // fclose($dirty);
        // }

        if ($sentenceId != null) {
            $result = $this->find(
                "first",
                array(
                    'fields' => 'Tag.id',
                    'conditions' => array("Tag.internal_name" => $internalName),
                    'contain' => array()
                )
            );
            $tagId = $result['Tag']['id'];
             
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

    /**
     * utility function to transform the human friendly tag
     * into a url friendly representation, also needed to avoid as much
     * as possible duplicate
     * 
     * @param string $tagName The human friendly tag name to convert
     *
     * @return string The url friendly string 
     */
    private function _tag_to_internal_name($tagName)
    {
        $tagName = trim($tagName);
        return preg_replace('/(\s{1,})|([\[\)\'":])/u','_', $tagName);


    }

    public function removeTagFromSentence($tagId, $sentenceId) {
        return $this->TagsSentences->removeTagFromSentence(
            $tagId,
            $sentenceId
        );
    }

    public function paramsForPaginate($tagInternalName, $limit, $lang = null)
    {
        $conditions = array('Tag.internal_name' => $tagInternalName);
        $contain =  array(
            'Tag' => array(
                'fields' => array()
            )
        );

        if (!empty($lang) && $lang != 'und') {
             $conditions = array(
                "AND" => array(
                    'Tag.internal_name' => $tagInternalName,
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
                'fields' => array('name', 'internal_name', 'nbrOfSentences'),
                'contain' => array(),
                'order' => 'nbrOfSentences DESC',
            )
        );
    }

    public function getIdFromInternalName($tagInternalName) {

        $result = $this->find(
            'first',
            array(
                'conditions' => array('Tag.internal_name'=>$tagInternalName),
                'contain' => array(),
                'fields' => 'id'
            )
        );
        return $result['Tag']['id'];
    }
    
    
    /**
     * Get id and name of tag, from given 'internal name'.
     *
     * @param string $tagInternalName Internal name of the tag.
     *
     * @return array
     */
    public function getInfoFromInternalName($tagInternalName) {

        $result = $this->find(
            'first',
            array(
                'conditions' => array('Tag.internal_name'=>$tagInternalName),
                'contain' => array(),
                'fields' => array('id', 'name')
            )
        );
        return $result;
    }
}
