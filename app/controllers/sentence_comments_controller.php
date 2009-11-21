<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

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

class SentenceCommentsController extends AppController {
	var $name = 'SentenceComments';
	
	var $helpers = array('Comments','Sentences', 'Languages', 'Navigation', 'Html');
	var $components = array ('GoogleLanguageApi', 'Permissions', 'Mailer');
	
	var $langs = array('en', 'fr', 'jp', 'es', 'de');
	
	function beforeFilter() {
	    parent::beforeFilter(); 
		
		// setting actions that are available to everyone, even guests
	    $this->Auth->allowedActions = array('index', 'save', 'show','latest');
	}
	
	function index(){
		$sentenceComments = array();
		
		$this->SentenceComment->recursive = 1;
		
		foreach($this->langs as $lang){
			$sentenceComments[$lang] = $this->SentenceComment->find(
				"all",
				array( 
					"conditions" => array("SentenceComment.lang" => $lang),
					"limit"=> 10,
					"order" => "SentenceComment.created DESC"
				)
			);
		}
		
		$sentenceComments['unknown'] = $this->SentenceComment->find(
			"all",
			array( 
				"conditions" => array("NOT" => array("SentenceComment.lang" => $this->langs)),
				"limit"=> 10,
				"order" => "SentenceComment.created DESC"
			)
		);
		
		$this->set('sentenceComments', $sentenceComments);
	}

	
	function show($sentenceId){
        Sanitize::paranoid($sentenceId);
        $s = new Sentence();
		$s->id = $sentenceId;		
		$s->recursive = 2;
		$sentence = $s->read();
		
		$this->set('sentence_id', $sentenceId);
		
		if($sentence != null){
			$this->set('sentenceExists', true);
			$this->set('sentence', $sentence);
		}else{
			$this->set('sentenceExists', false);
		}
		
		$sentenceComments = $this->SentenceComment->find('all', 
			array(
				'conditions' => array('SentenceComment.sentence_id' => $sentenceId),
				'order' => 'SentenceComment.created'
			)
		);
		$this->set('sentenceComments', $sentenceComments);
		
		// checking which options user can access to
		$specialOptions = $this->Permissions->getSentencesOptions($sentence, $this->Auth->user('id'));
		$this->set('specialOptions',$specialOptions);
		
		
		if($this->Auth->user('id')){
			// saving parent email in session variable
			$this->Session->write('user_email', $sentence['User']['email']);
			
			// saving participants in session variable so we can send notification to them
			if($sentence['User']['email'] != '' AND $sentence['User']['email'] != $this->Auth->user('email') AND $sentence['User']['send_notifications'] == 1){
				$participants = array($sentence['User']['email']);
			}else{
				$participants = array();
			}
			foreach($sentenceComments as $comment){
				if(!in_array($comment['User']['email'],$participants) AND $comment['User']['email'] != $this->Auth->user('email') AND $comment['User']['send_notifications'] == 1){
					$participants[] = $comment['User']['email'];
				}
			}
			$this->Session->write('participants', $participants);
		}
	}
	
	function save(){
        Sanitize::html($this->data['SentenceComment']['text']);
		if(!empty($this->data['SentenceComment']['text'])){
			// detecting language
			$this->GoogleLanguageApi->text = $this->data['SentenceComment']['text'];
			$response = $this->GoogleLanguageApi->detectLang();
			
			$this->data['SentenceComment']['user_id'] = $this->Auth->user('id');
			$this->data['SentenceComment']['lang'] = $this->GoogleLanguageApi->google2TatoebaCode($response['language']);
			
			if($this->SentenceComment->save($this->data)){	
				// send message to the other participants of the thread
				$participants = $this->Session->read('participants');
				if(count($participants) > 0){
					foreach($participants as $participant){
						// prepare message
						$subject = 'Tatoeba - Comment on sentence : ' . $this->data['SentenceComment']['sentence_text'];
						if($participant == $this->Session->read('user_email')){
							$msgStart = sprintf('%s has posted a comment on one of your sentences.', $this->Auth->user('username'));
						}else{
							$msgStart = sprintf('%s has posted a comment on a sentence where you also posted a comment.', $this->Auth->user('username'));
						}
						$message = $msgStart
							. "\n"
							.'http://'.$_SERVER['HTTP_HOST'] .'/sentence_comments/show/'.$this->data['SentenceComment']['sentence_id'].'#comments'
							. "\n\n- - - - - - - - - - - - - - - - -\n\n" 
							. $this->data['SentenceComment']['text']
							. "\n\n- - - - - - - - - - - - - - - - -\n\n";
							
						// send notification
						$this->Mailer->to = $participant;
						$this->Mailer->toName = '';
						$this->Mailer->subject = $subject;
						$this->Mailer->message = $message;
						$this->Mailer->send();
					}
				}
				$this->flash(
					__('Your comment has been saved.',true), 
					'/sentence_comments/show/'.$this->data['SentenceComment']['sentence_id']
				);
			}
		}
	}
	
	
	function latest() {
		return $this->SentenceComment->find('all', array('order' => 'SentenceComment.created DESC', 'limit' => 5));
	}

}
?>
