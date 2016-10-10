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
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Model Class for users sentences.
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class UsersSentences extends AppModel
{
    public $name = 'UsersSentences';
    public $useTable = "users_sentences";
    public $actsAs = array('Containable');
    public $belongsTo = array(
        'Sentence',
        'User' => array('foreignKey' => 'user_id')
    );


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

    public function getPaginatedCorpusOf($userId, $correctness = null, $lang = null)
    {
        $conditions = array('UsersSentences.user_id' => $userId);
        if (is_int($correctness)) {
            $conditions['UsersSentences.correctness'] = $correctness;
        }
        if (!empty($lang)) {
            $conditions['lang'] = $lang;
        }

        $result = array(
            'conditions' => $conditions,
            'fields' => array('id', 'sentence_id', 'correctness', 'modified'),
            'contain' => array(
                'Sentence' => array('id', 'lang', 'text', 'correctness')
            ),
            'limit' => 50,
            'order' => 'modified DESC'
        );

        return $result;
    }


    public function correctnessValueFromLabel($label)
    {
        switch ($label) {
            case "not-ok":
                return -1;
            case "unsure":
                return 0;
            case "ok":
                return 1;
            default:
                return null;
        }
    }


    public function getCorrectnessForSentence($sentenceId)
    {
        $result = $this->find('all',
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

        return $result;
    }

    /**
     * Make all sentences with given id dirty.
     *
     * @param  int $sentenceId ID of sentence to dirty. Should be sanitized.
     */
    public function makeDirty($sentenceId)
    {       
        $sql = "UPDATE `users_sentences` SET `dirty` = 1 WHERE `sentence_id` = '{$sentenceId}'";

        $this->query($sql);
    }
}