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

use Cake\Database\Schema\TableSchema;
use Cake\ORM\Table;
use Cake\Core\Configure;
use Cake\Utility\Hash;

class TagsSentencesTable extends Table
{
    public $name = 'TagSentences';
    public $useTable = 'tags_sentences';
    public $actsAs = array('Containable');


    public $belongsTo = array(
        'User',
        'Sentence',
        'Tag',
        );

    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('added_time', 'string');
        return $schema;
    }

    public function initialize(array $config)
    {
        $this->belongsTo('Users');
        $this->belongsTo('Tags');
        $this->belongsTo('Sentences');

        if (Configure::read('Search.enabled')) {
            $this->addBehavior('Sphinx', ['alias' => $this->getAlias()]);
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

        $data = $this->newEntity([
            'user_id' => $userId,
            'tag_id' => $tagId,
            'sentence_id' => $sentenceId,
            'added_time' => date('Y-m-d H:i:s'),
            'alreadyExists' => $isTagged
        ]);

        if (!$isTagged) {
            return $this->save($data);
        } else {
            return $data;
        }
    }


    public function getAllTagsOnSentence($sentenceId)
    {
        return $this->find('all')
            ->contain(['Tags', 'Users'])
            ->where(['TagsSentences.sentence_id' => $sentenceId])
            ->select([
                'Tags.name',
                'Users.id',
                'Users.username',
                'TagsSentences.tag_id',
                'TagsSentences.added_time'
            ])
            ->group(['TagsSentences.tag_id'])
            ->toList();
    }

    public function removeTagFromSentence($tagId,$sentenceId) {
        $this->deleteAll([
            'tag_id' => $tagId,
            'sentence_id' => $sentenceId
        ]);
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
        $date = date('Y-m-d', strtotime('-2 weeks'));

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
        $result = $this->find('all')
            ->where([
                'tag_id' => $tagId,
                'sentence_id' => $sentenceId
            ])
            ->select(['tag_id'])
            ->first();

        return !empty($result);
    }

    public function sphinxAttributesChanged(&$attributes, &$values, &$isMVA, $entity) {
        $sentenceId = $entity->sentence_id;
        $isMVA = true;
        $attributes[] = 'tags_id';
        $records = $this->find('all')
            ->where(['sentence_id' => $sentenceId])
            ->select('tag_id')
            ->toList();
        $tagsId = Hash::extract($records, '{n}.tag_id');
        $values = array($sentenceId => array($tagsId));
    }
}
