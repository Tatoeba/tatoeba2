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
 */
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Core\Configure;

class TagsSentencesTable extends Table
{
    public $name = 'TagSentences';
    public $useTable = "tags_sentences";
    public $actsAs = array('Containable');


    public $belongsTo = array(
        'User',
        'Sentence',
        'Tag',
        );

    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        if (Configure::read('Search.enabled')) {
            $this->Behaviors->attach('Sphinx');
        }
    }

    protected function _findCount($state, $query, $results = array()) {
        if ($state === 'before') {
            // Filter out duplicate tags
            $query['fields'] = 'DISTINCT sentence_id';
        }
        return parent::_findCount($state, $query, $results);
    }

    public function tagSentence($sentenceId, $tagId, $userId)
    {
        $isTagged = $this->isSentenceTagged($sentenceId, $tagId);

        if (!$isTagged) {
            $data = array(
                "TagsSentences" => array(
                    "user_id" => $userId,
                    "tag_id" => $tagId,
                    "sentence_id" => $sentenceId,
                    "added_time" => date("Y-m-d H:i:s")
                )
            );

            $this->save($data);

            return true;
        }

        return false;
    }


    public function getAllTagsOnSentence($sentenceId)
    {
        return $this->find(
            'all',
            array(
                'fields' => array(
                    'Tag.name',
                    'User.id',
                    'User.username',
                    'TagsSentences.tag_id',
                    'TagsSentences.added_time'
                ),
                'conditions' => array(
                    'TagsSentences.sentence_id' => $sentenceId
                ),
                'contain' => array(
                    'Tag', 'User'
                ),
                'group' => 'TagsSentences.tag_id'
            )
        );
    }

    public function removeTagFromSentence($tagId,$sentenceId) {
        $this->unBindModel(
            array(
                'belongsTo' => array('User', 'Tag', 'Sentence')
            )
        );
        $this->deleteAll(
            array(
                'tag_id' => $tagId,
                'sentence_id' => $sentenceId
            ),
            // we don't want record to be deleted in cascade, as we only want
            // the relation to be broken
            false,
            true  // yet we want callbacks
        );
    }


    /**
     * Get sentences with tag that were tagged more than 2 weeks ago.
     *
     * @param int    $tagId Id of the tag.
     * @param string $lang  Language of the sentences.
     *
     * @return array
     */
    public function getSentencesWithNonNewTag($tagId, $lang)
    {
        $date = date('Y-m-d', strtotime("-2 weeks"));

        $sentenceConditions = array();

        if (!empty($lang)) {
            $sentenceConditions = array('lang' => $lang);
        }

        return $this->find(
            'all',
            array(
                'fields' => array('sentence_id'),
                'conditions' => array(
                    'tag_id' => $tagId,
                    'added_time <' => $date,
                    'text !=' => null
                ),
                'contain' => array(
                    'Sentence' => array(
                        'Transcription' => array(
                            'User' => array('fields' => 'username'),
                        ),
                        'conditions' => $sentenceConditions
                    )
                ),
                'limit' => 100
            )
        );
    }


    /**
     * Returns true if a sentence is tagged with given tagId
     *
     * @param int $sentenceId Id of the sentence.
     * @param int $tagId      Id of the tag.
     *
     * @return boolean
     */
    public function isSentenceTagged($sentenceId, $tagId)
    {
        $result = $this->find(
            'first',
            array(
                'fields' => 'tag_id',
                'conditions' => array(
                    "tag_id" => $tagId,
                    "sentence_id" => $sentenceId
                ),
            )
        );

        return !empty($result);
    }

    public function sphinxAttributesChanged(&$attributes, &$values, &$isMVA) {
        $isMVA = true;
        $attributes[] = 'tags_id';
        $sentenceId = $this->data['TagsSentences']['sentence_id'];
        $records = $this->find('all', array(
            'conditions' => array('sentence_id' => $sentenceId),
            'fields' => 'tag_id',
        ));
        $tagsId = (array)Set::classicExtract($records, '{n}.TagsSentences.tag_id');
        $values = array($sentenceId => array($tagsId));
    }
}
