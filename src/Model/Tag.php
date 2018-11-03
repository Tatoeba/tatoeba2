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
namespace App\Model;


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

    public function getChangeTagName()
    {
        return '@change';
    }
    public function getCheckTagName()
    {
        return '@check';
    }
    public function getDeleteTagName()
    {
        return '@delete';
    }
    public function getNeedsNativeCheckTagName()
    {
        return '@needs native check';
    }
    public function getOKTagName()
    {
        return 'OK';
    }
    /**
     * Cakephp callback before each saving operation
     *
     * @return bool True if the saving operation can continue
     *              False if we have to abort it
     */

    public function beforeSave($options = array())
    {
        $tagName = $this->data['Tag']['name'];
        $result = $this->getIdFromName($tagName);
        return empty($result);
    }

    /**
     *
     *
     */
    public function addTag($tagName, $userId, $sentenceId = null)
    {
        $tagName = trim($tagName);
        if ($tagName == '') {
            return false;
        }
        // Truncate to a maximum byte length of 50. If a multibyte
        // character would be split, the entire character will be
        // truncated.
        $tagName = mb_strcut($tagName, 0, 50, "UTF-8");

        // Special case: don't allow the owner of a sentence to give it an OK tag.
        if ($tagName == 'OK') {
            $owner = $this->Sentence->getOwnerInfoOfSentence($sentenceId);
            if ($owner && $userId == $owner['id']) {
                return false;
            }
        }

        $data = array(
            "Tag" => array(
                "name" => $tagName,
                "user_id" => $userId,
                "created" => date("Y-m-d H:i:s")
            )
        );
        // try to add it as a new tag
        $added = $this->save($data);
        if ($added) {
            $tagId = $this->id;
        } else {
            // This is mildly inefficient because the query has already
            // been performed in beforeSave().
            $tagId = $this->getIdFromName($tagName);
            if ($tagId == null) {
                return false;
            }
        }

        $event = new CakeEvent('Model.Tag.tagAdded', $this, compact('tagName'));
        $this->getEventManager()->dispatch($event);

        if ($sentenceId != null) {
            $this->TagsSentences->tagSentence(
                $sentenceId,
                $tagId,
                $userId
            );
            return $tagId;
        }

        return false;
    }

    public function removeTagFromSentence($tagId, $sentenceId) {
        if (!$this->canRemoveTagFromSentence($tagId, $sentenceId)) {
            return false;
        }
        return $this->TagsSentences->removeTagFromSentence(
            $tagId,
            $sentenceId
        );
    }

    private function canRemoveTagFromSentence($tagId, $sentenceId) {
        $result = $this->TagsSentences->find('first', array(
            'conditions' => array(
                'tag_id' => $tagId,
                'sentence_id' => $sentenceId,
            ),
        ));
        return $result && CurrentUser::canRemoveTagFromSentence(
            $result['TagsSentences']['user_id']
        );
    }

    public function paramsForPaginate($tagId, $limit, $lang = null)
    {
        $conditions = array('Tag.id' => $tagId);
        $contain = array(
            'Tag' => array(
                'fields' => array('id'),
            ),
            'Sentence' => $this->Sentence->paginateContain(),
        );

        if (!empty($lang) && $lang != 'und') {
            $conditions['Sentence.lang'] = $lang;
        }
        $params = array(
            'TagsSentences' => array(
                'limit' => $limit,
                'fields' => array('DISTINCT sentence_id', 'user_id'),
                'conditions' => $conditions,
                'contain' => $contain
            )
        );

        return $params;

    }


    /**
     * Get tag id from tag internal name.
     *
     * @param string $tagInternalName Internal name of the tag.
     *
     * @return int Id of the tag
     */
    public function getIdFromInternalName($tagInternalName) {
        $result = $this->find(
            'first',
            array(
                'conditions' => array('Tag.internal_name' => $tagInternalName),
                'fields' => 'id'
            )
        );
        return $result['Tag']['id'];
    }


    /**
     *
     * TODO
     *
     */
    public function getIdFromName($tagName) {
        $result = $this->find(
            'first',
            array(
                'conditions' => array('Tag.name'=>$tagName),
                'fields' => 'id'
            )
        );
        return !empty($result) ? $result['Tag']['id'] : NULL;
    }


    /**
     *
     * TODO
     *
     */
    public function tagExists($tagId) {
        $result = $this->find(
        'first',
            array(
                'conditions' => array('Tag.id'=>$tagId),
                'fields' => 'name'
            )
        );
        return empty($result) ? false : true;
    }


    /**
     *
     * TODO
     *
     */
    public function getNameFromId($tagId) {
        $result = $this->find(
            'first',
            array(
                'conditions' => array('Tag.id'=>$tagId),
                'fields' => 'name'
            )
        );
        return $result['Tag']['name'];
    }
}
