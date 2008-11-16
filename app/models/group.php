<?php
class Group extends AppModel {

	var $name = 'Group';
	var $actsAs = array('Acl' => array('requester'));
	
	var $validate = array(
		'name' => array('alphanumeric'),
		'created' => array('date'),
		'modified' => array('date')
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $hasMany = array(
			'User' => array('className' => 'User',
								'foreignKey' => 'group_id',
								'dependent' => false,
								'conditions' => '',
								'fields' => '',
								'order' => '',
								'limit' => '',
								'offset' => '',
								'exclusive' => '',
								'finderQuery' => '',
								'counterQuery' => ''
			)
	);

	function parentNode() {
	    return null;
	}
}
?>