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
 * @link     https://tatoeba.org
 */
namespace App\Model;

use App\Model\Entity\User;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\I18n\Time;

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
 * @link     https://tatoeba.org
 */


class CurrentUser
{
    private static $_auth;
    private static $_profileLanguages;


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
        self::$_auth = array('User' => $user);
        self::_setProfileLanguages();
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
        if (!self::$_auth) {
            return null;
        }

        if (strpos($path, 'User') !== 0) {
            $path = sprintf('User.%s', $path);
        }

        return Hash::get(self::$_auth, $path);
    }

    public static function getSetting($setting)
    {
        if (CurrentUser::isMember()) {
            return CurrentUser::get("settings.$setting");
        } else {
            return User::$defaultSettings[$setting];
        }
    }

    /**
     * Indicates if current user is admin or not.
     *
     * @return bool
     */
    public static function isAdmin()
    {
        return self::get('role') == User::ROLE_ADMIN;
    }


    /**
     * Indicates if current user is moderator or not.
     *
     * @return bool
     */
    public static function isModerator()
    {
        return in_array(self::get('role'), User::ROLE_CORPUS_MAINTAINER_OR_HIGHER);
    }


    /**
     * Indicates if current user is trusted or not.
     *
     * @return bool
     */
    public static function isTrusted()
    {
        return in_array(self::get('role'), User::ROLE_ADV_CONTRIBUTOR_OR_HIGHER);
    }


    /**
     * Indicates if current user is a member or not, in other words: did he/she
     * validated his/her registration.
     *
     * @return bool
     */
    public static function isMember()
    {
        return in_array(self::get('role'), User::ROLE_CONTRIBUTOR_OR_HIGHER);
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
     * Indicates if the current user can edit sentences
     * that belong to the user of the given user id.
     *
     * @param string $id Id of the owner of the sentence.
     *
     * @return bool
     */
    public static function canEditSentenceOfUserId($id)
    {
        if (!self::isMember()) {
            return false;
        }

        $belongsToCurrentUser = (self::get('id') == $id);
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
     * Indicates if the current user can remove a sentence.
     * Specify either $ownerId or $ownerName.
     *
     * @param int $sentenceId Id of the sentence.
     * @param int $ownerId User id of the owner of the sentence.
     * @param int $ownerName User name of the owner of the sentence.
     *
     * @return bool True if he can, False otherwise.
     */
    public static function canRemoveSentence($sentenceId, $ownerId = null, $ownerName = null)
    {
        if (!self::isMember()) {
            return false;
        }

        if (self::isModerator()) {
            return true;
        }

        $isOwner = (
            self::get('id') == $ownerId ||
            self::get('username') == $ownerName
        );
        if (!$isOwner) {
            return false;
        }

        $Link = TableRegistry::get('Links');
        $hasTranslations = $Link->find()
            ->where(['sentence_id' => $sentenceId])
            ->first();
        if (!$hasTranslations) {
            return true;
        }

        return false;
    }

    /**
     * Indicates if the current user can edit a transcription.
     * Specify both $sentenceOwnerId and $transcrOwnerId.
     *
     * @param int $transcrOwnerId User id of the owner of the transcription.
     * @param int $sentenceOwnerId User id of the owner of the sentence.
     *
     * @return bool True if the current user can, False otherwise.
     */
    public static function canEditTranscription($transcrOwnerId, $sentenceOwnerId)
    {
        if (!CurrentUser::isMember()) {
            return false;
        }

        $currentUserId = self::get('id');
        return ($transcrOwnerId === null && CurrentUser::isTrusted())
               || $sentenceOwnerId === $currentUserId
               || $transcrOwnerId === $currentUserId
               || CurrentUser::isModerator();
    }

    /**
     * A user is new if they registered within the last 14 days
     *
     * @return bool
     */
    public static function isNewUser()
    {
        $since = new Time(self::get('since'));
        
        return $since->wasWithinLast('2 weeks');
    }

    /**
     * Retrieve correctness that the user has set for a certain sentence.
     *
     * @param int $sentenceId Id of the sentence.
     *
     * @return int
     */
    public static function correctnessForSentence($sentenceId)
    {
        if (!self::isMember()) {
            return false;
        }

        $userId = self::get('id');
        $UsersSentences = TableRegistry::get('UsersSentences');
        return $UsersSentences->correctnessForSentence($sentenceId, $userId);
    }


    /**
     * Languages that the user has set in their settings, to filter the languages
     * in which translations are displayed.
     *
     * @return array
     */
    public static function getLanguages()
    {
        $lang = CurrentUser::get('settings.lang');

        if (empty($lang)) {
            return null;
        }

        $langArray = explode(',', $lang);
        return $langArray;
    }


    /**
     * Languages added in profile.
     *
     * @return array
     */
    public static function getProfileLanguages()
    {
        return self::$_profileLanguages;
    }

    private static function _setProfileLanguages()
    {
        $UsersLanguages = TableRegistry::get('UsersLanguages');
        $userId = self::get('id');
        $languages = $UsersLanguages->getLanguagesOfUser($userId);
        $languageCodes = array();
        foreach($languages as $lang) {
            $languageCodes[] = $lang->language_code;
        }

        self::$_profileLanguages = $languageCodes;
    }

    public static function canEditLicenseOfSentence($sentence)
    {
        $isOwner = self::get('id') == $sentence->user_id;
        $canSwitchLicense = self::getSetting('can_switch_license');
        $noIssues = $sentence->license != '';
        return ($isOwner && $canSwitchLicense && $noIssues) || self::isAdmin();
    }
    
    public static function hasAcceptedNewTermsOfUse()
    {
        return !self::isMember() || self::getSetting('new_terms_of_use') == User::TERMS_OF_USE_LATEST_VERSION;
    }
    
    public static function canAdoptOrUnadoptSentenceOfUser($user)
    {
        if (!$user || !$user->id || $user->id === self::get('id')) {
            return self::isMember();
        } else {
            $userAccountDeactivated = isset($user->role) ?
                in_array($user->role, [User::ROLE_SPAMMER, User::ROLE_INACTIVE]) : false;
            return self::isTrusted() && $userAccountDeactivated;
        }
    }

    public static function canMarkSentencesOfUser($user)
    {
        $userBlocked = $user->level == -1;
        $userSuspended = $user->role == User::ROLE_SPAMMER;
        return (CurrentUser::isAdmin() && ($userBlocked || $userSuspended));
    }
}
