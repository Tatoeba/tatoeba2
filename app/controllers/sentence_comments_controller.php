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
    var $uses = array("SentenceComment","Sentence");	
	var $helpers = array('Comments','Sentences', 'Languages', 'Navigation', 'Html');
	var $components = array ('GoogleLanguageApi', 'Permissions', 'Mailer');
	
	function beforeFilter() {
	    parent::beforeFilter(); 
		
		// setting actions that are available to everyone, even guests
	    $this->Auth->allowedActions = array('index', 'save', 'show','latest');
	}
	
	/**
	 * Display 10 latest comments for each language.
	 */
	function index(){
		$this->set(
			'sentenceComments'
			, $this->SentenceComment->getLatestCommentsInEachLanguage()
		);
	}

	/**
	 * Display comments for given sentence.
	 */
	function show($sentenceId){

        Sanitize::paranoid($sentenceId);
        // redirect to sentences/show
        // we don't remove the method to keep compatibily with previous google indexing
        $this->redirect(array("controller" => "sentences" , "action" => "show" ,  $sentenceId  ),301 );
	}
	
	/**
	 * Save new comment.
	 */
	function save(){
        Sanitize::html($this->data['SentenceComment']['text']);
		if(!empty($this->data['SentenceComment']['text'])){
			// detecting language
			$this->GoogleLanguageApi->text = $this->data['SentenceComment']['text'];
			$response = $this->GoogleLanguageApi->detectLang();
			
			$this->data['SentenceComment']['user_id'] = $this->Auth->user('id');
			$this->data['SentenceComment']['lang'] = $this->GoogleLanguageApi->google2TatoebaCode($response['language']);
			
			if($this->SentenceComment->save($this->data)){	
				$sentenceId = $this->data['SentenceComment']['sentence_id'];
				$participants = $this->SentenceComment->getEmailsFromComments($sentenceId);
				$sentenceOwner = $this->SentenceComment->getEmailFromSentence($sentenceId);
				
				if($sentenceOwner != null){
					$participants[] = $sentenceOwner;
				}
				
				// send message to the other participants of the thread
				if(count($participants) > 0){
					foreach($participants as $participant){
						if($participant != $this->Auth->user('email')){
							// prepare message
							$subject = 'Tatoeba - Comment on sentence : ' . $this->data['SentenceComment']['sentence_text'];
							if($participant == $sentenceOwner){
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
				}
				$this->flash(
					__('Your comment has been saved.',true), 
					'/sentence_comments/show/'.$this->data['SentenceComment']['sentence_id']
				);
			}
		}
	}
	
	/**
	 * Return 5 latest comments.
	 * Called in requestAction() on homepage.
	 */
	function latest() {
		return $this->SentenceComment->getLatestComments(5);
	}

}
?>
