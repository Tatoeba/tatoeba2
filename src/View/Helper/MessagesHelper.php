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
namespace App\View\Helper;

use Cake\Core\Configure;
use App\View\Helper\AppHelper;
use App\Model\CurrentUser;
use \Datetime;

/**
 * Helper for messages.
 *
 * @category SentenceComments
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */


class MessagesHelper extends AppHelper
{
    public $helpers = array('Html', 'ClickableLinks');

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

    /**
     * @param string $content     Text of the comment.
     *
     * @return string The comment body formatted for HTML display.
     */
    public function formatContent($content) {
        $content = htmlentities($content, ENT_QUOTES, Configure::read('App.encoding'));

        // Convert sentence mentions to links
        $content = $this->ClickableLinks->clickableSentence($content);

        // Make URLs clickable
        $content = $this->ClickableLinks->clickableURL($content);

        // Convert linebreaks to <br/>
        $content = nl2br($content);

        return $content;
    }


    /**
     * Displays the preview (first X characters) of a message. 
     * If possible, it will not cut the message in the middle of a word or a link.
     * 
     * @param  String  $content     The whole content.
     * @param  integer $length      Number of characters of the preview.
     * @param  integer $extraLength Tells how far we should search for a "space"
     *                              character, when trying to not cut the text
     *                              in the middle of a word/link.
     * 
     * @return String               Preview text
     */
    public function preview($content, $length = 200, $extraLength = 100)
    {
        $contentBefore = mb_substr($content, 0, $length);
        $contentAfter = mb_substr($content, $length);

        $spaceAfter = mb_strpos($contentAfter, " ");
        $newLineAfter = mb_strpos($contentAfter, PHP_EOL);
        if (!$spaceAfter || $newLineAfter < $spaceAfter) {
            $spaceAfter = $newLineAfter;
        }

        $hasLink = $this->ClickableLinks->hasClickableLink($content);

        $formatContent = true;

        if ($spaceAfter && $spaceAfter < $extraLength) {
            
            // We want to display 200 + a few more charafters. The few more
            // characters are the ones that are before the 1st "space" that we find
            // after the 200 characters.
            $lengthToCut = $length + $spaceAfter;
            $previewContent = mb_substr($content, 0, $lengthToCut);
            $displayElipsis = mb_strlen($content) > $lengthToCut;

        } else if ($hasLink && mb_strlen($content) <= $length + $extraLength) {

            // Normally, if fall in this case, then we're either trying to cut
            // a text in a language that has no space, or we're cutting the text
            // in a middle of an URL. In this case, if the message is not too long
            // we display it entirely.
            $previewContent = $content;
            $displayElipsis = false;

        } else {

            // If we can't do a "soft" truncation, then we just hard truncate.
            // In case of hard truncation, we don't format the text.
            $previewContent = mb_substr($content, 0, $length);
            $displayElipsis = mb_strlen($content) > $length;
            $formatContent = false;

        }

        if ($formatContent) {
            $previewContent = $this->formatContent($previewContent);
        } else {
            $previewContent = nl2br(h($previewContent));
        }

        if ($displayElipsis) {
            $previewContent .= ' [...]';
        }

        return $previewContent;
    }
}
