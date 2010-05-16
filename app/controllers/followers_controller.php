<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   Etienne Deparis <etienne.deparis@umaneti.net>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
*/

/**
 * Controller for followers.
 *
 * @category Followers
 * @package  Controllers
 * @author   Etienne Deparis <etienne.deparis@umaneti.net>
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class FollowersController extends AppController
{
    public $name = 'Followers';
    public $helpers = array('Html', 'Navigation');
    public $paginate = array(
        'limit' => 20,
        'order' => array('last_time_active' => 'desc'),
        'contain' => array(
            "Group" => array(
                "fields" => "Group.name"
            )
        )
    );
    
    /**
     * Before filter.
     * 
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        // setting actions that are available to everyone, even guests
        // no need to allow login
        $this->Auth->allowedActions = array('*');
    }
    
    /**
     * Display followers.
     *
     * @param int    $userId Id of user.
     * @param string $ajax   TODO
     * 
     * @return void
     */
    public function followers($userId, $ajax = 'false')
    {
        $userId = Sanitize::paranoid($userId);

        if ($ajax == 'true') {
            $this->set('ajax', true);
            $aUser = $this->Follower->getFollowers($userId, 10);
        } else {
            $aUser = $this->Follower->getFollowers($userId);
        }

        $this->set('user', $aUser);
    }
    
    /**
     * Display following of specified user.
     * 
     * @param int    $userId Id of user.
     * @param string $ajax   TODO
     *
     * @return void
     */
    public function following($userId, $ajax = 'false')
    {
        $userId = Sanitize::paranoid($userId);

        if ($ajax == 'true') {
            $this->set('ajax', true);
            $aUser = $this->Follower->getFollowing($userId, 10);
        } else {
            $aUser = $this->Follower->getFollowing($userId);
        }
        $this->set('user', $aUser);
    }

    /**
     * Start following a user.
     * Used in AJAX request in users.followers_and_following.js.
     *
     * @return void
     */
    public function start_following()
    {
        $userId = Sanitize::paranoid($_POST['user_id']);
        
        $this->Follower->habtmAdd('User', $this->Auth->user('id'), $userId);
    }

    /**
     * Stop following a user.
     * Used in AJAX request in users.followers_and_following.js.
     *
     * @return void
     */
    public function stop_following()
    {
        $userId = Sanitize::paranoid($_POST['user_id']);
        
        $this->Follower->habtmDelete('User', $this->Auth->user('id'), $userId);
    }

    /**
     * Block a user... supposedly.
     *
     * @param int $userId Id of user.
     *
     * @return void
     */
    public function refuse_follower($userId)
    {
        $userId = Sanitize::paranoid($userId);

        $this->Follower->habtmDelete('User', $userId, $this->Auth->user('id'));
        $this->redirect(array('action' => 'followers', $this->Auth->user('id')));
    }
}
?>
