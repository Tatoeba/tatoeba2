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

	function followers($user_id, $ajax = 'false'){
		Sanitize::paranoid($user_id);

		if($ajax == 'true'){
			$this->set('ajax', true);
			$aUser = $this->Follower->get_followers($user_id, 10);
		}else{
			$aUser = $this->Follower->get_followers($user_id);
		}

		$this->set('user', $aUser);
	}

	function following($user_id, $ajax = 'false'){
		Sanitize::paranoid($user_id);

		if($ajax == 'true'){
			$this->set('ajax', true);
			$aUser = $this->Follower->get_following($user_id, 10);
		}else{
			$aUser = $this->Follower->get_following($user_id);
		}
		$this->set('user', $aUser);
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


	/**
	 * Stop following a user.
	 * Used in AJAX request in users.followers_and_following.js.
	 */
	function refuse_follower($user_id){
		Sanitize::paranoid($user_id);

		$this->Follower->habtmDelete('User', $user_id, $this->Auth->user('id'));
		$this->redirect(array('action' => 'followers', $this->Auth->user('id')));
	}
}
?>
