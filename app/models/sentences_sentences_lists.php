<?php
/**
 * Tatoeba Project, free collaborative creation of languages corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 * Model for association table between sentences and lists.
 *
 * @category SentencesSentencesLists
 * @package  Models
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

class SentencesSentencesLists extends AppModel
{
    public $name = 'SentencesSentencesLists';
    public $useTable = 'sentences_sentences_lists';
    public $actsAs = array('Containable');
    public $recursive = -1;

    public $belongsTo = array(
        'Sentence' => array('foreignKey' => 'sentence_id'),
        'SentencesList' => array('foreignKey' => 'sentences_list_id')
    );

    /**
     * Add sentence to list.
     *
     * @param int $sentenceId Id of the sentence.
     * @param int $listId     Id of the list.
     *
     * @return array
     */
    public function addSentenceToList($sentenceId, $listId)
    {
        $data = array(
            'sentence_id' => $sentenceId,
            'sentences_list_id' => $listId
        );

        $isSaved = $this->save($data);

        return $isSaved;
    }


    /**
     * Remove sentence from list.
     *
     * @param int $sentenceId Id of the sentence.
     * @param int $listId     Id of the list.
     *
     * @return array
     */
    public function removeSentenceFromList($sentenceId, $listId)
    {
        $conditions = array(
            'sentence_id' => $sentenceId,
            'sentences_list_id' => $listId
        );

        $isDeleted = $this->deleteAll($conditions, false);

        return $isDeleted;
    }


    /**
     * Returns value of $this->paginate, for paginating sentences of a list.
     *
     * @param int    $listId Id of the list.
     * @param int    $limit  Number of sentences per page.
     *
     * @return array
     */
    public function getPaginatedSentencesInList($listId, $limit)
    {
        return array(
            'limit' => $limit,
            'conditions' => array('sentences_list_id' => $listId),
            'order' => 'created DESC'
        );
    }


    public function getListsForSentence($sentenceId)
    {
        $orCondition['is_public'] = true;
        if (CurrentUser::isMember()) {
            $orCondition['user_id'] = CurrentUser::get('id');
        }

        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'sentence_id' => $sentenceId,
                    'OR' => $orCondition
                ),
                'fields' => array('created'),
                'contain' => array(
                    'SentencesList' => array(
                        'fields' => array('id', 'name', 'is_public')
                    )
                ),
                'order' => 'is_public, created DESC'
            )
        );
    }
}
