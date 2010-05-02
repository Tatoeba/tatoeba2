<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 * Static class that stores the Auth information of the user. This is the only
 * solution I found to be able to easily access the Auth::user() information from
 * everywhere. It also enables to retrieve the current user's permissions from 
 * everywhere. 
 *
 * NOTE: Still not sure if it's the best idea, regarding the permissions part.
 *
 * @category CurrentUser
 * @package  Models
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class CurrentUser extends AppModel
{
    public $useTable = null;
    
    private static $auth;
    
    
    /**
     * Store the Auth information of the user, i.e. the value is the one returned 
     * by Auth::user(). Used in app_controller.php, in beforeFilter().
     * 
     * @param array $user 
     *
     * @return void
     */
    public static function store($user)
    {
        self::$auth = $user;
    }
    
    
    /**
     * Returns value of the Auth::user() array. Slightly modified version of:
     *
     * Copyright (c) 2008 Matt Curry
     * www.PseudoCoder.com
     * http://github.com/mcurry/cakephp/tree/master/snippets/static_user
     * http://www.pseudocoder.com/archives/2008/10/06/accessing-user-sessions-from-models-or-anywhere-in-cakephp-revealed/
     *
     * @author      Matt Curry <matt@pseudocoder.com>
     * @license     MIT
     */
    public static function get($path) {
        $path = str_replace('.', '/', $path);
        if (strpos($path, 'User') !== 0) {
            $path = sprintf('User/%s', $path);
        }
        
        if (strpos($path, '/') !== 0) {
            $path = sprintf('/%s', $path);
        }

        $value = Set::extract($path, self::$auth);

        if (!$value) {
            return false;
        }
        
        return $value[0];
    }
    
    
    /**
     * Indicates if current user is admin or not.
     * 
     * @return bool
     */
    public static function isAdmin()
    {
        return self::get('group_id') == 1;
    }
    
    
    /**
     * Indicates if current user is trusted or not.
     * 
     * @return bool
     */
    public static function isTrusted()
    {
        return self::get('group_id') && self::get('group_id') < 4;
    }
    
    
    /**
     * Indicates if current user is a member or not, in other words: did he/she
     * validated his/her registration.
     * 
     * @return bool
     */
    public static function isMember()
    {
        return self::get('group_id') && self::get('group_id') < 5;
    }
    
    /**
     * Indicates if current user is owner of the sentence with given id.
     *
     * @param int $sentenceId Id of the sentence.
     * 
     * @return bool
     */
    public static function isOwnerOfSentence($sentenceId)
    {
        $Sentence = ClassRegistry::init('Sentence');
        $result = $Sentence->find(
            'first',
            array(
                'fields' => array(),
                'conditions' => array(
                    'Sentence.id' => $sentenceId,
                    'Sentence.user_id' => self::get('id')
                ),
                'contain' => array()
            )
        );
        return !empty($result);
    }
    
    
    /**
     * Indicates if current user can link/unlink translations to the sentence of
     * given id.
     *
     * @param int $sentenceId Id of the main sentence.
     * 
     * @return bool
     */
    public static function canLinkAndUnlink($sentenceId)
    {
        if (self::isAdmin()) {
            return true;
        }
        
        $sentenceBelongsToUser = self::isOwnerOfSentence($sentenceId);
        
        return $sentenceBelongsToUser && self::isTrusted();
    }
}
?>