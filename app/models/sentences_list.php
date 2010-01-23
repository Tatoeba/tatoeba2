<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang (tranglich@gmail.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


class SentencesList extends AppModel{
	var $name = 'SentencesList';
	
	var $belongsTo = array('User');
	
	var $actsAs = array('ExtendAssociations', 'Containable');
	
	var $hasAndBelongsToMany = array('Sentence');
	
	/**
	 * Returns the sentences lists that the given user can 
	 * add sentences to.
	 */
	function getUserChoices($userId){
		$this->SentencesList->recursive = -1;
		return $this->find(
			'all', 
			array("conditions" => 
				array("OR" => array(
					  "SentencesList.user_id" => $userId
					, "SentencesList.is_public" => 1
				)
			))
		);
	}
	
	/**
	 * Returns public lists that do not belong to given user.
	 */
	function getPublicListsNotFromUser($userId){
		return $this->find('all', array(
			"conditions" => array(
				  "SentencesList.user_id !=" => $userId
				, "SentencesList.is_public" => 1
			)
		));
	}
	
	/**
	 * Returns all the lists that given user cannot edit.
	 */
	function getNonEditableListsForUser($userId){
		return $this->find('all', array(
			"conditions" => array(
				  "SentencesList.user_id !=" => $userId
				, "SentencesList.is_public" => 0
			)
		));
	}
    
    /**
	 * Returns sentences from a list, along with the 
     * translations of the sentences if language is specified.
	 */
    function getSentences ( $listId, $translationsLanguage = null, $romanization = null ) {
        
        $contain = array( "Sentence");
        
        if ( $translationsLanguage != null ) {
            
            $contain = array( "Sentence" => array( 
                "Translation" => array( 
                    "fields" => array("text")
                    , "conditions" => array(
                        "Translation.lang" => $translationsLanguage
                    )
                )
            ));
            
        }
        
        $list = $this->find(
            'first'
            , array(
                "conditions" => array("SentencesList.id" => $listId)
                , "contain" => $contain
            )
        );
        
        if ( $romanization != null ) {
            $sentences = array();
            foreach( $list['Sentence'] as $sentence ){
                $sentence['romanization'] = $this->Sentence->getRomanization(
                    $sentence['text'], $sentence['lang']
                );
                
                if( $translationsLanguage != null ){
                    $translations = array();
                    foreach( $sentence['Translation'] as $translation ){
                        $translation['romanization'] = $this->Sentence->getRomanization(
                            $translation['text'], $translationsLanguage
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
}
?>