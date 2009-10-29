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
class UserController extends AppController {

	var $name = 'User';

	function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allowedActions = array('*');
	}

	function index() {
		$aUser = $this->User->findById($this->Auth->user('id'));

		$this->pageTitle = 'Your profile';

		$this->set('user', $aUser);
	}

	function profile() {
		;
	}

	function save_image() {
		if(!empty($this->data)){
			if($this->data['profile_image']['image']['error'] == UPLOAD_ERR_OK){
				if(is_uploaded_file($this->data['profile_image']['image']['tmp_name'])){
					// TODO: Check extension and size
					$this->loadModel('User');

					$aUser = $this->User->findById($this->Auth->user('id'));

					$sTmpFile = $this->data['profile_image']['image']['tmp_name'];
					$sEmail = $aUser['User']['email'];
					$sProfilesPath = IMAGES . 'profiles' . DS;
					$sNewFile =  md5($sEmail) . '.png';

					if(move_uploaded_file($sTmpFile, $sProfilesPath . $sNewFile)){
						$aToSave = array(
							'User' => array(
								'image' => $sNewFile
							)
						);

						$this->User->id = $this->Auth->user('id');
						$this->User->save($aToSave);
					}
				}
			}
		}
		$this->redirect(array('action' => 'index'));
	}

	function save_basic() {
		/*
		 * TODO:
		 * name
		 * birthday
		 * country
		 */
	}

	function save_contact() {
		/*
		 * TODO:
		 * email <- already implemnted, just to move here
		 * url
		 */
	}

	function save_settings() {
		/*
		 * TODO:
		 * notification On/Off <- already implemnted, just to move here
		 * put profile public On/Off
		 * 
		 */
	}

	function change_password() {
		/*
		 * TODO:
		 * change password <- already implemnted, just to move here
		 */
	}
}
?>
