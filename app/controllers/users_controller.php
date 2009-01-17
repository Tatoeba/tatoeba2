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
		$this->Auth->allowedActions = array('*');
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
			if($this->Auth->user('group_id') == 5){
				$this->flash(__('Your account is not validated yet. You will not be able to add sentences, translate or post comments. To validate it, click on the link in the email that has been sent to you during your registration. You can have this email resent to you.', true), '/users/resend_registration_mail/');
			}else{
				$this->redirect($this->Auth->redirect());
			}
		}
	}

	
	function logout(){  
		$this->Cookie->del('Auth.User'); // delete cookie when logout
		$this->Session->setFlash('You are logged out.');  
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
						__('Thank you for registering. To validate your registration, click on the link in the email that has been sent to you.',true), 
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
	
	function send_newsletter(){
		$this->User->recursive = -1;
		$users = $this->User->find('all');
		
		// prepare message
		$subject['en'] = 'Tatoeba - New version';
		$message['en'] = "Dear %s,\n"
			."\n"
			."You are receiving this email because you have, at some point of your life,"
			." registered in Tatoeba Project (http://tatoeba.fr/). Perhaps it was at a time"
			." when the project was not called Tatoeba yet, so if you're not sure what "
			."this project is about, it aims to collect many, many, many sentences "
			."translated in several languages.\n"
			."\n"
			."Today, I am glad to announce something that may not mean a lot to you, "
			."but means a lot to me : a new version of Tatoeba is now available!\n"
			."\n"
			."For this happy event, your password has been changed and you will have "
			."to re-confirm your registration by clicking on this link :\n"
			."http://tatoeba.fr/eng/users/confirm_registration/%s\n"
			."\n"
			."Once you have re-confirmed your registration, you can log in with the "
			."following login and password.\n"
			."login : %s\n"
			."password : %s\n"
			."\n"
			."And once you have logged in, maybe take a few minutes to add a sentence "
			."(any sentence that comes through your mind) or to translate sentences : "
			."http://tatoeba.fr/eng/sentences/contribute\n"
			."\n"
			."For people who would like more details about this new version and what's "
			."going to happen with the data in the old version, you may find your answers "
			."here : http://blog.tatoeba.org\n"
			."\n"
			."If not, you can always contact me and ask me questions.\n"
			."\n"
			."Thank you.\n"
			."\n"
			."Trang";
			
		$subject['fr'] = 'Tatoeba - Nouvelle version';
		$message['fr'] = "Bonjour %s,\n"
			."\n"
			."Vous recevez cet email car un jour dans votre vie vous vous êtes "
			."inscrit sur Tatoeba Project (http://tatoeba.fr). "
			."Peut-être était-ce même à un période où le projet ne s'appelait pas "
			."encore Tatoeba, donc si vous n'êtes pas sûr de savoir en quoi consite "
			."ce projet, son but est de collecter beaucoup, beaucoup, beaucoup de "
			."phrases traduites dans plusieurs langues.\n"
			."\n"
			."Aujourd'hui, j'ai le plaisir de vous annoncer quelque chose qui "
			."vous importe peut-être peu, mais qui m'importe beaucoup : une "
			."nouvelle version de Tatoeba est maintenant disponible!\n"
			."\n"
			."Pour cet heureux évènement, votre mot de passe a été modifié et "
			."vous allez devoir re-confirmer votre inscription en cliquant sur ce lien :\n"
			."http://tatoeba.fr/fre/users/confirm_registration/%s/%s\n"
			."\n"
			."Une fois votre inscription re-confirmée, vous pourrez vous connectez "
			."avec l'identifiant et le mot de passe suivant :\n"
			."nom d'utilisateur : %s\n"
			."mot de passe : %s\n"
			."\n"
			."Et une fois que vous êtes connecté(e), peut-être prendriez-vous "
			."quelques minutes pour ajouter une phrase (n'importe quelle phrase "
			."qui vous passe par la tête) ou pour traduire des phrases : "
			."http://tatoeba.fr/fre/sentences/contribute\n"
			."\n"
			."Pour ceux qui voudraient en savoir plus concernant cette nouvelle "
			."version et ce qu'il va arriver avec les données de l'ancienne version, "
			."vous pourrez sans doute trouver vos réponses ici : http://blog.tatoeba.org/ "
			."(mais c'est en anglais, sorry)\n"
			."\n"
			."Sinon, vous pouvez toujours me contacter et me poser des question.\n"
			."\n"
			."Merci.\n"
			."\n"
			."Trang";
		
		foreach($users as $user){
			// generating new password
			$newPassword = $this->User->generate_password();
			
			// data to save
			$this->data['User']['id'] = $user['User']['id'];
			$this->data['User']['password'] = $this->Auth->password($newPassword);
			
			if($this->User->save($this->data)){
				// update aro table
				$aro = new Aro();
				$data['Aro']['model'] = 'User';
				$data['Aro']['foreign_key'] = $user['User']['id'];
				$data['Aro']['parent_id'] = 5;
				$aro->save($data);
				
				// token for re-confirmation of registration
				$toHash = $this->Auth->password($this->data['User']['password']).$user['User']['since'].$user['User']['username'];
				$correctToken = $this->Auth->password($toHash);
				
				// message in the proper language
				$lang = ($user['User']['lang'] == 'fr') ? 'fr':'en';
				$msg = sprintf($message[$lang], $user['User']['username'], $user['User']['id'], $correctToken, $user['User']['username'], $newPassword);
				
				// send email
				/*
				$this->Mailer->to = $user['User']['email'];
				$this->Mailer->toName = '';
				$this->Mailer->subject = $subject[$lang];
				$this->Mailer->message = $message;
				$this->Mailer->send();
				*/
				
				pr($subject[$lang]);
				pr($msg);
				pr('-------------');
				$logs[] = $user['User']['email'].' - sent';
			}
		}
		
		$this->set('logs', $logs);
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
		$this->Acl->allow($group, 'controllers/Sentences/add');
		$this->Acl->allow($group, 'controllers/Sentences/translate');
		$this->Acl->allow($group, 'controllers/Sentences/save_translation');
		$this->Acl->allow($group, 'controllers/Sentences/contribute');
		
		//Permissions for trusted_users
		$group->id = 3;
		$this->Acl->deny($group, 'controllers');
		$this->Acl->allow($group, 'controllers/SentenceComments/add');
		$this->Acl->allow($group, 'controllers/Sentences/edit');
		$this->Acl->allow($group, 'controllers/Sentences/add');
		$this->Acl->allow($group, 'controllers/Sentences/translate');
		$this->Acl->allow($group, 'controllers/Sentences/save_translation');
		$this->Acl->allow($group, 'controllers/Sentences/contribute');
		
	    //Permissions for users
	    $group->id = 4;
		$this->Acl->deny($group, 'controllers');
		$this->Acl->allow($group, 'controllers/SentenceComments/add');
		$this->Acl->allow($group, 'controllers/Sentences/edit');
		$this->Acl->allow($group, 'controllers/Sentences/add');
		$this->Acl->allow($group, 'controllers/Sentences/translate');
		$this->Acl->allow($group, 'controllers/Sentences/save_translation');
		$this->Acl->allow($group, 'controllers/Sentences/contribute');
	}
}
?>