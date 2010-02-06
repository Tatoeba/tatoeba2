<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009 DEPARIS Étienne <etienne.deparis@umaneti.net>
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
 * @author   DEPARIS Étienne <etienne.deparis@umaneti.net>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Model for Followers.
 *
 * @category Follower
 * @package  Models
 * @author   DEPARIS Étienne <etienne.deparis@umaneti.net>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class Follower extends AppModel
{
    public $name = 'Follower';
    public $useTable = 'users';

    public $actsAs = array('ExtendAssociations','Containable');

    public $hasAndBelongsToMany = array(
        'User' => array(
            'className' => 'User',
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
        )
    );

    /**
     * return the follower of a user
     *
     * @param int $userId The user we want the followers
     * @param int $limit  Limit
     *
     * @return Array
     */
    public function getFollowers($userId, $limit = null)
    {
        $user = new User();
        $user->id = $userId;
        $user->hasAndBelongsToMany['Follower']['limit'] = $limit;
        return $user->read();
    }

    /**
     * return the followings of a user
     *
     * @param int $userId The user we want the followings
     * @param int $limit  Limit
     *
     * @return Array
     */
    public function getFollowing($userId, $limit = null)
    {
        $user = new User();
        $user->id = $userId;
        $user->hasAndBelongsToMany['Following']['limit'] = $limit;
        return $user->read();
    }
}
?>
