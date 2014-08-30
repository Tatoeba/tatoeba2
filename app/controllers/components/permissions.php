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
 * @link     http://tatoeba.org
 */

/**
 * Component for permissions.
 *
 * @category Default
 * @package  Components
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class PermissionsComponent extends Object
{
    public $components = array('Auth', 'Acl');

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
            if ($this->Auth->user('group_id') < 3) {
                $specialOptions['canEdit'] = true;
            }

            // -- edit and adopt --
            if ($sentenceOwnerId == $currentUserId) {
                $specialOptions['canEdit'] = true;
                $specialOptions['canLetGo'] = true;
            }

            // -- link/unlink --
            // It's important to set this permission after canEdit has been set.
            if ($this->Auth->user('group_id') < 4 && $specialOptions['canEdit']) {
                $specialOptions['canLinkAndUnlink'] = true;
            }

            // -- delete --
            $specialOptions['canDelete'] = ($this->Auth->user('group_id') < 2);


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
     * @param array $comments         Array of comments.
     * @param int   $currentUserId    Id of the requester.
     * @param int   $currentUserGroup Group of the requester.
     *
     * @return array
     */

    public function  getCommentsOptions(
        $comments,
        $currentUserId,
        $currentUserGroup
    ) {
        $commentsPermissions = array();
        foreach ($comments as $comment) {
            $commentPermissions = $this->getCommentOptions(
                $comment,
                $comment['User']['id'],
                $currentUserId,
                $currentUserGroup
            );
            array_push($commentsPermissions, $commentPermissions);
        }
        return $commentsPermissions;
    }

    /**
     * Check which options user can access to and returns
     * data that is needed for the comments menu.
     *
     * @param array $comment          Comment.
     * @param int   $ownerId          Id of the comment owner.
     * @param int   $currentUserId    Id of currently logged in user.
     * @param int   $currentUserGroup Id of the user's group.
     *
     * @return array
     */

    public function getCommentOptions(
        $comment,
        $ownerId,
        $currentUserId,
        $currentUserGroup
    ) {
        $rightsOnComment = array(
            "canDelete" => false,
            "canEdit" => false
        );
        if (empty($currentUserId) || empty($currentUserGroup)) {
            return $rightsOnComment;
        }

        if ($ownerId === $currentUserId) {
            $rightsOnComment['canDelete'] = true;
        } elseif ($currentUserGroup < 2) {
            $rightsOnComment['canDelete'] = true;
        }

        if ($rightsOnComment['canDelete']) {
            $rightsOnComment['canEdit'] = true;
        } elseif ($currentUserGroup < 2) {
            $rightsOnComment['canEdit'] = true;
        }

        return $rightsOnComment;
    }

    /**
     * Check which options user can access to and returns
     * data that is needed for the message on the Wall.
     *
     * @param array $message          Message.
     * @param int   $ownerId          Id of the message owner.
     * @param int   $currentUserId    Id of currently logged in user.
     * @param int   $currentUserGroup Id of the user's group.
     *
     * @return array
     */
    public function getWallMessageOptions(
        $message,
        $ownerId,
        $currentUserId,
        $currentUserGroup
    ) {
        $rightsOnWallMessage = array(
            "canReply"  => false,
            "canDelete" => false,
            "canEdit" => false
        );
        // TODO add functions to determine options
        if (empty($currentUserId) || empty($currentUserGroup)) {
            return $rightsOnWallMessage;
        }

        if (empty($message['children'])) {
            if ($ownerId === $currentUserId) {
                $rightsOnWallMessage['canDelete'] = true;
            } elseif ($currentUserGroup < 2) {
                $rightsOnWallMessage['canDelete'] = true;
            }
        }

        if ($ownerId === $currentUserId) {
            $rightsOnWallMessage['canEdit'] = true;
        } elseif ($currentUserGroup < 2) {
            $rightsOnWallMessage['canEdit'] = true;
        }

        $rightsOnWallMessage['canReply'] = true;

        return $rightsOnWallMessage;
    }

    /**
     * Convenience function to get permissions for an array of wall's messages
     *
     * @param array $messages         Array of comments.
     * @param int   $currentUserId    Id of the requester.
     * @param int   $currentUserGroup Group of the requester.
     *
     * @return array
     */

    public function  getWallMessagesOptions(
        $messages,
        $currentUserId,
        $currentUserGroup
    ) {

        foreach ($messages as $i=>$message) {
            $messages[$i]['Permissions'] = $this->getWallMessageOptions(
                $message,
                $message['User']['id'],
                $currentUserId,
                $currentUserGroup
            );

            if (!empty($message['children'])) {
                $messages[$i]['children'] = $this->getWallMessagesOptions(
                    $message['children'],
                    $currentUserId,
                    $currentUserGroup
                );
            }

        }
        //pr($messages);
        return $messages;
    }



    public function getMenusForComments($comments)
    {
        $menus = array();
        
        foreach ($comments as $comment) {
            $menus[] = $this->getMenuForComment(
                $comment['SentenceComment'], $comment['User']
            );
        }

        return $menus;
    }

    public function getMenusForCommentsOfUser($comments, $user)
    {
        foreach ($comments as $comment) {
            $menus[] = $this->getMenuForComment($comment, $user);
        }

        return $menus;
    }

    public function getMenuForComment($comment, $user)
    {
        $menu = array(); 
        $commentId = $comment['id'];
        
        // hide
        if (CurrentUser::isAdmin()) {
            $hidden = $comment['hidden'];

            if ($hidden) {
                $hiddenLinkText = __('unhide', true);
                $hiddenLinkAction = 'unhide_message';
            } else {
                $hiddenLinkText = __('hide', true);
                $hiddenLinkAction = 'hide_message';
            }

            $menu[] = array(
                'text' => __('hide', true),
                'url' => array(
                    "controller" => "sentence_comments",
                    "action" => $hiddenLinkAction,
                    $commentId
                )
            );
        }

        $authorId = $user['id'];
        if ($authorId === CurrentUser::get('id') || CurrentUser::isAdmin()) {
            // delete
            $menu[] = array(
                'text' => __('delete', true),
                'url' => array(
                    "controller" => "sentence_comments",
                    "action" => "delete_comment",
                    $commentId
                )
            );

            // edit
            $menu[] = array(
                'text' => __('edit', true),
                'url' => array(
                    "controller" => "sentence_comments",
                    "action" => "edit",
                    $commentId
                )
            );
        }

        // PM
        if (CurrentUser::isMember() && $authorId != CurrentUser::get('id')) {
            $username = $user['username'];
            $menu[] = array(
                'text' => __('PM', true),
                'url' => array(
                    "controller" => "private_messages",
                    "action" => "write",
                    $username
                )
            );
        }

        // view
        $sentenceId = $comment['sentence_id'];
        $menu[] = array(
            'text' => '#',
            'url' => array(
                "controller" => "sentences",
                "action" => "show",
                $sentenceId
            )
        );

        return $menu;
    }
}
?>
