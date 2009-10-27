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

class ConversationsController extends AppController{

	var $name = 'Conversations';
	var $helpers = array('Html', 'Form', 'Sentences');
	
	
	function beforeFilter() {
	    parent::beforeFilter();
		
		// setting actions that are available to everyone, even guests
	    $this->Auth->allowedActions = array('*');
	}
	
	function index(){
		$conversations = $this->Conversation->find('all');
		$this->set('conversations', $conversations);
	}
	
	function show($id){
		$this->SentencesList->id = $id;
		$this->set('list', $this->SentencesList->read());
	}
	
	function add(){
		if(!empty($this->data)){
			$nb_replies = $this->data['Conversation']['nb_replies'];
			$lang_from = $this->data['Conversation']['lang_from'];
			$lang_to = $this->data['Conversation']['lang_to'];
			
			
			for($i = 1; $i <= $nb_replies; $i++) {
				$sentence_to_add = new Sentence();
				$translation_to_add = new Sentence();
				
				$sentence_to_add_data['Sentence']['text'] = $this->data['Conversation']['reply_from'.$i];
				$sentence_to_add_data['Sentence']['lang'] = $lang_from;
				$sentence_to_add_data['Sentence']['sentence_lang'] = $lang_from; // needed for the logs
				$sentence_to_add_data['Sentence']['user_id'] = $this->Auth->user('id');
				$translation_to_add_data['Sentence']['text'] = $this->data['Conversation']['reply_to'.$i];
				$translation_to_add_data['Sentence']['lang'] = $lang_to;
				$translation_to_add_data['Sentence']['sentence_lang'] = $lang_to; // needed for the logs
				$translation_to_add_data['Sentence']['user_id'] = $this->Auth->user('id');
	
				$sentence_to_add->save($sentence_to_add_data);
//				pr($sentence_to_add->id);
				//$translation_to_add->id = $sentence_to_add->id;
				$translation_to_add_data['Translation']['Translation'][] = $sentence_to_add->id;
				$translation_to_add_data['InverseTranslation']['InverseTranslation'][] = $sentence_to_add->id;
				$translation_to_add->save($translation_to_add_data);
				//pr($translation_to_add->id);
			}
			//pr($this->data);
			//$this->data['SentencesC']['user_id'] = $this->Auth->user('id');
			//$this->Conversation->save($this->data);
			$conversation_data['Conversation']['user_id'] = $this->Auth->user('id');
			$conversation_data['Conversation']['title'] = $this->data['Conversation']['title'];
			$this->Conversation->save($conversation_data);
		}
		$this->redirect(array("action"=>"index"));
	}
	
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
	
	function edit($param){
		$this->set('mode', 'new');


	}
	function new_reply($order, $tesr){
		$this->layout = null;
		$this->set('order', $order);

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