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

class ConversationsController extends AppController{

	var $name = 'Conversations';
	var $helpers = array('Html', 'Form', 'Pagination', 'Sentences');
	var $components = array ('Lucene');
	
	
	function beforeFilter() {
	    parent::beforeFilter();
		
		// setting actions that are available to everyone, even guests
	    $this->Auth->allowedActions = array('*');
	}
	
	function index() {
		$conversations = $this->Conversation->find('all');
		$this->set('conversations', $conversations);
	}
	
	function show($id) {
		if(!empty($id)) {
			$conversation = $this->Conversation->getWithId($id);
			$this->set('conversation', $conversation);
		} else {
			$this->redirect(array("action" => "index"));
		}
	}
	
	function add() {
		if(!empty($this->data)) {
			$nb_replies = $this->data['Conversation']['nb_replies'];
			$languages = explode(";", $this->data['Conversation']['languages']);

			$conversation_data['Conversation']['user_id'] = $this->Auth->user('id');
			$this->Conversation->save($conversation_data);
			
			foreach ($languages as $language) {
				$conversation_title = new ConversationTitle();
				$conversation_title_data['title'] = $this->data['Conversation']['title'.$language];
				$conversation_title_data['lang'] = $language;
				$conversation_title_data['conversation_id'] = $this->Conversation->id;
				$conversation_title->save($conversation_title_data);
			}
			
			for($i = 1; $i <= $nb_replies; $i++) {
				$dialog_initial_language_reply_id = false;
				foreach ($languages as $language) {
					$dialog_sentence = new Sentence();
					$dialog_sentence_data['Sentence']['text'] = $this->data['Conversation']['content'.$language.$i];
					$dialog_sentence_data['Sentence']['lang'] = $language;
					$dialog_sentence_data['Sentence']['sentence_lang'] = $language; // needed for the logs
					$dialog_sentence_data['Sentence']['user_id'] = $this->Auth->user('id');
					
					if ($dialog_initial_language_reply_id === false) {//Occured in the first iteration, in order to consider the first language as the initial one
						$dialog_sentence->save($dialog_sentence_data);
						$dialog_initial_language_reply_id = $dialog_sentence->id;
					} else {
						$dialog_sentence_data['Translation']['Translation'][] = $dialog_initial_language_reply_id;
						$dialog_sentence_data['InverseTranslation']['InverseTranslation'][] = $dialog_initial_language_reply_id;
						$dialog_sentence->save($dialog_sentence_data);
					}
					
					$dialog_reply = new ConversationsSentence();
					$dialog_reply_data['ConversationsSentence']['conversation_id'] = $this->Conversation->id;
					$dialog_reply_data['ConversationsSentence']['sentence_id'] = $dialog_sentence->id;
					$dialog_reply_data['ConversationsSentence']['speaker'] = $this->data['Conversation']['speaker'.$i];
					$dialog_reply_data['ConversationsSentence']['replies_order'] = $i;
					$dialog_reply->save($dialog_reply_data);
					
				}
			}
			

			//pr($this->data);
			//$this->data['SentencesC']['user_id'] = $this->Auth->user('id');
			//$this->Conversation->save($this->data);
		}
		$this->redirect(array("action"=>"index"));
	}
	
	function edit($param){
		$this->set('mode', 'new');
	}
	
	function new_reply($order, $dialog_languages){
		$this->layout = null;
		$this->set('order', $order);
		$this->set('dialog_languages', $dialog_languages);

	}

	function new_dialog($main_language) {
		$this->layout = null;
		$this->set('main_language', $main_language);

	}
	
	function new_dialog_language($new_language) {
		$this->layout = null;
		$this->set('new_language', $new_language);

	}
	
	function new_dialog_language_title($new_language) {
		$this->layout = null;
		$this->set('new_language', $new_language);
	}
	
}
?>