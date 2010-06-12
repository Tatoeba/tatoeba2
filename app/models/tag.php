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

    private function _tag_to_internal_name($tagName)
    {
        $tagName = trim($tagName);
        return preg_replace('/(\s{1,})|([\[\)\'"])/u','_', $tagName);


    }

    public function removeTagFromSentence($tagId, $sentenceId) {
        return $this->TagsSentences->removeTagFromSentence(
            $tagId,
            $sentenceId
        );
    }

    public function paramsForPaginate($tagInternalName, $limit)
    {
        $params = array(
            'TagsSentences' => array(
                'limit' => $limit,
                'fields' => array('user_id','sentence_id'), 
                'conditions' => array('Tag.internal_name' => $tagInternalName),
                'contain' => array(
                    'Tag' => array(
                        'fields' => array()
                    )
                )
            )
        );
        
        return $params;

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
