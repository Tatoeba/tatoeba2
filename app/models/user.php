<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  TATOEBA Project(should be changed)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class User extends AppModel {

	var $name = 'User';
	var $actsAs = array('Acl' => array('requester'), 'ExtendAssociations');
	
	const LOWEST_TRUST_GROUP_ID = 4;
	
	var $validate = array(
		'username' => array(
			'alphanumeric' => array(
				'rule' => '/^\\w*$/', 
				'message' => 'Username can only contain letters, numbers, or underscore'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Username already taken.'
			),
			'min' => array(
				'rule' => array('minLength', 2), 
				'message' => 'Username must be at least two letters'
			)
		),
		'email' => array(
			'email' => array(
				'rule' => 'email', 
				'message' => 'Non valid email'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Email already used.'
			)
		),
		'lang' => array('alphanumeric'),
		'lastlogout' => array('numeric'),
		'status' => array('numeric'),
		'permissions' => array('numeric'),
		'level' => array('numeric'),
		'group_id' => array('numeric'),
		'homepage' => array('url'),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
		'Group' => array(
			'className' => 'Group',
			'foreignKey' => 'group_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	var $hasMany = array(
		  'SentenceComments' => array('limit' => 10, 'order' => 'created DESC')
		, 'Contributions' => array('limit' => 10, 'order' => 'datetime DESC')
		, 'Sentences' => array('limit' => 10, 'order' => 'modified DESC')
		, 'SentencesLists'
		// , 'Mastering_lang'
		// , 'Learning_lang'
	);
	
	var $hasAndBelongsToMany = array(
		'Follower' => array(
			'className' => 'User',
			'joinTable' => 'followers_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'follower_id',
			'conditions' => '',
			'order' => '',
			'limit' => '',
			'unique' => true,
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
		'Following' => array(
			'className' => 'Following',
			'joinTable' => 'followers_users',
			'foreignKey' => 'follower_id',
			'associationForeignKey' => 'user_id',
			'conditions' => '',
			'order' => '',
			'limit' => '',
			'unique' => true,
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
		'Favorite' => array(
			'className' => 'Favorite',
			'joinTable' => 'favorites_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'favorite_id',
			'conditions' => '',
			'order' => '',
			'limit' => '10',
			'unique' => true,
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
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
	
	// this should probably be in the controller... why did I put it here I don't remember
	function generate_password(){
		$pw = '';
		$c  = 'bcdfghjklmnprstvwz' . 'BCDFGHJKLMNPRSTVWZ' ; //consonants except hard to speak ones
		$v  = 'aeiou';              //vowels
		$a  = $c.$v;                //both 
		
		//use two syllables...
		for($i=0;$i < 2; $i++){
		$pw .= $c[rand(0, strlen($c)-1)];
		$pw .= $v[rand(0, strlen($v)-1)];
		$pw .= $a[rand(0, strlen($a)-1)];
		}
		//... and add a nice number
		$pw .= rand(1,9);
		
		$pw = rtrim($pw);
		
		if (strlen($pw) == 7) {
			$pw .= rand(0,9);
		}
		
		return $pw;
	}

}
?>
