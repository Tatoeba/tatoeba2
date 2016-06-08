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
 * @link     http://tatoeba.org
 */

/**
 * Helper used to display a form to add a message to a wall
 *
 * @category Wall
 * @package  Help
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

class WallHelper extends AppHelper
{

    public $helpers = array(
        'Html', 'Form' , 'Date', 'ClickableLinks', 'Messages', 'Languages'
    );
    

    /**
     * create the form to add a new message
     *
     * @return void
     */

    public function displayAddMessageToWallForm()
    {
        /* we use models=>wall to force use wall, instead cakephp would have
           called "walls/save' which is not what we want
        */
        echo $this->Form->create(
            '', 
            array(
                "action" => "save",
                "class" => "message form"
            )
        );
        ?>
        
        <div class="header">
            <div class="info">
            <?php
            $user = CurrentUser::get('User');
            $this->Messages->displayAvatar($user['User']);
            ?>
            </div>
            <div class="title">
            <?php __('Add a message: '); ?>
            </div>
        </div>

        <div class="body">
            <div class="textarea">
            <?php
            echo $this->Form->textarea('content', array('lang' => '', 'dir' => 'auto'));
            echo $this->Form->hidden('replyTo', array('value'=>"" ));
            ?>
            </div>

            <div layout="row" layout-align="end center" layout-padding>
                <md-button type="submit" class="cancelFormLink md-raised">
                    <?php __('Cancel'); ?>
                </md-button>

                <md-button type="submit" class="md-raised md-primary">
                    <?php __('Send'); ?>
                </md-button>
            </div>
        </div>
        
        <?php
        echo $this->Form->end();

    }


    /**
     * Create form for editing a wall message
     *
     * @param string $message The message
     *
     * @return void
     */
    public function displayEditMessageForm($message)
    {
        $messageId = $message['Wall']['id'];
        ?>
        <div class="editWallMessage" >
        <?php
        echo $this->Form->create(
            "Wall",
            array(
                "url" => array(
                    "controller" => "wall",
                    "action" => "edit",
                    $messageId
                ),
                "class" => "message form"
            )
        );
        
        echo $this->Form->hidden('id');

        $this->Messages->displayFormHeader(__("Edit Wall Message", true));
        ?>

        <div class="body">
            <div class="textarea">
            <?php echo $this->Form->textarea('content'); ?>
            </div>

            <?php
            $cancelUrl = $this->Html->url(
                array(
                    "action" => "show_message",
                    "{$message['Wall']['id']}#message_{$message['Wall']['id']}"
                )
            );
            ?>
            <div layout="row" layout-align="end center" layout-padding>
                <md-button class="md-raised" href="<?= $cancelUrl; ?>">
                    <?php __('Cancel'); ?>
                </md-button>

                <md-button type="submit" class="md-raised md-primary">
                    <?php __('Save changes'); ?>
                </md-button>
            </div>

        </div>
        
        <?php
        echo $this->Form->end();
        ?>
        </div>
        <?php
    }



    /**
     * display username as a link to his profile
     *
     * @param string $userName nickname of the user
     *
     * @return void
     */

    public function displayLinkToUserProfile($userName)
    {
        echo $this->Html->link(
            $userName,
            array(
                "controller"=>"user",
                "action"=>"profile",
                $userName
            ),
            array(
                "title"=>__("View this user's profile", true)
            )
        );
    }

    /**
     * Create the ul containing all the replies and subreplies
     *
     * @param array $children the reply to be displayed with nested inside
     *                        "Wall" (message itselft)
     *                        "User" the owner of this message
     *                        "children" the replies of this message
     *                        "Permission" the right current user have on this
     *                         message
     *
     * @return void
     */
    private function _displayAllReplies($children)
    {
        if (!empty($children)) {
            foreach ($children as $child) {
                $this->createReplyDiv(
                    // this is because the allMessages array
                    // is indexed with message Id
                    $child['Wall'],
                    $child['User'],
                    $child['children'],
                    $child['Permissions']
                );
            }
        }
    }

    /**
     * create the visual representation of the root message of a thread
     *
     * @param array $message     A simple array with only the information about
     *                           the message
     * @param array $author      Same as $message but for the message's author
     * @param array $permissions Array of the permisions current user have on
     *                           This message
     *
     * @return void
     */

    public function createRootDiv($message, $author, $permissions)
    {
        $writerImage = $author['image'];
        $writerName  = $author['username'];

        if (empty($writerImage)) {
            $writerImage = 'unknown-avatar.png';
        }

        $messageId = $message['id'];
        $menu = $this->_getMenuFromPermissions($message, $permissions);
        ?>
        <div class="root">
            <?php
            $this->Messages->displayMessage(
                $message,
                $author,
                null,
                $menu
            );
            ?>
        </div>
    <?php
    }

    /**
     * Create the div containing a reply to a message and all the sub reply
     * the call is recursive
     *
     * @param array $message           the reply to be displayed
     * @param array $owner             Information about the message's author
     * @param array $children          Replies for this message
     * @param array $permissions permisions the current user have
     *                                 on this message
     *
     * @return void
     */
    public function createReplyDiv($message, $owner, $children, $permissions)
    {
        $messageId = $message['id'];
        ?>
        <div class="thread" id="message_<?php echo $messageId; ?>">

        <?php
        $menu = $this->_getMenuFromPermissions($message, $permissions);
        $this->Messages->displayMessage(
            $message,
            $owner,
            null,
            $menu
        );
        ?>

        <?php ?><div class="replies" id="messageBody_<?php echo $messageId; ?>"><?php
        if (!empty($children)) {
            $this->_displayToggleButton($messageId);

            $this->_displayAllReplies(
                $children
            );
        }
        ?></div><?php ?>

        </div>
    <?php
    }

    private function _displayToggleButton($messageId)
    {
        echo '<div class="toggleRepliesButton hideReplies"
                id="hide_replies_button_'.$messageId.'" 
                onclick="toggleReplies('.$messageId.')">
                '.__('hide replies', true).'</div>';
        echo '<div class="toggleRepliesButton showReplies"
                style="display:none;"
                id="show_replies_button_'.$messageId.'" 
                onclick="toggleReplies('.$messageId.')">
                '.__('show replies', true).'</div>';
    }


    /**
     * Create a whole thread from a root message and its children
     *
     * @param array $message     Root message array
     * @param array $author      Root message's author array.
     * @param array $permissions Root message permissions for current user.
     * @param array $children    Nested array of children and children of
     *                           children.
     *
     * @return return void
     */

    public function createThread($message, $author, $permissions, $children)
    {

        $messageId = $message['id'];

        echo '<div id="message_'.$messageId.'" class="topThread" >'."\n";
        // Root message
        $this->createRootDiv(
            $message,
            $author,
            $permissions
        );

        // replies
        echo '<div class="replies" id="messageBody_'.$messageId .'" >';
        if (!empty($children)) {
            $this->_displayToggleButton($messageId);

            foreach ($children as $child ) {
                $this->createReplyDiv(
                    // this is because the allMessages array
                    // is indexed with message Id
                    $child['Wall'],
                    $child['User'],
                    $child['children'],
                    $child['Permissions']
                );
            }
        }
        echo '</div>';
        echo '</div>';


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
            __('by {messageAuthor}', true),
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
            'action' => 'index#message_'.$id
        );
        echo $this->Html->link('>>>', $pathToWallMessage);
        ?>
        </div>

        </div>
        <?php
    }


    private function _getMenuFromPermissions($message, $permissions)
    {
        $menu = array();
        $messageId = $message['id'];
        $hidden = $message['hidden'];

        if (CurrentUser::isAdmin()) {
            if ($hidden) {
                $hiddenLinkText = __d('admin', 'unhide', true);
                $hiddenLinkAction = 'unhide_message';
            } else {
                $hiddenLinkText = __d('admin', 'hide', true);
                $hiddenLinkAction = 'hide_message';
            }

            // hide/unhide link, for when people start acting like kids and stuff
            $menu[] = array(
                'text' => $hiddenLinkText,
                'url' => array(
                    "controller" => "wall",
                    "action" => $hiddenLinkAction,
                    $messageId
                )
            );
        }

        if ($permissions['canEdit']) {
            $menu[] = array(
                'text' => __("edit", true),
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
                'text' => __('delete', true),
                'url' => array(
                    "controller"=>"wall",
                    "action"=>"delete_message",
                    $messageId
                ),
                'confirm' => __('Are you sure?', true)
            );
        }

        if ($permissions['canReply']) {
            $replyLinkId = 'reply_' . $messageId;
            $replyClasses = 'replyLink ' . $messageId;
            $menu[] = array(
                'text' => __("reply", true),
                'url' => null,
                'class' => $replyClasses,
                'id' => $replyLinkId
            );
        }

        // message link
        $menu[] = array(
            'text' => '#',
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
