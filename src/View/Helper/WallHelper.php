<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  Allan SIMON <allan.simon@supinfo.com>
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
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
namespace App\View\Helper;

use App\View\Helper\AppHelper;
use App\Model\CurrentUser;


/**
 * Helper used to display a form to add a message to a wall
 *
 * @category Wall
 * @package  Help
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

class WallHelper extends AppHelper
{

    public $helpers = array(
        'Html', 'Date', 'ClickableLinks', 'Languages', 'Messages'
    );

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
    private function preview($content, $length = 200, $extraLength = 100)
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
            $previewContent = $this->Messages->formatContent($previewContent);
        } else {
            $previewContent = nl2br(h($previewContent));
        }

        if ($displayElipsis) {
            $previewContent .= ' [...]';
        }

        return $previewContent;
    }

    /**
     * Display wall message preview (on homepage).
     *
     * @param int    $id      Id of the message.
     * @param string $author  Author of the message.
     * @param string $content Content of the message.
     * @param string $date    Date of the message.
     *
     * @return void
     */
    public function messagePreview($id, $author, $content, $date)
    {
        ?>
        <div class="lastWallMessages">

        <div class="header">
        <?php
        echo $this->Date->ago($date);
        // Text of link
        $text = format(
            __('by {messageAuthor}'),
            array('messageAuthor' => $author)
        );
        // Path of link
        $pathToUserProfile = array(
            "controller"=>"user",
            "action"=>"profile",
            $author
        );
        // Link
        echo $this->Html->link(' '.$text, $pathToUserProfile);
        ?>
        </div>

        <?php
        $preview = $this->preview($content, 200, 100);
        echo $this->Languages->tagWithLang(
            'div', '', $preview,
            array('class' => 'body', 'escape' => false)
        );
        ?>

        <div class="link">
        <?php
        $pathToWallMessage = array(
            'controller' => 'wall',
            'action' => 'index',
            '#' => 'message_'.$id
        );
        echo $this->Html->link('>>>', $pathToWallMessage);
        ?>
        </div>

        </div>
        <?php
    }


    public function getMenuFromPermissions($message, $permissions)
    {
        $menu = array();
        $messageId = $message['id'];
        $hidden = $message['hidden'];

        if ($permissions['canReport']) {
            $menu[] = array(
                /* @translators: flag button to report a wall post (verb) */
                'text' => __('Report'),
                'icon' => 'flag',
                'url' => array(
                    'controller' => 'report_content',
                    'action' => 'wall_post',
                    $messageId,
                )
            );
        }
        if ($permissions['canPM']) {
            $menu[] = array(
                'text' => __('Send message'),
                'icon' => 'mail',
                'url' => array(
                    'controller' => 'private_messages',
                    'action' => 'write',
                    $message->user->username
                )
            );
        }

        if (CurrentUser::isAdmin()) {
            if ($hidden) {
                $hiddenLinkText = __d('admin', 'unhide');
                $hiddenLinkAction = 'unhide_message';
            } else {
                $hiddenLinkText = __d('admin', 'hide');
                $hiddenLinkAction = 'hide_message';
            }

            // hide/unhide link, for when people start acting like kids and stuff
            $menu[] = array(
                'text' => $hiddenLinkText,
                'icon' => 'visibility_off',
                'url' => array(
                    "controller" => "wall",
                    "action" => $hiddenLinkAction,
                    $messageId
                )
            );
        }

        if ($permissions['canEdit']) {
            $menu[] = array(
                /* @translators: edit button on a wall post (verb) */
                'text' => __('Edit'),
                'icon' => 'edit',
                'url' => array(
                    'controller' => 'wall',
                    'action' => 'edit',
                    $messageId
                )
            );
        }


        if ($permissions['canDelete']) {
            // delete link
            $menu[] = array(
                /* @translators: delete button on a wall post (verb) */
                'text' => __('Delete'),
                'icon' => 'delete',
                'url' => array(
                    "controller"=>"wall",
                    "action"=>"delete_message",
                    $messageId
                ),
                'confirm' => __('Are you sure?')
            );
        }

        if ($permissions['canReply']) {
            $replyLinkId = 'reply_' . $messageId;
            $replyClasses = 'replyLink ' . $messageId;
            $menu[] = array(
                /* @translators: reply button on a wall post (verb) */
                'text' => __x('button', 'Reply'),
                'icon' => 'reply',
                'url' => null,
                'class' => $replyClasses,
                'id' => $replyLinkId
            );
        }

        return $menu;
    }
}
