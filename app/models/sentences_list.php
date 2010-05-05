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
    public $belongsTo = array('User');
    public $actsAs = array('ExtendAssociations', 'Containable');
    public $hasAndBelongsToMany = array('Sentence');
    
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
                'contain' => array(),
                'fields' => array('id', 'name', 'user_id')
            )
        );
        
        $privateLists = array();
        $publicLists = array();
        foreach ($results as $result) {
            $listId = $result['SentencesList']['id'];
            $listName = $result['SentencesList']['name'];
            $userId = $result['SentencesList']['user_id'];
            
            if (CurrentUser::get('id') == $userId) {
                $privateLists[$listId] = $listName;
            } else {
                $publicLists[$listId] = $listName;
            }
        }
        
        // This seems to be the only way to keep the keys...
        $listsKeys = array(-1, -2, 0);
        $listsValues = array(
            __('Add to new list...', true), 
            __('Manage lists...', true),
            '-----------------' 
        );
        foreach ($privateLists as $key => $privateList) {
            $listsKeys[] = $key;
            $listsValues[] = $privateList;
        }
        $listsKeys[] = -3;
        $listsValues[] = '-----------------';
        foreach ($publicLists as $key => $publicList) {
            $listsKeys[] = $key;
            $listsValues[] = $publicList;
        }
        $lists = array_combine($listsKeys, $listsValues);
        
        return $lists;
    }
    
    /**
     * Returns public lists that do not belong to given user.
     *
     * @param int $userId Id of the user.
     *
     * @return array
     */
    public function getPublicListsNotFromUser($userId)
    {
        return $this->find(
            "all", 
            array(
                "conditions" => array(
                    "SentencesList.user_id !=" => $userId,
                    "SentencesList.is_public" => 1
                ),
                'contain' => array(
                    'User' => array(
                        'fields' => array('username')
                    )
                )
            )
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
                )
            )
        );
    }
    
    /**
     * Returns sentences from a list, along with the translations of the sentences 
     * if language is specified.
     *
     * @param int    $listId           Id of the list.
     * @param string $translationsLang Language of the translations.
     * @param string $romanization     Display or not romanizations.
     *
     * @return array
     */
    public function getSentences(
        $listId, $translationsLang = null, $romanization = null
    ) {
        
        $contain = array("Sentence");
        
        if ($translationsLang != null) {
            $contain = array(
                "Sentence" => array(
                    "fields" => array("id", "lang", "text"),
                    "Translation" => array( 
                        "fields" => array("id", "lang", "text"),
                        "conditions" => array(
                            "lang" => $translationsLang
                        )
                    )
                )
            );
        }
        
        $list = $this->find(
            "first",
            array(
                "conditions" => array("SentencesList.id" => $listId),
                "contain" => $contain
            )
        );
        
        if ($romanization != null) {
            $sentences = array();
            foreach ($list['Sentence'] as $sentence) {
                $sentence['romanization'] = $this->Sentence->getRomanization(
                    $sentence['text'], $sentence['lang']
                );
                
                if ($translationsLang != null) {
                    $translations = array();
                    foreach ($sentence['Translation'] as $translation) {
                        $translation['romanization'] 
                            = $this->Sentence->getRomanization(
                                $translation['text'], $translationsLang
                            );
                        $translations[] = $translation;
                    }
                    
                    $sentence['Translation'] = $translations;
                }
                
                $sentences[] = $sentence;
            }
            $list['Sentence'] = $sentences;
        }
        
        return $list;
    }
    
    /**
     * Check if list belongs to current user.
     *
     * @param int $listId Id of list.
     * @param int $userId Id of user.
     *
     * @return bool
     */
    public function belongsToCurrentUser($listId, $userId)
    {

        // TODO it would be simpler to all do in the request
        // if no result = don't belong to
        // if result = belong to
        // it will make the request lighter

        $list = $this->find(
            'first',
            array(
                "conditions" => array(
                    "SentencesList.id" => $listId
                ),
                "contain" => array(),
                "fields" => array(
                    'user_id',
                    'is_public'
                )
            )
        );

        if ($list['SentencesList']['user_id'] == $userId
            || $list['SentencesList']['is_public'] == 1
        ) {
            return true;
        } else {
            return false;
        }
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
          $savedValue = $this->habtmAdd('Sentence', $listId, $sentenceId);
          $this->_incrementNumberOfSentencesToList($listId);
        
          return $savedValue;
    }
    
    /**
     * get all the list of a given user
     *
     * @param int $userId Id of the user
     *
     * @return array
     */

    public function getUserLists($userId)
    {
        $myLists = $this->find(
            'all',
            array(
                'conditions' => array(
                    "SentencesList.user_id =" => $userId,
                ),
                'contain' => array(
                    'User' => array(
                        'fields' => array('username')
                    )
                )
            )
        );

        return $myLists;

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
        $savedValue = $this->habtmDelete('Sentence', $listId, $sentenceId);

        $this->_decrementNumberOfSentencesToList($listId);
        return $savedValue;
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
                'fields' => array('name'),
                'contain' => array()
            )
        );
        
        return $result['SentencesList']['name'];
    }
}
?>
