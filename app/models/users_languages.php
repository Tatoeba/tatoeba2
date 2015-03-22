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
    public $belongsTo = array('User' => array('foreignKey' => 'of_user_id'));


    public function getLanguagesOfUser($userId)
    {
        $languages = $this->find(
            'all',
            array(
                'conditions' => array('of_user_id' => $userId),
                'order' => 'level DESC',
                'contain' => array()
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
                'contain' => array(),
                'order' => 'level DESC',
                'contain' => array()
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
                ),
                'contain' => array()
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
                'fields' => array('language_code', 'by_user_id'),
                'contain' => array()
            )
        );

        return $languageInfo['UsersLanguages'];
    }


    public function getUsersForLanguage($lang)
    {
        $result = $this->find(
            'all',
            array(
                'conditions' => array(
                    'language_code' => $lang
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
                'order' => 'UsersLanguages.level DESC'
            )
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
                'order' => 'total DESC',
                'contain' => array()
            )
        );

        return $result;
    }
}
