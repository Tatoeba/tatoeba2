<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user_controller
 *
 * @author Salem
 */

App::import('Core', 'Sanitize');

class UserController extends AppController {

	var $name = 'User';

	function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allowedActions = array('*');
	}

	function index() {
		$aUser = $this->User->findById($this->Auth->user('id'));

		$this->loadModel('Country');
		$aCountries = $this->Country->findAll();

		foreach($aCountries as $aCountry){
			$aCleanCountries[$aCountry['Country']['id']] = $aCountry['Country']['name'];
		}

		// pr($aCountries);

		$this->pageTitle = 'Your profile';

		// pr($aUser);

		$this->set('countries', $aCleanCountries);
		$this->set('user', $aUser);
	}

	function profile($sUserName = null) {
		if(is_null($sUserName)){
			$this->redirect(array('action' => 'index'));
		}else{
			$bLogin = $this->Auth->user('id') ? true : false;
			// TODO: check if the username exits and then handle the error
			$aUser = $this->User->findByUsername($sUserName);
			if($aUser['User']['name'] != '')
				$this->pageTitle = sprintf(__("Profile of %s", true), $aUser['User']['name']);
			else
				$this->pageTitle = sprintf(__("%s's profile", true), $sUserName);
			// Check if his/her profile is public
			$this->set('login', $bLogin);
			$this->set('is_public', $aUser['User']['is_public']);
			$this->set('user', $aUser);
		}
	}

	function save_image() {
		if(!empty($this->data)){
			if($this->data['profile_image']['image']['error'] == UPLOAD_ERR_OK){
				if(is_uploaded_file($this->data['profile_image']['image']['tmp_name'])){
					$sFileExt = strtolower(end(explode('.', $this->data['profile_image']['image']['name'])));
					$aValidExts = array('png', 'jpg', 'jpeg', 'gif');
					$iFileSize = (int) $this->data['profile_image']['image']['size']/1024; // Size in ko

					// Check file extension
					if(!in_array($sFileExt, $aValidExts)){
						$this->Session->setFlash('Please choose GIF, JPEG or PNG image format.');

						$this->redirect(array('action' => 'index'));
					}

					// Check file size, max 1 mo?
					if($iFileSize > 1024){
						$this->Session->setFlash('Please choose an image that do net exceed 1 Mo.');

						$this->redirect(array('action' => 'index'));
					}

					$this->loadModel('User');

					$aUser = $this->User->findById($this->Auth->user('id'));

					$sTmpFile = $this->data['profile_image']['image']['tmp_name'];
					$sEmail = $aUser['User']['email'];
					$sProfilesPath = IMAGES . 'profiles' . DS;
					$sNewFile =  md5($sEmail) . '.' . $sFileExt;

					if(move_uploaded_file($sTmpFile, $sProfilesPath . $sNewFile)){
						$this->User->id = $this->Auth->user('id');
						$this->User->save(array(
							'User' => array(
								'image' => $sNewFile
							)
						));
					}
				}
			}
		}

		$this->redirect(array('action' => 'index'));
	}

	function save_description() {
		if(!empty($this->data)){

			$aToSave = array();

			if(!empty($this->data['profile_description']['description'])){
                Sanitize::html($this->data['profile_description']['description']);
				$aToSave += array(
					'description' => $this->data['profile_description']['description']
				);
			}

			if(!empty($aToSave)){
				$this->User->id = $this->Auth->user('id');

				if($this->User->save(array('User' => $aToSave))){
					$this->Session->setFlash(__('Your informations have been updated.', true));
				}else{
					$this->Session->setFlash(__('An error occured while saving. Please try again or contact us to report this.', true));
				}
			}
		}

		$this->redirect(array('action' => 'index'));
	}

	function save_basic() {
		if(!empty($this->data)){
                Sanitize::html($this->data['profile_basic']['name']);


				$aToSave = array(
					'name' => $this->data['profile_basic']['name'],
					'birthday' => date('Y-m-d', strtotime($this->data['profile_basic']['birthday'])),
					'country_id' => $this->data['profile_basic']['country']
				);

				$this->User->id = $this->Auth->user('id');

				if($this->User->save(array('User' => $aToSave))){
					$this->Session->setFlash(__('Your basic informations have been updated.', true));
				}else{
					$this->Session->setFlash(__('An error occured while saving. Please try again or contact us to report this.', true));
				}
		}

		$this->redirect(array('action' => 'index'));
	}

	function save_contact() {
		if(!empty($this->data)){
            Sanitize::html($this->data['profile_contact']['description']);
            Sanitize::html($this->data['profile_contact']['email']);
            
			$aToSave = array(
				'User' => array(
					'email' => $this->data['profile_contact']['email']
				)
			);

			if(!empty($this->data['profile_contact']['url'])){
				$aToSave['User']['homepage'] = $this->data['profile_contact']['url'];
			}

			$this->User->id = $this->Auth->user('id');
			if($this->User->save($aToSave)){
				$flashMsg = __('Your contact informations have been saved.', true);
			}else{
				$flashMsg  = __('An error occured while saving. Please try again or contact us to report this.', true);
			}

			$this->Session->setFlash($flashMsg);
		}

		$this->redirect(array('action' => 'index'));
		/*
		 * TODO:
		 * email <- already implemnted, just to move here
		 * url
		 */
	}

	function save_settings() {
		/*
		 * TODO:
		 * Application language
		 */
		if(!empty($this->data)){

			$aToSave = array(
				'User' => array(
					'send_notifications' => $this->data['profile_setting']['send_notifications'],
					'is_public' => $this->data['profile_setting']['public_profile']
				)
			);

			$this->User->id = $this->Auth->user('id');
			if($this->User->save($aToSave)){
				$flashMsg = __('Your settings have been saved.', true);
			}else{
				$flashMsg = __('An error occured while saving. Please try again or contact us to report this.', true);
			}

			$this->Session->setFlash($flashMsg);
		}

		$this->redirect(array('action' => 'index'));
	}

	function save_password() {
		pr($this->data);

		if(!empty($this->data)){

			$this->User->id = $this->Auth->user('id');
			$this->User->recursive = 0;
			$user = $this->User->read();

			$hashedPass = $this->Auth->password($this->data['old_password']['passwd']);

			if($user['User']['password'] == $hashedPass && $this->data['new_password']['passwd'] == $this->data['new_password2']['passwd']){
				$this->data['User']['password'] = $this->Auth->password($this->data['new_password']['passwd']);

				if($this->User->save($this->data)){
					$flashMsg = __('New password has been saved.', true);
				}else{
					$flashMsg = __('An error occured while saving.', true);
				}
			}else{
				$flashMsg = __('Password error. Please try again.', true);
			}

			$this->Session->setFlash($flashMsg);
		}

		$this->redirect(array('action' => 'index'));
		/*
		 * TODO:
		 * change password <- already implemnted, just to move here
		 */
	}
}
?>
