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
 * @link     https://tatoeba.org
 */
namespace App\Model\Table;

use App\Auth\VersionedPasswordHasher;
use Cake\Database\Schema\TableSchema;
use Cake\Filesystem\File;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class UsersTable extends Table
{
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('birthday', 'string');
        $schema->setColumnType('description', 'text');
        $schema->setColumnType('settings', 'json');
        return $schema;
    }

    public function initialize(array $config)
    {
        parent::initialize($config);

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
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('username')
            ->requirePresence('username', 'create')
            ->notEmpty('username', __('Field required'))
            ->minLength('username', 2, __('Username must be at least two characters long'))
            ->maxLength('username', 20, __('Username must be at most 20 characters long'))
            ->add('username', 'alphanumeric', [
                'rule' => ['custom', '/^\\w*$/'],
                'message' => __('Username can only contain letters, numbers, or underscore'),
            ]);

        $validator
            ->scalar('password')
            ->requirePresence('password', 'create')
            ->notEmpty('password', __('Field required'))
            ->minLength('password', 6, __('Password must be at least 6 characters long'));

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmpty('email', __('Field required'))
            ->add('email', 'email', [
                'rule' => ['custom', '/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/'],
                'message' => __('Invalid email address'),
            ]);

        $validator
            ->date('since');

        $validator
            ->date('last_time_active');

        $validator
            ->scalar('role');

        $validator
            ->allowEmpty('send_notifications')
            ->boolean('send_notifications');

        $validator
            ->allowEmpty('name')
            ->scalar('name')
            ->maxLength('name', 255);

        $validator
            ->allowEmpty('birthday');

        $validator
            ->allowEmpty('description')
            ->scalar('description');

        $validator
            ->allowEmpty('homepage')
            ->scalar('homepage')
            ->maxLength('homepage', 255);

        $validator
            ->scalar('image')
            ->maxLength('image', 255);

        $validator
            ->allowEmpty('country_id')
            ->scalar('country_id')
            ->maxLength('country_id', 2);

        $validator
            ->scalar('audio_license')
            ->maxLength('audio_license', 50);

        $validator
            ->allowEmpty('audio_attribution_url')
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
        $rules->add($rules->isUnique(['username'], __('Username already taken.')));
        $rules->add($rules->isUnique(['email'], __('Email address already used.')));

        return $rules;
    }

    private function removeImages($file)
    {
        $images = [
            WWW_ROOT . 'img' . DS . 'profiles_128' . DS . $file,
            WWW_ROOT . 'img' . DS . 'profiles_36' . DS . $file,
        ];
        foreach ($images as $image) {
            $file = new File($image);
            if ($file->exists()) {
                $file->delete();
            }
        }
    }

    public function afterSave($event, $entity, $options = array())
    {
        if (!$entity->isNew() && $entity->isDirty('image') && empty($entity->image)) {
            $this->removeImages($entity->getOriginal('image'));
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
        return $this->get($userId);
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
        return $this->find()
            ->select([
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
                'role',
                'level',
                'country_id',
            ])
            ->where(['username' => $userName])
            ->first();
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
        return $this->find()
            ->select([
                'send_notifications',
                'settings',
                'email',
            ])
            ->where(['id' => $userId])
            ->first();
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
        return $this->find()
            ->select([
                'audio_license',
                'audio_attribution_url',
            ])
            ->where(['id' => $userId])
            ->first();
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
        return $this->get($id, ['contain' => [
            'Sentences' => function ($q) {
                return $q->select(['user_id', 'id', 'lang', 'correctness', 'text', 'modified'])
                         ->limit(10)
                         ->orderDesc('modified');
            },
            'Contributions' => function ($q) {
                $fields = [
                    'user_id',
                    'sentence_id',
                    'sentence_lang',
                    'translation_id',
                    'action',
                    'datetime',
                    'type',
                    'text',
                ];
                return $q->select($fields)
                         ->where(['type !=' => 'license'])
                         ->limit(10)
                         ->orderDesc('datetime');
            },
            'SentenceComments' => function ($q) {
                return $q->limit(10)
                         ->contain('Sentences')
                         ->orderDesc('SentenceComments.created');
            },
            'Wall' => function ($q) {
                return $q->select(['owner', 'id', 'content', 'date', 'hidden', 'modified'])
                         ->limit(10)
                         ->orderDesc('date');
            },
        ]]);
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
        return $this->get($id);
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
        $user = $this->find()
            ->select('id')
            ->where(['username' => $username])
            ->first();
        return $user ? $user->id : null;
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
        $user = $this->find()
            ->select(['username'])
            ->where(['id' => $userId])
            ->first();
        return $user ? $user->username : null;
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
        $user = $this->find()
            ->select(['id'])
            ->where(['email' => $userEmail])
            ->first();
        return $user ? $user->id : null;
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
        $user = $this->find()
            ->select(['email'])
            ->where(['id' => $userId])
            ->first();
        return $user ? $user->email : null;
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
        $user = $this->find()
            ->where(['email' => $email, 'id !=' => $userId])
            ->first();
        return !$user;
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
        $user = $this->find()
            ->select(['password'])
            ->where(['id' => $userId])
            ->first();
        return $user ? $user->password : null;
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
        $user = $this->find()
            ->select(['level'])
            ->where(['id' => $userId])
            ->first();
        return $user->level;
    }

    public function updatePasswordVersion($userId, $plainTextPassword)
    {
        $passwordHasher = new VersionedPasswordHasher();
        $user = $this->get($userId);
        $storedHash = $user->password;
        if ($passwordHasher->isOutdated($storedHash)) {
            $user->password = $plainTextPassword;
            $this->save($user);
        }
    }
}
