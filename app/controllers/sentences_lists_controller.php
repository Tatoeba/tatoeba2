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


App::import('Core', 'Sanitize');

class SentencesListsController extends AppController{

	var $name = 'SentencesLists' ;
	var $helpers = array('Sentences', 'Navigation', 'Html');
	
	
	function beforeFilter() {
	    parent::beforeFilter();
		
		// setting actions that are available to everyone, even guests
	    $this->Auth->allowedActions = array('*');
	}
	
	
	/**
	 * Displays all the lists.
	 * If user is logged in, it will also display a form to add
	 * a new list and the lists that belongs to that user.
	 */
	function index(){
		$lists = $this->SentencesList->find('all');
		$this->set('lists', $lists);
		
		if($this->Auth->user('id')){
			$myLists = $this->SentencesList->findAllByUserId($this->Auth->user('id'));
			$this->set('myLists', $myLists);
		}
	}
	
	
	/**
	 * Display content of a list.
	 */
	function show($id){
        Sanitize::paranoid($id);
		$this->SentencesList->id = $id;
		$this->set('list', $this->SentencesList->read());
	}
	
	
	/**
	 * Create a list.
	 */
	function add(){
        Sanitize::html($this->data['SentencesList']['name']);
		if(!empty($this->data)){
			$this->data['SentencesList']['user_id'] = $this->Auth->user('id');
			$this->SentencesList->save($this->data);
		}
		$this->redirect(array("action"=>"index"));
	}
	
	
	/**
	 * Edit list. From that page user can remove sentences from list, 
	 * edit list name or delete list.
	 */
	function edit($id){
        Sanitize::paranoid($id); 
		if(!$this->belongsToCurrentUser($id)){
			$this->redirect(array("action" => "show"));
		}else{
			$this->SentencesList->id = $id;
			$this->set('list', $this->SentencesList->read());
		}
	}
	
	
	/**
	 * Saves the new name of a list. 
	 * Used in AJAX request from sentences_lists.edit_name.js
	 */
	function save_name(){
        Sanitize::paranoid($_POST['id']);
        Sanitize::html($_POST['value']); 
		Configure::write('debug', 0);
		if($this->belongsToCurrentUser($_POST['id'])){
			if(isset($_POST['value']) AND isset($_POST['id'])){
				$this->SentencesList->id = $_POST['id'];
				if($this->SentencesList->saveField('name', $_POST['value'])){
					$this->set('result', $_POST['value']);
				}else{
					$this->set('result', 'error');
				}
			}else{
				$this->set('result', 'error');
			}
		}
	}
	
	
	/**
	 * Delete list.
	 */
	function delete($listId){
        Sanitize::paranoid($listId);
		if($this->belongsToCurrentUser($listId)){
			$this->SentencesList->delete($listId);
		}
		$this->redirect(array("action" => "index"));
	}
	
	/**
	 * Add sentence to a list.
	 */
	function add_sentence_to_list($sentenceId, $listId){
        Sanitize::paranoid($sentenceId);
        Sanitize::paranoid($listId);
		Configure::write('debug', 0);
		$this->set('s', $sentenceId);
		$this->set('l', $listId);
		if($this->belongsToCurrentUser($listId)){
			if($this->SentencesList->habtmAdd('Sentence' , $listId, $sentenceId)){
				$this->set('listId', $listId);
			}else{
				$this->set('listId', 'error');
			}
		}
	}
	
	
	/**
	 * Create a new list and add a sentence to that list.
	 */
	function add_sentence_to_new_list($sentenceId, $listName){
        Sanitize::paranoid($sentenceId);
        Sanitize::html($listName);
		Configure::write('debug', 0);
		if($this->belongsToCurrentUser($listId)){
			if($listName != ''){
				$newList['SentencesList']['user_id'] = $this->Auth->user('id');
				$newList['SentencesList']['name'] = $listName;
				if($this->SentencesList->save($newList)){
					$this->SentencesList->habtmAdd('Sentence', $this->SentencesList->id, $sentenceId);
					$this->set('listId', $this->SentencesList->id);
				}else{
					$this->set('listId', 'error');
				}
			}else{
				$this->set('listId', 'error');
			}
		}
	}
	
	
	/**
	 * Remove sentence from a list.
	 */
	function remove_sentence_from_list($sentenceId, $listId){
        Sanitize::paranoid($sentenceId);
        Sanitize::paranoid($listId);
		Configure::write('debug', 0);
		if($this->belongsToCurrentUser($listId)){
			if($this->SentencesList->habtmDelete('Sentence' , $listId, $sentenceId)){
				$this->set('removed', true);
			}
		}
	}
	
	
	/**
	 * Displays the lists of a specific user.
	 */
	function of_user($userId){
		$lists = $this->SentencesList->findAllByUserId($userId);
		$this->set('lists', $lists);
	}
	
	
	/**
	 * Returns the lists that belong to the user currently connected.
	 * It is called in the SentencesHelper, in the displayMenu() method.
	 */
	function choices(){
		$this->Package->recursive = -1;
		$lists = $this->SentencesList->find(
			'all', 
			array("conditions" => array("SentencesList.user_id" => $this->Auth->user('id')))
		);
		//$this->set('lists', $lists);
		return $lists;
	}
	
	
	/**
	 * Check if list belongs to current user.
	 */
	function belongsToCurrentUser($listId){
		$this->SentencesList->id = $listId;
		$list = $this->SentencesList->read();
		if($list['SentencesList']['user_id'] == $this->Auth->user('id')){
			return true;
		}else{
			return false;
		}
	}
	
}
?>
