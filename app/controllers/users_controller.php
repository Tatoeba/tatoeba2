<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $helpers = array('Html', 'Form');
	

	function beforeFilter() {
		parent::beforeFilter(); 
		
		// setting actions that are available to everyone, even guests
		$this->Auth->allowedActions = array('logout','register');
	}

	
	function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
		
		//$this->initDB();
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
	
	function login(){
	}
	
	function logout(){  
		$this->Session->setFlash('Logout');  
		$this->redirect($this->Auth->logout());  
	}
	
	function register(){
		if (!empty($this->data)) {
			$nonHashedPassword = $this->data['User']['password'];
			if ($this->data['User']['password'] == $this->Auth->password($this->data['User']['password_confirm'])) {
				$this->User->create();
				$this->data['User']['since'] = date("Y-m-d H:i:s");
				$this->data['User']['group_id'] = 3;
				if($this->User->save($this->data)){
					$this->redirect(array('controller' => 'pages', 'action' => 'index'));
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
	
	// temporary function to grant/deny access
	function initDB() {
	    $group =& $this->User->Group;
		
	    //Allow admins to everything
	    $group->id = 1;     
	    $this->Acl->allow($group, 'controllers');
	 
	    //Permissions for editors
	    $group->id = 2;
		$this->Acl->deny($group, 'controllers');
	    $this->Acl->allow($group, 'controllers/Sentences');
	 
	    //Permissions for users
	    $group->id = 3;
		$this->Acl->deny($group, 'controllers');
	    $this->Acl->allow($group, 'controllers/Sentences/show');
		$this->Acl->allow($group, 'controllers/Sentences/translate');
	}
}
?>