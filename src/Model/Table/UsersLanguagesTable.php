<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2018   HO Ngoc Phuong Trang
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
 */
namespace App\Model\Table;

use App\Event\SentencesReindexListener;
use App\Lib\LanguagesLib;
use App\Model\Entity\Language;
use App\Model\Entity\User;
use App\Model\CurrentUser;
use Cake\ORM\Table;
use Cake\ORM\Entity;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Validation\Validator;

class UsersLanguagesTable extends Table
{
    // TODO Reimplement the update of language stats

    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('details', 'text');
        return $schema;
    }

    public function initialize(array $config)
    {
        $this->belongsTo('Users', ['foreignKey' => 'of_user_id']);
        $this->belongsTo('Languages', ['foreignKey' => 'language_code']);

        $this->addBehavior('Timestamp');

        $this->getEventManager()->on(new SentencesReindexListener());
    }

    public function validationDefault(Validator $validator)
    {
        $languages = array_keys(LanguagesLib::languagesInTatoeba());
        $validator
            ->add('language_code', [
                'inList' => [
                    'rule' => ['inList', $languages]
                ]
            ]);

        $validator
            ->allowEmpty('level')
            ->add('level', [
                'inList' => [
                    'rule' => ['range', 0, Language::MAX_LEVEL]
                ]
            ]);

        $validator
            ->dateTime('created');

        $validator
            ->dateTime('modified');

        return $validator;
    }

    public function getLanguagesOfUser($userId)
    {
        $languages = $this->find(
            'all',
            array(
                'conditions' => array('of_user_id' => $userId),
                'order' => 'level DESC'
            )
        );

        return $languages;
    }


    public function getLanguagesByUser($userId)
    {
        $languages = $this->find(
            'all',
            array(
                'conditions' => array('by_user_id' => $userId),
                'order' => 'level DESC'
            )
        );

        return $languages;
    }


    public function getLanguageInfoOfUser($lang, $userId)
    {
        $languageInfo = $this->find()
            ->where([
                'of_user_id' => $userId,
                'by_user_id' => $userId,
                'language_code' => $lang
            ])
            ->first();

        return $languageInfo;
    }


    public function getLanguageInfo($id)
    {
        try  {
            $result = $this->get($id)
                ->extract(['language_code', 'by_user_id']);
        } catch (RecordNotFoundException $e) {
            $result = null;
        }

        return $result;
    }


    public function getUsersForLanguage($lang)
    {
        $result = array(
            'conditions' => array(
                'language_code' => $lang,
                'Users.role IN' => User::ROLE_CONTRIBUTOR_OR_HIGHER,
            ),
            'fields' => array(
                'of_user_id',
                'level',
            ),
            'contain' => array(
                'Users' => array(
                    'fields' => array(
                        'id',
                        'username',
                        'image'
                    )
                )
            ),
            'order' => ['UsersLanguages.level' => 'DESC'],
            'limit' => 30
        );

        return $result;
    }


    public function getNumberOfUsersForEachLanguage()
    {
        $result = $this->find()
            ->select([
                'language_code',
                'total' => 'COUNT(*)'
            ])
            ->where(['Users.role IN' => User::ROLE_CONTRIBUTOR_OR_HIGHER])
            ->order(['total' => 'DESC'])
            ->contain(['Users'])
            ->group(['language_code'])
            ->toList();

        return $result;
    }

    /**
     * Save a language for the user
     *
     * @param array   $data          The request data
     * @param integer $currentUserId The user id
     *
     * @return Entity|false
     **/
    public function saveUserLanguage($data, $currentUserId) 
    {
        $data['of_user_id'] = $data['by_user_id'] = $currentUserId;
        unset($data['modified']);
        unset($data['created']);

        if (isset($data['id']) && $data['id']) {
            try {
                $langInfo = $this->get($data['id']);
            } catch (RecordNotFoundException $e) {
                return false;
            }
            $this->patchEntity($langInfo, $data);
        } else {
            $langInfo = $this->newEntity($data);
        }

        if ($langInfo->by_user_id != $currentUserId) {
            return false;
        }

        return $this->save($langInfo);
    }

    public function deleteUserLanguage($id, $currentUserId)
    {
        try {
            $langInfo = $this->get($id);
        } catch (RecordNotFoundException $e) {
            $langInfo = null;
        }
        
        $canDelete = $langInfo && $langInfo->by_user_id == $currentUserId;

        if ($canDelete) {
            return $this->delete($langInfo);
        } else {
            return false;
        }
    }
}
