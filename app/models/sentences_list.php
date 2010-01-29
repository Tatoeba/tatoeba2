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
        return $this->find(
            "all", 
            array(
                "conditions" => 
                    array("OR" => array(
                        "SentencesList.user_id" => $userId,
                        "SentencesList.is_public" => 1
                    )
                )
            )
        );
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
                "Translation" => array( 
                    "fields" => array("id", "text"),
                    "conditions" => array(
                        "Translation.lang" => $translationsLang
                    )
                )
            ));
            
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
        $this->id = $listId;
        $list = $this->read();
        if ($list['SentencesList']['user_id'] == $userId
            OR $list['SentencesList']['is_public'] == 1
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
        return $this->habtmAdd('Sentence', $listId, $sentenceId);
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
        return $this->habtmDelete('Sentence', $listId, $sentenceId);
    }
    
}
?>