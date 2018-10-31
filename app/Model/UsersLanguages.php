<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 SIMON   Allan   <allan.simon@supinfo.com>
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
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Model Class for users languages skill level.
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
*/
class UsersLanguages extends AppModel
{
    public $name = 'UsersLanguages';
    public $useTable = "users_languages";
    public $actsAs = array("Containable");
    public $belongsTo = array(
        'User' => array('foreignKey' => 'of_user_id'),
        'Language' => array('foreignKey' => 'language_code')
    );


    public function beforeSave($options = array())
    {
        $userId = $this->data['UsersLanguages']['of_user_id'];
        $lang = $this->data['UsersLanguages']['language_code'];
        $groupId = $this->User->getGroupOfUser($userId);

        if ($this->id) {
          $data = $this->findById($this->id);          
          $previousLevel = $data['UsersLanguages']['level'];
          $newLevel = $this->data['UsersLanguages']['level'];
          $this->Language->decrementCountForLevel($lang, $previousLevel);

          if ($previousLevel == 5 || $newLevel == 5 && $previousLevel != $newLevel) {
              if ($previousLevel > $newLevel) {
                  $this->Language->decrementCountForGroup($lang, $groupId);
              } else {
                  $this->Language->incrementCountForGroup($lang, $groupId);
              }
          }
        } else {
          $this->Language->incrementCountForGroup($lang, $groupId);
        }

        return true;
    }


    public function afterSave($created, $options = array())
    {
        $lang = $this->data['UsersLanguages']['language_code'];
        $level = $this->data['UsersLanguages']['level'];
        $this->Language->incrementCountForLevel($lang, $level);

        if ($created && $level == 5) {
            $userId = $this->data['UsersLanguages']['of_user_id'];
            $groupId = $this->User->getGroupOfUser($userId);
            $this->Language->incrementCountForGroup($lang, $groupId);
        }
    }


    public function beforeDelete($options = array())
    {
        $data = $this->findById($this->id);
        $lang = $data['UsersLanguages']['language_code'];
        $level = $data['UsersLanguages']['level'];
        $this->Language->decrementCountForLevel($lang, $level);

        if ($level == 5) {
            $userId = $data['UsersLanguages']['of_user_id'];
            $groupId = $this->User->getGroupOfUser($userId);
            $this->Language->decrementCountForGroup($lang, $groupId);
        }

        return true;
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
        $languageInfo = $this->find(
            'first',
            array(
                'conditions' => array('id' => $id),
                'fields' => array('language_code', 'by_user_id')
            )
        );

        if (isset($languageInfo['UsersLanguages'])) {
            return $languageInfo['UsersLanguages'];
        } else {
            return null;
        }
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
            $canSave = true;
        } else {
            $id = $data['id'];
            $langInfo = $this->getLanguageInfo($id);
            $canSave = $langInfo['by_user_id'] == $currentUserId;
            $data['language_code'] = $langInfo['language_code'];
        }

        if ($canSave) {
            $data['of_user_id'] = $currentUserId;
            $data['by_user_id'] = $currentUserId;
            if ($data['level'] < 0) {
                $data['level'] = null;
            }
            return $this->save($data);
        } else {
            return array();
        }
    }

    public function deleteUserLanguage($id, $currentUserId)
    {
        $langInfo = $this->getLanguageInfo($id);
        $canDelete = $langInfo['by_user_id'] == $currentUserId;

        if ($canDelete) {
            return $this->delete($id, false);
        } else {
            return false;
        }
    }
}
