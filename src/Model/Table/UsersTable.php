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
namespace App\Model\Table;

use App\Auth\VersionedPasswordHasher;
use Cake\Database\Schema\TableSchema;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class UsersTable extends Table
{
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('settings', 'json');
        return $schema;
    }

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->belongsTo('Groups');

        $this->hasMany('Audios');
        $this->hasMany('Contributions');
        $this->hasMany('Favorites');
        $this->hasMany('LastContributions');
        $this->hasMany('PrivateMessages');
        $this->hasMany('SentenceAnnotations');
        $this->hasMany('SentenceComments');
        $this->hasMany('Sentences');
        $this->hasMany('SentencesLists');
        $this->hasMany('Tags');
        $this->hasMany('TagsSentences');
        $this->hasMany('Transcriptions');
        $this->hasMany('Wall', [
            'foreignKey' => 'owner'
        ]);

        $this->addBehavior('Acl.Acl', ['type' => 'requester']);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('username')
            ->requirePresence('username', 'create')
            ->lengthBetween('username', [2, 20])
            ->add('username', [
                'alphanumeric' => ['rule' => ['custom', '/^\\w*$/']],
            ]);

        $validator
            ->scalar('password')
            ->requirePresence('password', 'create');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->add('email', [
                'email' => ['rule' => ['custom' => '/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/']],
            ]);

        $validator
            ->date('since');

        $validator
            ->date('last_time_active');

        $validator
            ->integer('group_id');

        $validator
            ->boolean('send_notifications');

        $validator
            ->scalar('name')
            ->maxLength('name', 255);

        $validator
            ->date('birthday');

        $validator
            ->scalar('description');

        $validator
            ->scalar('homepage')
            ->maxLength('homepage', 255);

        $validator
            ->scalar('image')
            ->maxLength('image', 255);

        $validator
            ->scalar('country_id')
            ->maxLength('country_id', 2);

        $validator
            ->scalar('audio_license')
            ->maxLength('audio_license', 50);

        $validator
            ->scalar('audio_attribution_url')
            ->maxLength('audio_attribution_url', 255);

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['username']));
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->existsIn(['group_id'], 'Groups'));

        return $rules;
    }

    /**
     * Create the aro entry for given user in case it's not present. Without this
     * aro entry, the user cannot be edited, or cannot reset their password.
     *
     * @return null
     */
    public function fixAro($userId, $groupId) {
        $users = $this->Aro->find('all', array(
           'conditions' => array('foreign_key' => $userId)
        ));

        if (empty($users)) {
            $this->Aro->create();
            $this->Aro->save(array(
                'model' => 'User',
                'foreign_key' => $userId,
                'parent_id' => $groupId
            ));
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

        $pw = trim($pw);

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
        return $this->find(
            'first',
            array(
                'conditions' => array('username' => $userName),
                'fields' => array(
                    'id',
                    'name',
                    'image',
                    'homepage',
                    'since',
                    'send_notifications',
                    'description',
                    'settings',
                    'username',
                    'birthday',
                    'group_id',
                    'level',
                    'country_id'
                )
            )
        );
    }


    /**
     * Get options.
     *
     * @param int $userId Id of the user.
     *
     * @return array
     */
    public function getSettings($userId)
    {
        return $this->find(
            'first',
            array(
                'conditions' => array('id' => $userId),
                'fields' => array(
                    'send_notifications',
                    'settings',
                    'email',
                )
            )
        );
    }


    /**
     * Get audio-related settings
     *
     * @param int $userId Id of the user.
     *
     * @return array
     */
    public function getAudioSettings($userId)
    {
        return $this->find(
            'first',
            array(
                'conditions' => array('id' => $userId),
                'fields' => array(
                    'audio_license',
                    'audio_attribution_url',
                )
            )
        );
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
                )
            )
        );

        return $result ;
    }

    /**
     * Get user latest sentences, logs, comments, wall messages.
     *
     * @param int $id Id of the user
     *
     * @return array
     */
    public function getUserByIdWithExtraInfo($id)
    {
        $user = $this->find(
            'first',
            array(
                'conditions' => array('User.id' => $id),
                'contain' => array(
                    'Sentences' => array(
                        'limit' => 10,
                        'fields' => array(
                            'id',
                            'lang',
                            'correctness',
                            'text',
                        ),
                        'order' => 'modified DESC'
                    ),
                    'Contributions' => array(
                        'limit' => 10,
                        'conditions' => array('type !=' => 'license'),
                        'fields' => array(
                            'sentence_id',
                            'sentence_lang',
                            'translation_id',
                            'action',
                            'datetime',
                            'type',
                            'text',
                        ),
                        'order' => 'datetime DESC '
                    ),
                    'SentenceComments' => array(
                        'limit' => 10,
                        'fields' => array(
                            'id',
                            'text',
                            'created',
                            'sentence_id',
                            'hidden',
                            'modified'
                        ),
                        'order' => 'created DESC'
                    ),
                    'Wall' => array(
                        'limit' => 10,
                        'fields' => array(
                            'id',
                            'content',
                            'date',
                            'hidden',
                            'modified'
                        ),
                        'order' => 'date DESC'
                    )
                )
            )
        );
        return $user;
    }


    /**
     * Retrieves only the fields from users table, no joins.
     *
     * @param int $id Id of the user.
     *
     * @return array User data.
     */
    public function getUserById($id)
    {
        $user = $this->find(
            'first',
            array(
                'conditions' => array('User.id' => $id)
            )
        );

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
                'fields' => 'User.id'
            )
        );
        return !empty($user) ? $user['User']['id'] : null;
    }


    /**
     * Return name of a user from the user's id.
     *
     * @param int $userId User's id.
     *
     * @return string
     */
    public function getUserNameFromId($userId)
    {
        $user = $this->find(
            'first',
            array(
                'conditions' => array('User.id' => $userId),
                'fields' => 'User.username'
            )
        );
        return !empty($user) ? $user['User']['username'] : null;
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
                'fields' => 'User.id'
            )
        );
        return !empty($user) ? $user['User']['id'] : null;
    }

    /**
     * Return an email from a user id.
     *
     * @param int $userId user id.
     *
     * @return string
     */
    public function getEmailFromId($userId)
    {
        $user = $this->find(
            'first',
            array(
                'conditions' => array('User.id' => $userId),
                'fields' => 'User.email'
            )
        );
        return !empty($user) ? $user['User']['email'] : null;
    }

    /**
     * Check if (new) email for user is unique
     *
     * @param string $email new email.
     *
     * @return bool
     */
    public function isEmailUnique($email, $userId)
    {
        $result =  $this->find(
            'first',
            array(
                'conditions' => array(
                    'email' => $email,
                    'User.id !=' => $userId
                )
           )
        );
        return empty($result);
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
                'fields' => 'User.password'
            )
        );
        return !empty($user) ? $user['User']['password'] : null;
    }

    /**
     * Return the level of the user of given id.
     *
     * @param int $userId Id of the user.
     *
     * @return int
     */
    public function getLevelOfUser($userId)
    {
        $result = $this->find(
            'first',
            array(
                'conditions' => array('User.id' => $userId),
                'fields' => 'User.level'
            )
        );
        return $result['User']['level'];
    }


    public function getGroupOfUser($userId)
    {
        $result = $this->findById($userId, 'group_id');

        return $result['User']['group_id'];
    }

    public function updatePasswordVersion($userId, $plainTextPassword)
    {
        $this->id = $userId;
        $storedHash = $this->field('password');
        if ($this->passwordHasher->isOutdated($storedHash)) {
            $this->saveField('password', $plainTextPassword);
        }
    }
}
