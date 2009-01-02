<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $helpers = array('Html', 'Form');
	var $components = array ('Mailer');

	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->autoRedirect = false;
		
		// setting actions that are available to everyone, even guests
		// no need to allow login
		$this->Auth->allowedActions = array('logout','register','new_password', 'my_profile', 'save_profile', 'confirm_registration');
		//$this->Auth->allowedActions = array('*');
	}

	
	function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
		
		$this->initDB();
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid User.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->User->create();
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The User has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The User could not be saved. Please, try again.', true));
			}
		}
		$groups = $this->User->Group->find('list');
		$this->set(compact('groups'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid User', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The User has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The User could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}
		$groups = $this->User->Group->find('list');
		$this->set(compact('groups'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for User', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->User->del($id)) {
			$this->Session->setFlash(__('User deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	
	
	function login() {
		//-- code inside this function will execute only when autoRedirect was set to false (i.e. in a beforeFilter).
		if ($this->Auth->user()) {
			if (!empty($this->data) AND $this->data['User']['rememberMe']) {
				$cookie = array();
				$cookie['username'] = $this->data['User']['username'];
				$cookie['password'] = $this->data['User']['password'];
				$this->Cookie->write('Auth.User', $cookie, true, '+2 weeks');
				unset($this->data['User']['rememberMe']);
			}
			$this->redirect($this->Auth->redirect());
		}
	}

	
	function logout(){  
		$this->Cookie->del('Auth.User'); // delete cookie when logout
		$this->Session->setFlash('Logout');  
		$this->redirect($this->Auth->logout());  
	}
	
	function register(){
		if (!empty($this->data)) {
			$nonHashedPassword = $this->data['User']['password'];
			if ($this->data['User']['password'] == $this->Auth->password($this->data['User']['password_confirm'])) {
				$this->User->create();
				$this->data['User']['since'] = date("Y-m-d H:i:s");
				$this->data['User']['group_id'] = User::LOWEST_TRUST_GROUP_ID + 1;
				if($this->User->save($this->data)){
					$pass = $this->Auth->password($this->data['User']['password']);
					$token = $this->Auth->password($pass.$this->data['User']['since'].$this->data['User']['username']);
					// prepare message
					$subject = __('Tatoeba registration',true);
					$message = sprintf(__('Dear %s,',true), $this->data['User']['username'])
						. "\n\n" 
						. __('Welcome to Tatoeba and thank you for your interest in this project!',true)
						. "\n\n"
						. __('You can validate your registration by clicking on this link :',true)
						. "\n"
						. 'http://' . $_SERVER['HTTP_HOST'] . '/users/confirm_registration/' . $this->User->id . '/' . $token;
					
					// send email with new password
					$this->Mailer->to = $this->data['User']['email'];
					$this->Mailer->toName = '';
					$this->Mailer->subject = $subject;
					$this->Mailer->message = $message;
					$this->Mailer->send();
					
					$this->flash(
						__('Thank you for registering. To validate your registration, 
						click on the link in the email that has been sent to you.',true), 
						'/users/login'
					);
				}else{
					$this->data['User']['password'] = '';
				}
			}else{
				$this->data['User']['password'] = '';
				$this->data['User']['password_confirm'] = '';
				$this->set('error', __('Passwords do not match',true));
			}
		}
	}
	
	function confirm_registration($id, $token){
		$this->User->id = $id;
		$user = $this->User->read();
		
		$toHash = $this->Auth->password($user['User']['password']).$user['User']['since'].$user['User']['username'];
		$correctToken = $this->Auth->password($toHash);
		
		if($user['User']['group_id'] < User::LOWEST_TRUST_GROUP_ID + 1){
			$msg = __('Your registration is already validated.',true);
		}else if($token == $correctToken){
			$this->data['User']['id'] = $id;
			$this->data['User']['group_id'] = User::LOWEST_TRUST_GROUP_ID;
			if($this->User->save($this->data)){
				// update aro table
				$aro = new Aro();
				$data = $aro->findByForeignKey($id);
				$data['Aro']['parent_id'] = User::LOWEST_TRUST_GROUP_ID;
				$this->Acl->Aro->save($data);
				
				$msg = __('Your registration has been validated.',true);
			}else{
				$msg = __('A problem occured. Your registration could not be validated.',true);
			}
		}else{
			$msg = __('Non valid registration link.',true);
		}
		
		$this->flash($msg,'/users/login');
	}
	
	function new_password(){
		if (!empty($this->data)) {
			$user = $this->User->findByEmail($this->data['User']['email']);
			
			// check if user exists, if so :
			if($user){
				$newPassword = $this->User->generate_password();
				
				// data to save
				$this->data['User']['id'] = $user['User']['id'];
				$this->data['User']['password'] = $this->Auth->password($newPassword);
				
				if($this->User->save($this->data)){ // if saved
					// prepare message
					$subject = __('Tatoeba, new password',true);
					$message = __('Your login : ',true)
						. $user['User']['username']
						. "\n" 
						. __('Your new password : ',true)
						. $newPassword;
					
					// send email with new password
					$this->Mailer->to = $this->data['User']['email'];
					$this->Mailer->toName = '';
					$this->Mailer->subject = $subject;
					$this->Mailer->message = $message;
					$this->Mailer->send();
					
					$this->flash(
						__('Your new password has been sent at ',true) . $this->data['User']['email'],
						'/users/login'
					);
				}
			}else{
				$this->flash(
					__('There is no registered user with this email : ',true) . $this->data['User']['email'],
					'/users/new_password'
				);
			}
		}
	}
	
	function my_profile(){
		$id = $this->Auth->user('id');
		$this->set('user', $this->User->read(null, $id));
	}
	
	function save_profile(){
		$this->User->id = $this->Auth->user('id');
		$user = $this->User->read();
		$hashedPass = $this->Auth->password($this->data['User']['old_password']);
		
		$flashMsg = '';
		$savePass = false;
		$saveEmail = false;
		
		if($user['User']['password'] == $hashedPass){
			$this->data['User']['password'] = $this->Auth->password($this->data['User']['new_password']);
			$flashMsg .= __('New password has been saved.',true);
			$flashMsg .= ' ';
			$savePass = true;
		}
		
		if($user['User']['email'] != $this->data['User']['email']){
			$flashMsg .= __('Email saved : ', true);
			$flashMsg .= $this->data['User']['email'];
			$saveEmail = true;
		}
		
		if($savePass OR $saveEmail){
			if($this->User->save($this->data)){
				$this->flash(
					$flashMsg,
					'/users/my_profile/'
				);
			}else{
				$this->flash(
					__('No changes have been applied.',true),
					'/users/my_profile/'
				);
			}
		}else{
			$this->flash(
				__('No changes have been applied.',true),
				'/users/my_profile/'
			);
		}
	}
	
	// temporary function to grant/deny access
	function initDB() {
	    $group =& $this->User->Group;
		
	    //Allow admins to everything
	    $group->id = 1;     
	    $this->Acl->allow($group, 'controllers');
	 
	    //Permissions for moderators
	    $group->id = 2;
		$this->Acl->deny($group, 'controllers');
		$this->Acl->allow($group, 'controllers/SuggestedModifications');
		$this->Acl->allow($group, 'controllers/SentenceComments');
		$this->Acl->allow($group, 'controllers/Sentences');
		$this->Acl->allow($group, 'controllers/Sentences/edit');
		
		//Permissions for trusted_users
		$group->id = 3;
		$this->Acl->deny($group, 'controllers');
		$this->Acl->allow($group, 'controllers/SentenceComments/add');
		$this->Acl->allow($group, 'controllers/Sentences/edit');
		
	    //Permissions for users
	    $group->id = 4;
		$this->Acl->deny($group, 'controllers');
		$this->Acl->allow($group, 'controllers/SentenceComments/add');
	}
}
?>