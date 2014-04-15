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
    public $useTable = false;
    
    private static $_auth;
    
    
    /**
     * Store the Auth information of the user, i.e. the value is the one returned 
     * by Auth::user(). Used in app_controller.php, in beforeFilter().
     * 
     * @param array $user Value returned by Auth::user()
     *
     * @return void
     */
    public static function store($user)
    {
        self::$_auth = $user;
    }
    
    
    /**
     * Returns value of the Auth::user() array. Slightly modified version of:
     *
     * Copyright (c) 2008 Matt Curry
     * www.PseudoCoder.com
     * http://github.com/mcurry/cakephp/tree/master/snippets/static_user
     * http://www.pseudocoder.com/archives/2008/10/06/accessing-user-sessions-from-models-or-anywhere-in-cakephp-revealed/
     *
     * @param string $path
     *
     * @author      Matt Curry <matt@pseudocoder.com>
     * @license     MIT
     *
     * @return string
     */
    public static function get($path)
    {
        $path = str_replace('.', '/', $path);
        if (strpos($path, 'User') !== 0) {
            $path = sprintf('User/%s', $path);
        }
        
        if (strpos($path, '/') !== 0) {
            $path = sprintf('/%s', $path);
        }

        $value = Set::extract($path, self::$_auth);

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
     * Indicates if current user is moderator or not.
     * 
     * @return bool
     */
    public static function isModerator()
    {
        return self::get('group_id') && self::get('group_id') < 3;
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
     * Indicates if current user can link/unlink translations to the sentence of
     * given id. TODO something is wrong here
     *
     * @param string $username Name of the sentence owner (?).
     * 
     * @return bool
     */
    public static function canLinkWithSentenceOfUser($username)
    {
        if (!self::isMember()) {
            return false;
        }
        
        if (self::isModerator()) {
            return true;
        }
        
        $belongsToCurrentUser = (self::get('username') == $username);
        return $belongsToCurrentUser && self::isTrusted();
    }
    
    
    /**
     * Indicates if current user can edit sentence of user with give username.
     *
     * @param string $username Username of owner of the sentence.
     * 
     * @return bool
     */
    public static function canEditSentenceOfUser($username)
    {
        if (!self::isMember()) {
            return false;
        }
        
        $belongsToCurrentUser = (self::get('username') == $username);
        return $belongsToCurrentUser || self::isModerator();
    }


    /**
     * Indicates if current user can remove a given tag on a given sentence.
     *
     * @param int $taggerId Id of the guy who add this tag on the current sentence.
     *
     * @return bool True if he can, False otherwise.
     */
    public static function canRemoveTagFromSentence($taggerId)
    {
        if (!self::isMember()) {
            return false;
        }
        
        if (self::isModerator()) {
            return true;
        }
        
        $TagAddedByCurrentUser = (self::get('id') == $taggerId);
        return $TagAddedByCurrentUser;


    }
    
    /**
     * A user is new if they registered within the last 14 days
     * 
     * @return bool
     */
    public static function isNewUser()
    {
        $isNewUser = false;
        $daysToWait = "14";
        
        $today = new DateTime("now");
        $since = new DateTime(self::get('since'));
        $userAge = $since->diff($today);
        
        if ($userAge->days <= $daysToWait) {
            $isNewUser = true;
        }
        
        return $isNewUser;
    }
    
    /**
     * Indicates if sentence of given id has been favorited by current user.
     *
     * @param int $sentenceId Id of the sentence.
     * 
     * @return bool
     */
    public static function hasFavorited($sentenceId)
    {
        $userId = self::get('id');
        $FavoritedBy = ClassRegistry::init('FavoritedBy');
        return $FavoritedBy->isSentenceFavoritedByUser($sentenceId, $userId);
    }

    /**
     * Get user's ip, even if behind a proxy (anyway tatoeba is currently
     * behind a proxy
     *
     * @return IP
     */
    public function getIp()
    {
        if (getenv("HTTP_CLIENT_IP")) {
            return getenv("HTTP_CLIENT_IP"); 
        } elseif (getenv("HTTP_X_FORWARDED_FOR")) {
            return getenv("HTTP_X_FORWARDED_FOR"); 
        } else { 
            return getenv("REMOTE_ADDR"); 
        }
    }
    
    
    /** 
     * Get user's languages.
     *
     * @return array
     */
    public function getLanguages()
    {
        $lang = CurrentUser::get('lang');
        
        if (empty($lang)) {
            return null;
        }
        
        $langArray = explode(',', $lang);
        return $langArray;
    }
    
}
?>
