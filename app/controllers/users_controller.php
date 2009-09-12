<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $helpers = array('Html', 'Form', 'Date', 'Logs', 'Sentences', 'Navigation');
	var $components = array ('Mailer', 'Captcha', 'RememberMe');
	var $paginate = array('limit' => 50, 'order' => array('since' => 'desc')); 

	function beforeFilter() {
		parent::beforeFilter();
		// setting actions that are available to everyone, even guests
		// no need to allow login
		$this->Auth->allowedActions = array('all', 'search', 'show', 'logout','register','new_password', 'confirm_registration', 'resend_registration_mail', 'captcha_image', 'followers', 'following', 'favoriting', 'check_username','check_email');
		//$this->Auth->allowedActions = array('*');
	}

	function updateAros(){
		$this->User->recursive = 0;
		$users = $this->User->find('all');
		foreach($users as $user){
			$aro = new Aro();
			$data = $aro->find("first", array( "conditions" => array("foreign_key" => $user['User']['id'], "model" => "User")));
			$data['Aro']['parent_id'] = $user['User']['group_id'];
			$this->Acl->Aro->save($data);
		}
	}
	
	function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
		
		//$this->initDB();
		//$this->updateAros();
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Invalid User');
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				// update aro table
				$aro = new Aro();
				$data = $aro->find("first", array( "conditions" => array("foreign_key" => $this->data['User']['id'], "model" => "User")));
				$data['Aro']['parent_id'] = $this->data['User']['group_id'];
				$this->Acl->Aro->save($data);
				
				$this->Session->setFlash('The User has been saved');
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash('The User could not be saved. Please, try again.');
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
			$this->Session->setFlash('Invalid id for User');
			$this->redirect(array('action'=>'index'));
		}
		if ($this->User->del($id)) {
			$this->Session->setFlash('User deleted');
			$this->redirect(array('action'=>'index'));
		}
	}
	
	function login()  
	{  
		if(!$this->Auth->user()){  
			return;  
		}
		
		$data['User']['id'] = $this->Auth->user('id');
		$data['User']['last_time_active'] = time();
		$this->User->save($data);
		
		if($this->Auth->user('group_id') == 5)	{
			$this->flash(__('Your account is not validated yet. You will not be able to add sentences, translate or post comments. To validate it, click on the link in the email that has been sent to you during your registration. You can have this email resent to you.', true), '/users/resend_registration_mail/');
		}
		
		if(empty($this->data)){  
			$this->redirect($this->Auth->redirect());  
		}  
		
		if(empty($this->data['User']['rememberMe'])){
			$this->RememberMe->delete();  
		}else{
			$this->RememberMe->remember($this->data['User']['username'], $this->data['User']['password']);  
		}  
		
		unset($this->data['User']['rememberMe']);  
		$this->redirect($this->Auth->redirect());  
	}

	function logout()  
	{  
		$this->RememberMe->delete();  
		$this->redirect($this->Auth->logout());  
	}  
	
	function register(){
		if (!empty($this->data)) {
			
			$this->User->create();
			$this->data['User']['since'] = date("Y-m-d H:i:s");
			$this->data['User']['group_id'] = User::LOWEST_TRUST_GROUP_ID + 1;
			$nonHashedPassword = $this->data['User']['password'];
			
			$this->User->set( $this->data );
			if($this->User->validates()){
				if($this->Captcha->check($this->data['User']['captcha'], true) AND $this->User->save($this->data)){
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
						__('Thank you for registering. To validate your registration, click on the link in the email that has been sent to you.',true), 
						'/users/login'
					);
				}else{
					$this->data['User']['password'] = '';
					$this->data['User']['captcha'] = '';
					$this->Session->setFlash(__('The code you entered did not match with the image, please try again.',true));
				}	
			}else{
				$this->data['User']['password'] = '';
				$this->data['User']['captcha'] = '';
			}
		}
	}
	
	function resend_registration_mail(){
		if(!empty($this->data)) {
			$user = $this->User->findByEmail($this->data['User']['email']);
			
			if($user){
				if($user['User']['group_id'] == 5){
					$toHash = $this->Auth->password($user['User']['password']).$user['User']['since'].$user['User']['username'];
					$token = $this->Auth->password($toHash);
					
					// prepare message
					$subject = __('Tatoeba registration',true);
					$message = sprintf(__('Dear %s,',true), $user['User']['username'])
						. "\n\n" 
						. __('Welcome to Tatoeba and thank you for your interest in this project!',true)
						. "\n\n"
						. __('You can validate your registration by clicking on this link :',true)
						. "\n"
						. 'http://' . $_SERVER['HTTP_HOST'] . '/users/confirm_registration/' . $user['User']['id'] . '/' . $token;
					
					// send email with new password
					$this->Mailer->to = $user['User']['email'];
					$this->Mailer->toName = '';
					$this->Mailer->subject = $subject;
					$this->Mailer->message = $message;
					$this->Mailer->send();
					
					$this->Session->setFlash(__('The registration email has been resent.',true));  
					$this->redirect($this->Auth->logout());  
				}else{
					$this->flash(
						__('Your registration has already been validated. Try to login again.', true),
						'/users/resend_registration_mail'
					);
				}
			}else{
				$this->flash(
					__('There is no user with this email : ',true) . $user['User']['email'],
					'/users/resend_registration_mail'
				);
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
				$data = $aro->find("first", array( "conditions" => array("foreign_key" => $id, "model" => "User")));
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
	
	function settings(){
		$id = $this->Auth->user('id');
		$this->User->recursive = 0;
		$this->set('user', $this->User->read(null, $id));
	}
	
	function save_password(){
		$this->User->id = $this->Auth->user('id');
		$this->User->recursive = 0;
		$user = $this->User->read();
		$hashedPass = $this->Auth->password($this->data['old_password']['passwd']);
		
		if($user['User']['password'] == $hashedPass && $this->data['new_password']['passwd'] == $this->data['new_password2']['passwd']){
			$this->data['User']['password'] = $this->Auth->password($this->data['new_password']['passwd']);
			
			if($this->User->save($this->data)){
				$flashMsg = __('New password has been saved.',true);
			}else{
				$flashMsg = __('An error occured while saving.',true);
			}
		}else{
			$flashMsg = __('Wrong old password or new password inputs do not match.',true);
		}
		
		$this->flash($flashMsg,	'/users/settings/');
	}
	
	function save_email(){
		$this->User->id = $this->Auth->user('id');
		if($this->User->save($this->data)){
			$flashMsg  = __('Email saved : ', true);
			$flashMsg .= $this->data['User']['email'];
		}else{
			$flashMsg  = __('An error occured while saving. The email you have entered is either not correct or is already used.', true);
		}
		
		$this->flash($flashMsg,	'/users/settings/');
	}
	
	function save_options(){
		$this->User->id = $this->Auth->user('id');
		if($this->User->save($this->data)){
			$flashMsg = __('Options saved.',true);
		}else{
			$flashMsg = __('New password has been saved.',true);
		}
		
		$this->flash($flashMsg,	'/users/settings/');
	}
	
	function search(){
		$user = $this->User->findByUsername($this->data['User']['username']);
		if($user != null){
			$id = ($user['User']['id'] < 1) ? 1 : $user['User']['id'];
			$this->redirect(array("action" => "show", $id));	
		}else{
			$this->flash(__('No user with this username : ', true).$this->data['User']['username'], '/users/all/');
		}
	}
	
	function show($id){
		$this->User->recursive = 1;
		if($id == "random"){
			$user = $this->User->find('first', array('conditions' => 'User.group_id < 5', 'order' => 'RAND()', 'limit' => 1));
		}else{
			$user = $this->User->findById($id);
		}
		
		if($user != null){
			$this->set('user', $user);
			
			// check if we can follow that user or not (we can if we're NOT already following the user, or if the user is NOT ourself)
			if($user['User']['id'] != $this->Auth->user('id')){
				$can_follow = true;
				foreach($user['Follower'] as $follower){
					if($follower['id'] == $this->Auth->user('id')){
						$can_follow = false;
					}
				}
				$this->set('can_follow', $can_follow);
			}
		}else{
			$this->Session->write('last_user_id', $id);
			$this->flash(__('No user with this id : ', true).$id, '/users/all/');
		}
	}
	
	function all(){		
		$this->User->recursive = 0;
		$this->set('users', $this->paginate(array('User.group_id < 5')));
	}
	/*
	function my_tatoeba(){
		$this->User->recursive = 1;
		$this->User->id = $this->Auth->user('id');
		$this->set('user', $this->User->read());
	}*/
	
	function captcha_image(){
	    Configure::write('debug',0);
	    $this->layout = null;
	    $this->Captcha->image();
	} 
	
	function followers($id){
		$this->User->unbindModel(
			array(
				'belongsTo' => array('Group'),
				'hasMany' => array('SentenceComments', 'Contributions', 'Sentences'),
				'hasAndBelongsToMany' => array('Following')
			)
		);
		$this->User->id = $id;
		$this->set('followers', $this->User->read());
	}
	
	function following($id){
		$this->User->unbindModel(
			array(
				'belongsTo' => array('Group'),
				'hasMany' => array('SentenceComments', 'Contributions', 'Sentences'),
				'hasAndBelongsToMany' => array('Follower')
			)
		);
		$this->User->id = $id;
		$this->set('following', $this->User->read());	
	}
	

	function favoriting($id){

		$this->User->unbindModel(
			array(
				'belongsTo' => array('Group'),
				'hasMany' => array('SentenceComments', 'Contributions', 'Sentences'),
				'hasAndBelongsToMany' => array('Following' , 'Follower')
			)
		);

		$this->User->id = $id;
		$this->set('favorites', $this->User->read());	
		

	}
	function start_following(){
		$follower_id = $this->Auth->user('id');
		$user_id = $_POST['user_id'];
		$this->User->habtmAdd('Follower', $user_id, $follower_id); 
	}
	
	function stop_following(){
		$follower_id = $this->Auth->user('id');
		$user_id = $_POST['user_id'];
		$this->User->habtmDelete('Follower', $user_id, $follower_id); 
	}
	
	/**
	 * Check if the username already exist or not.
	 */
	function check_username($username){
		Configure::write('debug',0);
		$this->User->recursive = 0;
		$user = $this->User->findByUsername($username);
		if ($user){
			$this->set('data' , true );
		}else{
			$this->set('data' , false);
		}
	}
	
	/**
	 * Check if the email already exist or not.
	 */
	function check_email($email){
		Configure::write('debug',0);
		$this->User->recursive = 0;
		$data = $this->User->findByEmail($email);
		if ($data){
			$this->set('data' , true );
		}else{
			$this->set('data' , false);
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
		$this->Acl->allow($group, 'controllers/Users/my_tatoeba');
		$this->Acl->allow($group, 'controllers/Users/settings');
		$this->Acl->allow($group, 'controllers/Users/save_email');
		$this->Acl->allow($group, 'controllers/Users/save_password');
		$this->Acl->allow($group, 'controllers/Users/save_options');
		$this->Acl->allow($group, 'controllers/Users/start_following');
		$this->Acl->allow($group, 'controllers/Users/favoriting');
		$this->Acl->allow($group, 'controllers/Users/stop_following');
		$this->Acl->allow($group, 'controllers/Favorites/add_favorite');
		
		//Permissions for trusted_users
		$group->id = 3;
		$this->Acl->deny($group, 'controllers');
		$this->Acl->allow($group, 'controllers/SentenceComments/add');
		$this->Acl->allow($group, 'controllers/Sentences');
		$this->Acl->deny($group, 'controllers/Sentences/delete');
		$this->Acl->allow($group, 'controllers/Users/my_tatoeba');
		$this->Acl->allow($group, 'controllers/Users/settings');
		$this->Acl->allow($group, 'controllers/Users/save_email');
		$this->Acl->allow($group, 'controllers/Users/save_password');
		$this->Acl->allow($group, 'controllers/Users/save_options');
		$this->Acl->allow($group, 'controllers/Users/start_following');
		$this->Acl->allow($group, 'controllers/Users/favoriting');
		$this->Acl->allow($group, 'controllers/Users/stop_following');
		$this->Acl->allow($group, 'controllers/Favorites/add_favorite');
		
	    //Permissions for users
	    $group->id = 4;
		$this->Acl->deny($group, 'controllers');
		$this->Acl->allow($group, 'controllers/SentenceComments/add');
		$this->Acl->allow($group, 'controllers/Sentences');
		$this->Acl->deny($group, 'controllers/Sentences/delete');
		$this->Acl->allow($group, 'controllers/Users/my_tatoeba');
		$this->Acl->allow($group, 'controllers/Users/settings');
		$this->Acl->allow($group, 'controllers/Users/save_email');
		$this->Acl->allow($group, 'controllers/Users/save_password');
		$this->Acl->allow($group, 'controllers/Users/save_options');
		$this->Acl->allow($group, 'controllers/Users/start_following');
		$this->Acl->allow($group, 'controllers/Users/favoriting');
		$this->Acl->allow($group, 'controllers/Users/stop_following');
		$this->Acl->allow($group, 'controllers/Favorites/add_favorite');
		$this->Acl->allow($group, 'controllers/Favorites/remove_favorite');
	}
}
?>
