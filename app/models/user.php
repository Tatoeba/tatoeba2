<?php
class User extends AppModel {

	var $name = 'User';
	var $actsAs = array('Acl' => array('requester'));
	
	var $validate = array(
		'username' => array(
			'alphanumeric' => array(
				'rule' => 'alphanumeric', 
				'message' => 'Username must only contain letters and numbers'),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Username already taken.')
			),
		'password' => array('rule' => 'alphanumeric', 'message' => 'Username must only contain letters and numbers'),
		'email' => array(
			'email' => array(
				'rule' => 'email', 
				'message' => 'Non valid email'),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Email already used.')
			),
		'lang' => array('alphanumeric'),
		'lastlogout' => array('numeric'),
		'status' => array('numeric'),
		'permissions' => array('numeric'),
		'level' => array('numeric'),
		'group_id' => array('numeric')
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Group' => array('className' => 'Group',
								'foreignKey' => 'group_id',
								'conditions' => '',
								'fields' => '',
								'order' => ''
			)
	);
	
	function parentNode() {
	    if (!$this->id && empty($this->data)) {
	        return null;
	    }
	    $data = $this->data;
	    if (empty($this->data)) {
	        $data = $this->read();
	    }
	    if (!$data['User']['group_id']) {
	        return null;
	    } else {
	        return array('Group' => array('id' => $data['User']['group_id']));
	    }
	}

}
?>