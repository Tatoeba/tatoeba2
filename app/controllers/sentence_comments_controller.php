<?php
class SentenceCommentsController extends AppController {
	var $name = 'SentenceComments';
	
	var $helpers = array('Comments','Sentences', 'Languages', 'Tooltip', 'Navigation', 'Html');
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
	
	function add($sentence_id){
		$sentence = new Sentence();
		$sentence->id = $sentence_id;
		$sentence->recursive = 2;
		$sentence = $sentence->read();
		$this->set('sentence', $sentence);	
		
		// checking which options user can access to
		$specialOptions = $this->Permissions->getSentencesOptions($sentence['Sentence']['user_id'], $this->Auth->user('id'));
		$this->set('specialOptions',$specialOptions);
		
		
		// saving parent email in session variable
		$this->Session->write('user_email', $sentence['User']['email']);
		
		// saving participants in session variable so we can send notification to them
		if($sentence['User']['email'] != '' AND $sentence['User']['email'] != $this->Auth->user('email')){
			$participants = array($sentence['User']['email']);
		}else{
			$participants = array();
		}
		foreach($sentence['SentenceComment'] as $comment){
			if(!in_array($comment['User']['email'],$participants) AND $comment['User']['email'] != $this->Auth->user('email')){
				$participants[] = $comment['User']['email'];
			}
		}
		$this->Session->write('participants', $participants);
	}
	
	// I don't like how 'show' is exactly the same as 'add' in the controller...
	// It's just the view that is different...
	function show($sentence_id){
		$s = new Sentence();
		$s->id = $sentence_id;		
		$s->recursive = 2;
		$sentence = $s->read();
		$this->set('sentence', $sentence);		
		
		// checking which options user can access to
		$specialOptions = $this->Permissions->getSentencesOptions($sentence['Sentence']['user_id'], $this->Auth->user('id'));
		$this->set('specialOptions',$specialOptions);
	}
	
	function save(){
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
						$subject = __('Tatoeba - Comment on sentence : ',true) . $this->data['SentenceComment']['sentence_text'];
						if($participant == $this->Session->read('user_email')){
							$msgStart = sprintf(__('%s has posted a comment one of your sentences.',true), $this->Auth->user('username'));
						}else{
							$msgStart = sprintf(__('%s has posted a comment on a sentence where you also posted a comment.',true), $this->Auth->user('username'));
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