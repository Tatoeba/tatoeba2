<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 * Model for sentences list.
 *
 * @category SentencesLists
 * @package  Models
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class SentencesList extends AppModel
{
    public $actsAs = array('Containable');
    public $recursive = -1;
    public $belongsTo = array('User');
    public $hasMany = array('SentencesSentencesLists');
    public $hasAndBelongsToMany = array('Sentence');


    /**
     * Retrieves list.
     *
     * @param int $id Id of the list.
     *
     * @return array
     */
    public function getList($id)
    {
        return $this->find(
            'first',
            array(
                'conditions' => array('SentencesList.id' => $id),
                'fields' => array(
                    'SentencesList.id',
                    'SentencesList.name',
                    'SentencesList.user_id',
                    'SentencesList.is_public',
                    'SentencesList.created'
                ),
                'contain' => array(
                    'User' => array(
                        'fields' => array('User.username')
                    )
                )
            )
        );
    }


    /**
     * Returns the sentences lists that the given user can add sentences to.
     *
     * @param int $userId Id of the user.
     *
     * @return array
     */
    public function getUserChoices($userId)
    {
        $results = $this->find(
            "all",
            array(
                "conditions" =>
                    array("OR" => array(
                        "SentencesList.user_id" => $userId,
                        "SentencesList.is_public" => 1
                    )
                ),
                'fields' => array('id', 'name', 'user_id'),
                'order' => 'name'
            )
        );

        $privateLists = array();
        $publicLists = array();

        $currentUserId = CurrentUser::get('id');
        foreach ($results as $result) {
            $listId = $result['SentencesList']['id'];
            $listName = $result['SentencesList']['name'];
            $userId = $result['SentencesList']['user_id'];

            if (empty($listName)) {
                $listName = __('unnamed list', true);
            }

            if ($currentUserId == $userId) {
                $privateLists[$listId] = $listName;
            } else {
                $publicLists[$listId] = $listName;
            }
        }

        $lists['Private'] = $privateLists;
        $lists['Public'] = $publicLists;

        return $lists;
    }


    /**
     * Returns public lists that do not belong to given user.
     *
     * @param int $userId Id of the user.
     *
     * @return array
     */
    public function getPaginatedLists(
        $search = null, $username = null, $onlyCollaborative = false
    ) {
        $conditions = null;
        if (!empty($search)) {
            $conditions['SentencesList.name LIKE'] = "%$search%";
        }
        if (!empty($username)) {
            $userId = $this->User->getIdFromUsername($username);
            $conditions['SentencesList.user_id'] = $userId;
        }
        if ($onlyCollaborative) {
            $conditions['SentencesList.is_public'] = true;
        }

        return array(
            'conditions' => $conditions,
            'contain' => array(
                'User' => array(
                    'fields' => array('username')
                )
            ),
            'order' => 'created DESC',
            'limit' => 20
        );
    }


    /**
     * Returns all the lists that given user cannot edit.
     *
     * @param int $userId Id of the user
     *
     * @return array
     */
    public function getNonEditableListsForUser($userId)
    {
        return $this->find(
            "all",
            array(
                "conditions" => array(
                    "SentencesList.user_id !=" => $userId,
                    "SentencesList.is_public" => 0
                ),
                'contain' => array(
                    'User' => array(
                        'fields' => array('username')
                    )
                ),
                'order' => 'name'
            )
        );
    }


    /**
     *
     *
     *
     */
    public function getSentencesAndTranslationsOnly($listId, $translationLang)
    {
        if (empty($translationLang)) {
            $request = '
            SELECT Sentence.id, Sentence.text
            from sentences_sentences_lists as ssls
            left join sentences as Sentence on ssls.sentence_id = Sentence.id
            where ssls.sentences_list_id = '.$listId
            ;
        } else {
            $request = '
            select Sentence.id, Sentence.text, Translation.text
            from sentences_sentences_lists as ssls
            left join sentences as Sentence on ssls.sentence_id = Sentence.id
            left join
            (select s.id as sentence_id , t.text as text
            from sentences_sentences_lists as ssls
                left join sentences as s on ssls.sentence_id = s.id
                left join sentences_translations as st on (s.id = st.sentence_id)
                left join sentences as t on ( st.translation_id = t.id )
            where ssls.sentences_list_id = '.$listId.'
                and t.lang  = "'.$translationLang.'"
            ) as Translation on Sentence.id = Translation.sentence_id
            where ssls.sentences_list_id = '.$listId
            ;
        }
        $results = $this->query($request);

        return $results;
    }


    /**
     * Returns true if list belongs to current user OR is collaborative.
     *
     * @param int $listId Id of list.
     * @param int $userId Id of user.
     *
     * @return bool
     */
    public function isEditableByCurrentUser($listId, $userId)
    {
        $list = $this->find(
            'first',
            array(
                "conditions" => array(
                    "SentencesList.id" => $listId,
                    "OR" => array(
                        "user_id" => $userId,
                        "is_public" => 1
                    )
                ),
                "fields" => array(
                    'user_id',
                    'is_public'
                )
            )
        );

        return !empty($list);
    }


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
        $isSaved = $this->SentencesSentencesLists->addSentenceToList(
            $sentenceId, $listId
        );

        if ($isSaved) {
            $this->_incrementNumberOfSentencesToList($listId);
        }

        return $isSaved;
    }


    /**
     * get all the list of a given user
     *
     * @param int $username Username of the user
     *
     * @return array
     */
    public function getPaginatedUserLists($username)
    {
        $userId = $this->User->getIdFromUsername($username);

        $paginate = array(
            'conditions' => array(
                "SentencesList.user_id" => $userId,
            ),
            'contain' => array(
                'User' => array(
                    'fields' => array('username')
                )
            ),
            'order' => 'name',
            'limit' => 20
        );

        return $paginate;

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
        $isDeleted = $this->SentencesSentencesLists->removeSentenceFromList(
            $sentenceId, $listId
        );

        if ($isDeleted) {
            $this->_decrementNumberOfSentencesToList($listId);
        }

        return $isDeleted;
    }


    /**
     * Increment number of sentence to list.
     *
     * @param int $listId Id of the list.
     *
     * @return boolean
     */
    private function _incrementNumberOfSentencesToList($listId)
    {
        return $this->updateAll(
            array(
                'numberOfSentences'=>'numberOfSentences+1'
            ),
            array('SentencesList.id'=>$listId)
        );
    }


    /**
     * Decrement number of sentence to list.
     *
     * @param int $listId Id of the list.
     *
     * @return boolean
     */
    private function _decrementNumberOfSentencesToList($listId)
    {
        $success = $this->updateAll(
            array('numberOfSentences'=>'numberOfSentences-1'),
            array('SentencesList.id'=>$listId)
        );

        return $success;
    }


    /**
     * Returns name of the list of given id.
     *
     * @param id $listId Id of the list.
     *
     * @return string
     */
    public function getNameForListWithId($listId)
    {
        $result = $this->find(
            'first',
            array(
                'conditions' => array(
                    'id' => $listId
                ),
                'fields' => array('name')
            )
        );

        return $result['SentencesList']['name'];
    }


    /**
     * Add new sentence to list.
     *
     * @param int    $listId
     * @param string $sentenceText
     * @param string $sentenceLang
     *
     * @return bool
     */
    public function addNewSentenceToList($listId, $sentenceText, $sentenceLang)
    {
        $userId = CurrentUser::get('id');

        // Checking if user can add to list.
        $userLevel = $this->User->getLevelOfUser($userId);
        $canAdd = $this->isEditableByCurrentUser($listId, $userId) && $userLevel > -1;
        if (!$canAdd) {
            return false;
        }

        // Saving sentence
        $sentenceSaved = $this->Sentence->saveNewSentence(
            $sentenceText, $sentenceLang, $userId
        );
        if (!$sentenceSaved) {
            return false;
        }

        // Adding to list
        $sentenceId = $this->Sentence->id;
        if ($this->addSentenceToList($sentenceId, $listId)) {
            return $this->Sentence->getSentenceWithId($sentenceId);
        } else {
            return null;
        }
    }


    /**
     * Get number of sentences in list.
     *
     * @param int $listId Id of the list.
     *
     * @return int
     */
    public function getNumberOfSentences($listId)
    {
        $count = $this->SentencesSentencesLists->find(
            'count',
            array(
                'conditions' => array(
                    'sentences_list_id' => $listId
                )
            )
        );

        return $count;
    }
}
?>
