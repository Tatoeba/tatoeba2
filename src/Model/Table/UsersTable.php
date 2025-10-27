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

use App\Model\Entity\User;
use App\Auth\VersionedPasswordHasher;
use ArrayObject;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\Filesystem\File;
use Cake\I18n\Time;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Routing\Router;
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
        $this->hasMany('UsersLanguages', [
            'foreignKey' => 'of_user_id',
            'propertyName' => 'languages',
        ]);
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
            ->email(
                'email',
                False /* Don't check MX records */,
                __('Failed to change email address. Please enter a proper email address.')
            )
            ->requirePresence('email', 'create')
            ->notEmpty('email', __('Field required'));

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
            ->allowEmpty('birthday')
            ->add('birthday', 'validBirthday', [
                'rule' => function ($data, $provider) {
                    $data = explode('-', $data, 3);
                    $data = array_map(fn ($n) => (int)$n, $data);
                    list($year, $month, $day) = array_pad($data, 3, null);

                    if ($year && $month && $day) {
                        return checkdate($month, $day, $year);
                    } elseif ($month && $day) {
                        // Use 2016 because its a leap year.
                        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, 2016);

                        if ($day > $daysInMonth) {
                            return false;
                        }
                    }
                    return true;
                },
                'message' => __('The entered birthday is an invalid date. Please try again.'),
            ])
            ->add('birthday', 'isComplete', [
                'rule' => function ($data, $provider) {
                    $data = explode('-', $data, 3);
                    $data = array_map(fn ($n) => (int)$n, $data);
                    list($year, $month, $day) = array_pad($data, 3, null);
                    return !$day && $year || $month && $day || !$year && !$month && !$day;
                },
                'message' => __(
                    'The entered birthday is incomplete. '.
                    'Accepted birthdays: full date, month and day, year and month, only year.'
                ),
            ]);

        $pmAdminsLink = Router::url(['controller' => 'private_messages', 'action' => 'write', 'TatoebaAdmins']);
        $validator
            ->allowEmpty('description')
            ->scalar('description')
            ->add('description', 'outboundLinkCheck', [
                'rule' => 'isLinkPermitted',
                'provider' => 'appvalidation',
                'message' => format(
                    __('Sorry, you do not have the permission to include links in your profile description. '.
                       'Because of spam concerns, new accounts need to be verified before they can use '.
                       'outbound links. Please remove any outbound link from your profile description '.
                       'in order to continue. You can ask for permission to add links later by '.
                       '{linkStart}sending a message to administrators{linkEnd}.'
                    ),
                    ['linkStart' => "<a href=\"$pmAdminsLink\" target=\"_blank\">", 'linkEnd' => '</a>']
                ),
            ]);

        $validator
            ->allowEmpty('homepage')
            ->scalar('homepage')
            ->maxLength('homepage', 255)
            ->add('homepage', 'outboundLinkCheck', [
                'rule' => 'isLinkPermitted',
                'provider' => 'appvalidation',
                'message' => format(
                    __('Sorry, you do not have the permission to set a homepage on your profile. '.
                       'Because of spam concerns, new accounts need to be verified before they can use '.
                       'outbound links. Please remove the homepage from your profile '.
                       'in order to continue. You can ask for permission to add it later by '.
                       '{linkStart}sending a message to administrators{linkEnd}.'
                    ),
                    ['linkStart' => "<a href=\"$pmAdminsLink\" target=\"_blank\">", 'linkEnd' => '</a>']
                ),
            ]);

        $validator
            ->scalar('image')
            ->maxLength('image', 255);

        $validator
            ->allowEmpty('country_id')
            ->scalar('country_id')
            ->maxLength('country_id', 2);

        $validator
            ->allowEmptyString('audio_license')
            ->scalar('audio_license')
            ->maxLength('audio_license', 50);

        $validator
            ->allowEmpty('audio_attribution_url')
            ->scalar('audio_attribution_url')
            ->maxLength('audio_attribution_url', 255);

        $validator
            ->allowEmpty('is_spamdexing')
            ->boolean('is_spamdexing');

        return $validator;
    }

    /**
     * Fill unset birthday fields with zeros if birthday has at least one
     * user-set field. If all fields empty, returns null.
     *
     * @return string
     */
    private function _generateBirthdayDate(array $birthday)
    {
        $year =  $birthday['year']  ?: '0000';
        $month = $birthday['month'] ?: '00';
        $day =   $birthday['day']   ?: '00';

        if ($year == '0000') {
            if ($month == '00' && $day == '00') {
                return null;
            } elseif ($month == '02' && $day == '29') {
                // Mysql wont save a partial leap year date so change year to 1904
                // and catch in date view helper.
                $year = '1904';
            }
        }

        return "$year-$month-$day";
    }

    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
    {
        if (isset($data['birthday']['year']) && isset($data['birthday']['month']) && isset($data['birthday']['day'])) {
            $data['birthday'] = $this->_generateBirthdayDate($data['birthday']);
        }
        if (isset($data['is_spamdexing']) && $data['is_spamdexing'] === '') {
            $data['is_spamdexing'] = null;
        }
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
        $rules->add($rules->isUnique(['email'], __('That email address already exists. Please try another.')));

        return $rules;
    }

    public function beforeSave(Event $event, User $user, ArrayObject $options)
    {
        if ($user->isNew()) {
            if (!$user->has('is_spamdexing')) {
                // New users are considered potential spamdexing accounts until manual verification
                $user->is_spamdexing = true;
            }
        }
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

    public function updateLastContribution($userId)
    {
        try {
            $user = $this->get($userId);
        } catch (RecordNotFoundException $e) {    
        }
        $user->last_contribution = Time::now();
        $this->save($user);
    }
}
