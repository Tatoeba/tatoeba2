<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  BEN YAALA Salem <salem.benyaala@gmail.com>
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
 * @author   BEN YAALA Salem <salem.benyaala@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Model for users.
 *
 * @category Users
 * @package  Models
 * @author   BEN YAALA Salem <salem.benyaala@gmail.com>
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com> 
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class User extends AppModel
{

    /**
     *
     * @var string
     */
    public $name = 'User';

    /**
     *
     * @var array
     */
    public $actsAs = array(
        'Acl' => array('requester'),
        'ExtendAssociations',
        'Containable'
    );

    const LOWEST_TRUST_GROUP_ID = 4;

    /**
     *
     * @var array
     */
    public $validate = array(
        'username' => array(
            'alphanumeric' => array(
                'rule' => '/^\\w*$/',
                'message'
                    => 'Username can only contain letters, numbers, or underscore'
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
    
    /**
     *
     * @var array
     */
    public $belongsTo = array(
        'Group' => array(
            'className' => 'Group',
            'foreignKey' => 'group_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Country'
    );

    /**
     *
     * @var array
     */
    public $hasMany = array(
          'SentenceComments' => array('limit' => 10, 'order' => 'created DESC')
        , 'Contributions' => array('limit' => 10, 'order' => 'datetime DESC')
        , 'Sentences' => array('limit' => 10, 'order' => 'modified DESC')
        , 'SentencesLists'
        // , 'Mastering_lang'
        // , 'Learning_lang'
    );

    /**
     *
     * @var array
     */
    public $hasAndBelongsToMany = array(
        'Follower' => array(
            'className' => 'Follower',
            'joinTable' => 'followers_users',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'follower_id'
        ),
        'Following' => array(
            'className' => 'Following',
            'joinTable' => 'followers_users',
            'foreignKey' => 'follower_id',
            'associationForeignKey' => 'user_id'
        ),
        'Favorite' => array(
            'className' => 'Favorite',
            'joinTable' => 'favorites_users',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'favorite_id',
            'limit' => '10',
            'unique' => true
        )
    );

    /**
     * ?
     *
     * @return array
     */
    public function parentNode()
    {

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

    /**
     * Generate a random password.
     *
     * @return string
     */
    public function generatePassword()
    {
        $pw = '';
        $c  = 'bcdfghjklmnprstvwz' . 'BCDFGHJKLMNPRSTVWZ' ;
        //consonants except hard to speak ones
        $v  = 'aeiou';              //vowels
        $a  = $c.$v;                //both

        //use two syllables...
        for ($i=0; $i < 2; $i++) {
            $pw .= $c[rand(0, strlen($c)-1)];
            $pw .= $v[rand(0, strlen($v)-1)];
            $pw .= $a[rand(0, strlen($a)-1)];
        }
        //... and add a nice number
        $pw .= rand(1, 9);

        $pw = rtrim($pw);

        if (strlen($pw) == 7) {
            $pw .= rand(0, 9);
        }

        return $pw;
    }

    /**
     * get all the information needed to generate the user's profile
     *
     * @param integer $userId User Identifiant
     * 
     * @return array
     */
    public function getInformationOfCurrentUser($userId)
    {
        $this->unBindModel(
            array('hasMany' => array(
                    'Contributions',
                    'Sentences',
                    'SentenceComments'
                ),
                'hasAndBelongsToMany' => array(
                    'Favorite'
                )
            )
        );

        return $this->findById($userId);
    }

    /**
     * get all the information needed to generate a user profile
     *
     * @param string $userName User's screen name
     * 
     * @return array
     */
    public function getInformationOfUser($userName)
    {
        $this->unBindModel(
            array('hasMany' => array(
                    'Contributions',
                    'Sentences',
                    'SentenceComments'
                ),
                'hasAndBelongsToMany' => array('Favorite')
            )
        );

        return $this->findByUsername($userName);

    }

    /**
     * get all the information about a user needed by the Wall
     *
     * @param integer $userId User Indentifiant
     * 
     * @return array
     */
    public function getInfoWallUser($userId)
    {
        $result = $this->find(
            'first',
            array(
                'conditions' => array('User.id' => $userId),
                'fields' => array(
                    'User.image',
                    'User.username',
                    'User.id'
                ),
                'contain' => array()
            )
        ); 
        
        return $result ; 
    }
    
    /**
     * Get user by id.
     *
     * @param int|null $id Id of the user. If null we take a random one.
     *
     * @return void
     */
    public function getUserById($id = null)
    {
        //TODO: HACK SPOTTED user of order rand, and use of findById
        if ($id == null) {
            $user = $this->User->find(
                'first', 
                array(
                    'conditions' => 'User.group_id < 5', 
                    'order' => 'RAND()', 
                    'limit' => 1
                )
            );
        } else {
            $user = $this->findById($id);
        }
        return $user;
    }
    
    /**
     * Return id of a user from the username.
     *
     * @param string $username Username.
     *
     * @return int
     */
    public function getIdFromUsername($username)
    {
        $user = $this->find(
            'first',
            array(
                'conditions' => array('User.username' => $username),
                'contain' => array(),
                'fields' => 'User.id'
            )
        );
        return $user['User']['id'];
    }

    /**
     * Return id of a user from the email.
     *
     * @param string $userEmail user email.
     *
     * @return int
     */
    public function getIdFromEmail($userEmail)
    {
        $user = $this->find(
            'first',
            array(
                'conditions' => array('User.email' => $userEmail),
                'contain' => array(),
                'fields' => 'User.email'
            )
        );
        return $user['User']['email'];
    }

    /**
     * Return password of a user.
     *
     * @param int $userId Id of the user.
     *
     * @return string
     */
    public function getPassword($userId)
    {
        $user = $this->find(
            'first',
            array(
                'conditions' => array('User.id' => $userId),
                'fields' => 'User.password',
                'contain' => array()
            )
        );
        return $user['User']['password'];
    }
    
    /**
     * Return numbers of actives members.
     *
     * @return one value
     */
     
    public function getNumberOfActiveMembers()
    {
        return $this->find(
            'count',
            array('conditions' => array('last_time_active <>' => 0))        
        );
    }  
}
?>
