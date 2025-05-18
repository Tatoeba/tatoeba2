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
namespace App\View\Helper;

use App\View\Helper\AppHelper;
use App\Model\CurrentUser;

/**
 * Helper to display things related to private messages.
 *
 * @category PrivateMessages
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class PrivateMessagesHelper extends AppHelper
{
    public $helpers = array('Form', 'Messages');

    /**
     * function to format the text of the messages in case of answer
     *
     * @param string $content The content of the message
     * @param string $sender  The author of the original message
     *
     * @return string
     */
    public function formatReplyMessage($content, $sender)
    {
        $messNextRegExp = preg_replace(
            "#\r?\n#iU", "\n> ",
            wordwrap($content, 60)
        );
        return "\n" . format(__('{sender} wrote:'), compact('sender')) . "\n> "
            . $messNextRegExp;
    }

    public function getMenu($folder, $messageId, $type)
    {
        $menu = array();

        if ($folder == 'Trash') {
            $menu[] = array(
                /* @translators: button to restore a private message that has been put to trash (verb) */
                'text' => __('Restore'),
                'icon' => 'restore',
                'url' => array(
                    'action' => 'restore',
                    $messageId
                )
            );

            $menu[] = array(
                /* @translators: button to delete a private message that has already been put in the trash (verb) */
                'text' => __('Permanently delete'),
                'icon' => 'delete_forever',
                'url' => array(
                    'action' => 'delete_forever',
                    $messageId
                ),
                'confirm' => __('Are you sure?')
            );
        } else {
            $menu[] = array(
                /* @translators: button to put a private message in the trash (verb) */
                'text' => __('Delete'),
                'icon' => 'delete',
                'url' => array(
                    'action' => 'delete',
                    $messageId
                )
            );
        }

        if ($folder == 'Inbox') {
            $menu[] = array(
                'text' => __('Mark as unread'),
                'icon' => 'markunread_mailbox',
                'url' => array(
                    'action' => 'mark',
                    'Inbox',
                    $messageId
                )
            );

            if ($type == 'human') {
                $menu[] = array(
                    /* @translators: button to reply to a private message (verb) */
                    'text' => __x('button', 'Reply'),
                    'icon' => 'reply',
                    'url' => '#reply'
                );
            }
        }

        return $menu;
    }

    /**
     * Get user and label of sender/receiver for current message.
     *
     * @param  array  $msg
     * @param  string $folder
     *
     * @return array [user, label]
     */
    public function getUserAndLabel($msg, $folder)
    {
        $folder = $this->_getFolder($folder, $msg);

        if ($folder == 'Sent') {
            $user = $msg->recipient;
            $label = format(
                __('to {recipient}'),
                array('recipient' => $user->username)
            );
        } elseif ($this->_isDraftMessage($folder, $msg)) {
            $user = null;
            /* @translators: private message type (noun) */
            $label = __('Draft');
        } elseif ($msg->type == 'machine') {
            $user = null;
            $label = __('notification from Tatoeba');
        } else {
            $user = $msg->author;
            $label = format(
                __('from {sender}'),
                array('sender' => $user->username)
            );
        }

        return [$user, $label];
    }

    /**
     * If trash message, return msg's set origin index. Else, return folder.
     *
     * @param  string $folder
     * @param  array  $msg
     *
     * @return string
     */
    private function _getFolder($folder, $msg)
    {
        if (isset($msg['PrivateMessage']['origin'])) {
            return $msg['PrivateMessage']['origin'];
        }

        return $folder;
    }

    /**
     * Message is a draft message or a deleted draft message.
     *
     * @param  string  $originalFolder
     * @param  array   $msg
     *
     * @return boolean
     */
    private function _isDraftMessage($originalFolder, $msg)
    {
        return
            $msg['PrivateMessage']["draft_recpts"] != '' ||
            $originalFolder == 'Drafts';
    }
}
?>
