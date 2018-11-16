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

class UsersSentencesTable extends Table
{
    public $name = 'UsersSentences';
    public $useTable = "users_sentences";
    public $actsAs = array('Containable');
    public $belongsTo = array(
        'Sentence',
        'User' => array('foreignKey' => 'user_id')
    );


    /**
     * 
     */
    public function saveSentence($sentenceId, $correctness, $userId) 
    {
        $userSentence = $this->findBySentenceIdAndUserId(
            $sentenceId, $userId
        );

        if (empty($userSentence)) {
            $data = array(
                'user_id' => $userId,
                'sentence_id' => $sentenceId,
                'correctness' => $correctness
            );
        } else {
            $data = array(
                'id' => $userSentence['UsersSentences']['id'],
                'correctness' => $correctness,
                'dirty' => 0
            );
        }

        return $this->save($data);
    }

    /**
     * 
     */
    public function deleteSentence($sentenceId, $userId) 
    {
        $userSentence = $this->findBySentenceIdAndUserId(
            $sentenceId, $userId
        );

        if ($userSentence) {
            $id = $userSentence['UsersSentences']['id'];
            return $this->delete($id, false);
        }

        return false;
    }

    /**
     * Get correctness for sentence as set by user.
     *
     * @param  int $sentenceId Sentence ID.
     * @param  int $userId     User ID.
     * 
     * @return int
     */
    public function correctnessForSentence($sentenceId, $userId)
    {
        $result = $this->find(
            'first', array(
                'conditions' => array(
                    'sentence_id' => $sentenceId,
                    'user_id' => $userId
                )
            )
        );

        if (empty($result)) {
            return -2;
        } else {
            return $result['UsersSentences']['correctness'];
        }
    }

    /**
     * Get paginated user_sentneces for user.
     *
     * @param  int    $userId           User ID.
     * @param  int    $correctnessLabel Label for correctness value.
     * @param  string $lang             Language.
     *
     * @return array
     */
    public function getPaginatedCorpusOf($userId, $correctnessLabel = null, $lang = null)
    {
        $correctness = $this->correctnessValueFromLabel(
            $correctnessLabel
        );
        $conditions = array('UsersSentences.user_id' => $userId);

        if (is_int($correctness)) {
            $conditions['UsersSentences.correctness'] = $correctness;
        } elseif ($correctness === 'outdated') {
            $conditions['UsersSentences.dirty'] = true;
        }

        if (!empty($lang)) {
            $conditions['lang'] = $lang;
        }

        return array(
            'conditions' => $conditions,
            'fields' => array('id', 'sentence_id', 'correctness', 'modified'),
            'contain' => array(
                'Sentence' => array('id', 'lang', 'text', 'correctness')
            ),
            'limit' => 50,
            'order' => 'modified DESC'
        );
    }

    /**
     * Get correctness integer from label, if it exists.
     *
     * @param  string $label Correctness label used in route.
     *
     * @return int|string
     */
    private function correctnessValueFromLabel($label)
    {
        $values = [
            'not-ok' => -1,
            'unsure' => 0,
            'ok' => 1
        ];

        if (in_array($label, array_keys($values))) {
            return $values[$label];
        }

        return $label;
    }

    /**
     * Get correctness data for a sentence.
     *
     * @param  int $sentenceId Sentence ID.
     *
     * @return array
     */
    public function getCorrectnessForSentence($sentenceId)
    {
        return $this->find('all',
            array(
                'fields' => array(
                    'correctness', 'modified', 'dirty'
                ),
                'conditions' => array(
                    'sentence_id' => $sentenceId
                ),
                'contain' => array(
                    'User' => array(
                        'fields' => array('id', 'username')
                    )
                )
            )
        );
    }

    /**
     * Make all sentences with given id dirty.
     *
     * @param  int $sentenceId ID of sentence to dirty. Should be sanitized.
     */
    public function makeDirty($sentenceId)
    {
        $this->updateAll(['dirty' => true], ['sentence_id' => $sentenceId]);
    }
}