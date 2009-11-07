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
		$this->SentencesList->id = $id;
		$this->set('list', $this->SentencesList->read());
	}
	
	
	/**
	 * Create a list.
	 */
	function add(){
		if(!empty($this->data)){
			$this->data['SentencesList']['user_id'] = $this->Auth->user('id');
			$this->SentencesList->save($this->data);
		}
		$this->redirect(array("action"=>"index"));
	}
	
	
	/**
	 * Saves the new name of a list. 
	 * Used in AJAX request from sentences_lists.edit_name.js
	 */
	function save_name(){
		Configure::write('debug', 0);
		if(isset($_POST['value']) AND isset($_POST['id'])){
			$this->SentencesList->id = $_POST['id'];
			$list['SentencesList']['name'] = $_POST['value'];
			if($this->SentencesList->save($list)){
				$this->set('result', $_POST['value']);
			}else{
				$this->set('result', 'error');
			}
		}else{
			$this->set('result', 'error');
		}
	}
	
	
	/**
	 * Delete list.
	 */
	function delete($listId){
		$this->SentencesList->delete($listId);
		$this->redirect(array("action" => "index"));
	}
	
	/**
	 * Add sentence to a list.
	 * TODO : also check that user is actually the owner of the list.
	 */
	function add_sentence_to_list($sentenceId, $listId){
		Configure::write('debug', 0);
		$this->set('s', $sentenceId);
		$this->set('l', $listId);
		if($this->SentencesList->habtmAdd('Sentence' , $listId, $sentenceId)){
			$this->set('listId', $listId);
		}else{
			$this->set('listId', 'error');
		}
	}
	
	
	/**
	 * Create a new list and add a sentence to that list.
	 */
	function add_sentence_to_new_list($sentenceId, $listName){
		Configure::write('debug', 0);
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
	
	
	/**
	 * Remove sentence from a list.
	 * TODO : check that user is actually owner of the list.
	 * NOTE : it would be nice to have an "undo" for this action...
	 */
	function remove_sentence_from_list($sentenceId, $listId){
		Configure::write('debug', 0);
		if($this->SentencesList->habtmDelete('Sentence' , $listId, $sentenceId)){
			$this->set('removed', true);
		}
	}
	
	
	/**
	 * Displays the lists of a specific user.
	 */
	function of_user($user_id){
		$lists = $this->SentencesList->findAllByUserId($user_id);
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
	
}
?>