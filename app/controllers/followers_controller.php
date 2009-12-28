<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

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


App::import('Core', 'Sanitize');

class FollowersController extends AppController {

	var $name = 'Followers';
	var $helpers = array('Html', 'Navigation');
	var $paginate = array(
        'limit' => 20,
        'order' => array('last_time_active' => 'desc'),
        'contain' => array(
            "Group" => array(
                "fields" => "Group.name"
            )
        )
    );

	function beforeFilter() {
		parent::beforeFilter();
		// setting actions that are available to everyone, even guests
		// no need to allow login
		$this->Auth->allowedActions = array('*');
	}

	/**
	 * Display followers of specified user.
	 * NOTE : This is not used (yet).
	 */
	function followers($user_id){
		Sanitize::paranoid($user_id);
		$user = new User();
		$user->id = $user_id;
		$user->hasAndBelongsToMany['Follower']['limit'] = null;
		$user = $user->read();
		$this->set('user', $user);
	}


	/**
	 * Display users following specified user.
	 * NOTE : This is not used (yet).
	 */
	function following($user_id){
		/*$this->User->unbindModel(
			array(
				'belongsTo' => array('Group'),
				'hasMany' => array('SentenceComments', 'Contributions', 'Sentences'),
				'hasAndBelongsToMany' => array('Follower')
			)
		);
		$this->User->id = $id;
		$this->set('following', $this->User->read());*/
		$this->set('following', $this->Follower->get_following($user_id));
	}


	/**
	 * Start following a user.
	 * Used in AJAX request in users.followers_and_following.js.
	 */
	function start_following(){
		$user_id = $_POST['user_id'];
		$this->Follower->habtmAdd('User', $this->Auth->user('id'), $user_id);
	}


	/**
	 * Stop following a user.
	 * Used in AJAX request in users.followers_and_following.js.
	 */
	function stop_following(){
		$user_id = $_POST['user_id'];
		$this->Follower->habtmDelete('User', $this->Auth->user('id'), $user_id);
	}
}
?>
