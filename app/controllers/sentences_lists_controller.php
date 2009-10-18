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
	var $helpers = array('Sentences');
	
	
	function beforeFilter() {
	    parent::beforeFilter();
		
		// setting actions that are available to everyone, even guests
	    $this->Auth->allowedActions = array('*');
	}
	
	function index(){
		$lists = $this->SentencesList->find('all');
		$this->set('lists', $lists);
	}
	
	function show($id){
		$this->SentencesList->id = $id;
		$this->set('list', $this->SentencesList->read());
	}
	
	function add(){
		if(!empty($this->data)){
			$this->data['SentencesList']['user_id'] = $this->Auth->user('id');
			$this->SentencesList->save($this->data);
		}
		$this->redirect(array("action"=>"index"));
	}
	
	function edit(){
	}
	
	function of_user($user_id){
		
	}
	
	function choices(){
		$this->Package->recursive = -1;
		$lists = $this->SentencesList->find(
			'all', 
			array("conditions" => array("SentencesList.user_id" => $this->Auth->user('id')))
		);
		//$this->set('lists', $lists);
		return $lists;
	}
	
	function add_sentence_to_list($sentence_id, $list_id){
		$this->set('s', $sentence_id);
		$this->set('l', $list_id);
		if($this->SentencesList->habtmAdd('Sentence' , $list_id, $sentence_id)){
			$this->set('saved', true);
		}
	}
	
}
?>