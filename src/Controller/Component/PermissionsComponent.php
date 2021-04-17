<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
namespace App\Controller\Component;

use Cake\Controller\Component;
use App\Model\CurrentUser;


/**
 * Component for permissions.
 *
 * @category Default
 * @package  Components
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class PermissionsComponent extends Component
{
    public $components = array('Auth');

    /**
     * Check which options user can access to and returns
     * data that is needed for the sentences menu.
     *
     * @param array $sentence      Sentence.
     * @param int   $currentUserId Id of currently logged in user.
     *
     * @return array
     */
    public function getSentencesOptions($sentence, $currentUserId)
    {
        $sentenceOwnerId = $sentence['Sentence']['user_id'];

        $specialOptions = array(
            'canComment' => false,
            'canEdit' => false,
            'canLinkAndUnlink' => false,
            'canDelete' => false,
            'canAdopt' => false,
            'canLetGo' => false,
            'canTranslate' => false,
            'canFavorite' => false,
            'canUnFavorite' => false,
            'canAddToList' => false,
            'belongsToLists' => array()
        );

        if ($this->Auth->user('id')) {
            // -- comment --
            $specialOptions['canComment'] = true;

            // -- translate --
            $specialOptions['canTranslate'] = true;

            // -- favorite --
            $specialOptions['canFavorite'] = true;
            // if we have already favorite it then we just can unfavorite it
            // is_array is here to avoid a warning when favorites_users is empty
            if (is_array($sentence['Favorites_users'])) {
                foreach ($sentence['Favorites_users'] as $favoritingUser) {
                    if ($favoritingUser['user_id'] == $this->Auth->user('id')) {
                        $specialOptions['canUnFavorite'] = true;
                        $specialOptions['canFavorite'] = false;
                    }
                }
            }

            // -- edit --
            if (CurrentUser::isModerator()) {
                $specialOptions['canEdit'] = true;
            }

            // -- edit and adopt --
            if ($sentenceOwnerId == $currentUserId) {
                $specialOptions['canEdit'] = true;
                $specialOptions['canLetGo'] = true;
            }

            // -- link/unlink --
            // It's important to set this permission after canEdit has been set.
            if (CurrentUser::isTrusted() && $specialOptions['canEdit']) {
                $specialOptions['canLinkAndUnlink'] = true;
            }

            // -- delete --
            $specialOptions['canDelete'] = CurrentUser::isAdmin();


            // -- add to list --
            $specialOptions['canAddToList'] = true;
            if (isset($sentence['SentencesList'])) {
                foreach ($sentence['SentencesList'] as $list) {
                    array_push($specialOptions['belongsToLists'], $list['id']);
                }
            }

            // -- adopt --
            if ($sentenceOwnerId == null OR $sentenceOwnerId == 0) {

                $specialOptions['canAdopt'] = true;

            }
        }

        return $specialOptions;
    }

    /**
     * Convenience function to get permissions for an array of comments
     *
     * @param array $comments Array of comments.
     *
     * @return array
     */

    public function getCommentsOptions($comments) {
        $commentsPermissions = array();
        foreach ($comments as $comment) {
            if (isset($comment['SentenceComment'])) {
                $comment = $comment['SentenceComment'];
            }

            $commentPermissions = $this->getCommentOptions(
                $comment->user->id ?? null
            );
            array_push($commentsPermissions, $commentPermissions);
        }
        return $commentsPermissions;
    }

    /**
     * Get comment permissions for the current user
     *
     * @param int   $ownerId          Id of the comment owner.
     *
     * @return array
     */

    public function getCommentOptions($ownerId) {
        $rightsOnComment = array(
            "canDelete" => false,
            "canEdit" => false,
            "canHide" => false,
            "canPM" => false
        );
        if (!CurrentUser::isMember()) {
            return $rightsOnComment;
        }

        if (CurrentUser::isAdmin()) {
            $rightsOnComment['canDelete'] = true;
            $rightsOnComment['canEdit'] = true;
            $rightsOnComment['canHide'] = true;
        }

        if ($ownerId === CurrentUser::get('id')) {
            $rightsOnComment['canDelete'] = true;
            $rightsOnComment['canEdit'] = true;
        }

        if ($ownerId && CurrentUser::get('id') != $ownerId) {
            $rightsOnComment['canPM'] = true;
        }

        return $rightsOnComment;
    }

    /**
     * Get permissions for a wall message for the current user
     *
     * @param boolean $lastInThread   Whether the message is the last in the thread,
     *                                i.e. it has no children
     * @param int     $ownerId        Id of the message owner
     * @param int     $currentUserId  Id of currently logged in user
     *
     * @return array
     */
    public function getWallMessageOptions($lastInThread, $ownerId, $currentUserId) {
        $rightsOnWallMessage = array(
            "canReply"  => false,
            "canDelete" => false,
            "canEdit" => false,
            "canPM" => false
        );
        if (empty($currentUserId)) {
            return $rightsOnWallMessage;
        }

        $canModify = $ownerId === $currentUserId || CurrentUser::isAdmin();

        if ($lastInThread && $canModify) {
            $rightsOnWallMessage['canDelete'] = true;
        }

        if ($canModify) {
            $rightsOnWallMessage['canEdit'] = true;
        }

        if ($ownerId && $ownerId !== $currentUserId) {
            $rightsOnWallMessage['canPM'] = true;
        }

        $rightsOnWallMessage['canReply'] = true;

        return $rightsOnWallMessage;
    }

    /**
     * Convenience function to get permissions for an array of wall's messages
     *
     * @param array $messages         Array of comments.
     * @param int   $currentUserId    Id of the requester.
     *
     * @return array
     */

    public function  getWallMessagesOptions(
        $messages,
        $currentUserId
    ) {

        foreach ($messages as $i=>$message) {
            $lastInThread = empty($message->children);
            $messages[$i]['Permissions'] = $this->getWallMessageOptions(
                $lastInThread,
                $message->user->id ?? null,
                $currentUserId
            );

            if (!$lastInThread) {
                $messages[$i]['children'] = $this->getWallMessagesOptions(
                    $message['children'],
                    $currentUserId
                );
            }

        }

        return $messages;
    }
}
