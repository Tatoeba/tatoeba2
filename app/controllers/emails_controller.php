<?php
class EmailsController extends AppController {

	var $components = array ('Mailer'); // 'Mailer','comp2'  if Multiple 
	
	function beforeFilter() {
	    parent::beforeFilter(); 
		
		// setting actions that are available to everyone, even guests
	    $this->Auth->allowedActions = array('*');
	}
	
	function send(){
		$this->Mailer->to = 'tranglich@gmail.com';
		$this->Mailer->toName = '';
		$this->Mailer->subject = 'Another test';
		$this->Mailer->message = 'I really want to go to bed';
		$this->Mailer->send();
	}
}
?>