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

use Cake\ORM\Table;
use Cake\ORM\Entity;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\Exception\RecordNotFoundException;

class UsersLanguagesTable extends Table
{
    public $name = 'UsersLanguages';
    public $useTable = "users_languages";
    public $actsAs = array("Containable");
    public $belongsTo = array(
        'User' => array('foreignKey' => 'of_user_id'),
        'Language' => array('foreignKey' => 'language_code')
    );

    // TODO Reimplement the update of language stats

    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('details', 'text');
        return $schema;
    }

    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
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
        $languageInfo = $this->find(
            'first',
            array(
                'conditions' => array(
                    'by_user_id' => $userId,
                    'language_code' => $lang
                )
            )
        );

        return $languageInfo;
    }


    public function getLanguageInfo($id)
    {
        try  {
            $result = $this->get($id)->language_info;
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
                'User.group_id NOT' => array(5,6)
            ),
            'fields' => array(
                'of_user_id',
                'level',
            ),
            'contain' => array(
                'User' => array(
                    'fields' => array(
                        'id',
                        'username',
                        'image'
                    )
                )
            ),
            'order' => 'UsersLanguages.level DESC',
            'limit' => 30
        );

        return $result;
    }


    public function getNumberOfUsersForEachLanguage()
    {
        $result = $this->find(
            'all',
            array(
                'fields' => array('language_code', 'COUNT(*) as total'),
                'group' => 'language_code',
                'conditions' => array(
                    'User.group_id NOT' => array(5,6)
                ),
                'order' => 'total DESC',
                'contain' => array('User')
            )
        );

        return $result;
    }

    /**
     * Executed on Sentence's beforeFind
     */
    public function reportNativeness($event) {
        $query = $event->data[0];
        if (CurrentUser::getSetting('native_indicator')) {
            if (is_array($query['fields']) &&
                in_array('lang', $query['fields']) &&
                in_array('user_id', $query['fields']) &&
                in_array('User.level', $query['fields']) &&
                in_array('User.group_id', $query['fields'])
               ) {
                $this->User->virtualFields['is_native'] = 0;
                $query['fields'][] = '`UsersLanguages`.`id` IS NOT NULL AND User.group_id != 6 AND User.level > -1 AS User__is_native';
                $query['joins'][] = array(
                    'alias' => 'UsersLanguages',
                    'table' => 'users_languages',
                    'type' => 'left',
                    'conditions' => array(
                        'Sentence.user_id = UsersLanguages.of_user_id',
                        'Sentence.lang = UsersLanguages.language_code',
                        'UsersLanguages.level' => 5,
                    )
                );
            }
        }
        return $query;
    }

    public function saveUserLanguage($data, $currentUserId) 
    {
        if (empty($data['id'])) {
            $canSave = !empty($data['language_code']) && $data['language_code'] != 'und';
            $langInfo = $this->newEntity();
            $langInfo->language_code = $data['language_code'];
        } else {
            $id = $data['id'];
            try {
                $langInfo = $this->get($id);
            } catch (RecordNotFoundException $e) {
                $langInfo = $this->newEntity();
            }            
            $canSave = $langInfo->by_user_id == $currentUserId;
        }

        if ($canSave) {
            $langInfo->of_user_id = $currentUserId;
            $langInfo->by_user_id = $currentUserId;
            $langInfo->level = isset($data['level']) && $data['level'] >= 0 ? $data['level'] : null;
            $langInfo->details = isset($data['details']) ? $data['details'] : null;
            
            $result = $this->save($langInfo);
            return $result->old_format;
        } else {
            return array();
        }
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
