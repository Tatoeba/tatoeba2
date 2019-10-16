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
        'Html', 'Form' , 'Date', 'ClickableLinks', 'Messages', 'Languages', 'Url'
    );

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
        $preview = $this->Messages->preview($content, 200, 100);
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
                'text' => __("edit"),
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
                'text' => __('delete'),
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
                'text' => __("reply"),
                'icon' => 'reply',
                'url' => null,
                'class' => $replyClasses,
                'id' => $replyLinkId
            );
        }

        // message link
        $menu[] = array(
            'text' => '#',
            'icon' => 'link',
            'url' => array(
                'controller' => 'wall',
                'action' => 'show_message',
                $messageId,
                '#' => "message_" .$messageId
            )
        );

        return $menu;
    }

}

?>
