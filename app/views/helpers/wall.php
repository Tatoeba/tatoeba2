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

    public $helpers = array('Html', 'Form' , 'Date', 'Javascript', 'ClickableLinks');

    /**
     * create the reply link to a given message 
     *
     * @param int   $messageId   the id of the replied message
     * @param array $permissions Permissions the current user as on this message
     * @param bool  $firstTime   to know if the first time we call it
     *                           to add add or not the JS
     *
     * @return void
     */

    public function createLinks($messageId, $permissions, $firstTime = false)
    {
        if ($permissions['canDelete']) {
            // delete link
            echo $this->Html->link(
                __('delete', true),
                array(
                    "controller"=>"wall", 
                    "action"=>"delete_message",
                    $messageId
                ),
                null,
                __('Are you sure?', true)
            );
            echo ' - ';
        }
            
        if ($permissions['canReply']) { 
            // reply link
            if ($firstTime === true) {
                $this->Javascript->link('jquery.scrollTo-min.js', false);
                $this->Javascript->link('wall.reply.js', false);
            }
            $replyLinkId = 'reply_' . $messageId;
            $replyClasses = 'replyLink ' . $messageId;
            echo '<a class="'.$replyClasses.'" id="'.$replyLinkId.'" >';
             __("reply");
            echo '</a>';
            echo ' - ';
        }
        
        // message link
        echo $this->Html->link(
            '#',
            array(
                'controller' => 'wall',
                'action' => 'show_message',
                '#' => "message_" .$messageId,
                $messageId
            )
        );
    }

    /**
     * display the avatar of the one who write current message 
     *
     * @param string $userName  name of the user
     * @param string $userImage filename of user's picture
     *
     * @return void
     */
    
    public function displayMessagePosterImage($userName, $userImage)
    {
        if (empty($userImage)) {
            $userImage = 'unknown-avatar.png';
        }
        
        echo $this->Html->link(
            $this->Html->image(
                IMG_PATH . 'profiles_36/'. $userImage,
                array(
                    "alt" => $userName,
                    "title" => __("View this user's profile", true)
                )
            ),
            array(
                "controller"=>"user",
                "action"=>"profile",
                $userName
            ),
            array("escape"=>false)
        );
    }

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
        __('Add a Message : ');
        echo $this->Form->create('', array( "action" => "save"));
        echo "<fieldset>";
            echo $this->Form->textarea('content', array('label'=>""));
            echo $this->Form->hidden('replyTo', array('value'=>"" ));
        echo "</fieldset>";
        echo $this->Form->submit(__('Send', true));
        echo '<div class="divCancelFormLink" >';
            echo '<a class="cancelFormLink" >' . __("cancel", true) . '</a>';
        echo '</div>';
        echo $this->Form->end();

    }


    /**
     * display content of a message
     *
     * @param string $content Message to be rendered
     *
     * @return void
     */

    public function displayContent($content)
    {
        echo nl2br(
            $this->ClickableLinks->clickableURL(
                htmlentities(
                    $content,
                    ENT_QUOTES,
                    'UTF-8'
                )
            )
        );

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
            echo '<ul class="toto" >'; // TODO why toto ?
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
             echo '</ul>' ;
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
        ?> 
        <div class="message root">
            <?php
            $this->_displayMessageMeta(
                $message,
                $author,
                $permissions,
                true
            );
            ?>
            
            <!-- message content -->
            <div class="body" >
               <?php $this->displayContent($message['content']); ?> 
            </div>
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
     * @param array $messagePermission permisions the current user have
     *                                 on this message
     *
     * @return void
     */
    public function createReplyDiv($message,$owner,$children,$messagePermission)
    {
        $messageId = $message['id'];
        ?>
        <li class="thread" id="message_<?php echo $messageId; ?>">
        
        <div class="message">
            <!-- message meta -->
            <?php
            $this->_displayMessageMeta(
                $message,
                $owner,
                $messagePermission
            );
            ?>
            <!-- message content -->
            <div class="body">
                <?php $this->displayContent($message['content']); ?>
            </div>
        </div>
        
        <div class="replies" id="messageBody_<?php echo $messageId; ?>" >
        <?php
        if (!empty($children)) {
            $this->_displayAllReplies(
                $children
            );
        }
        ?>
        </div>
            
        </li>
    <?php
    }

    /**
     * Display the informations and menu on top of each wall messages
     *
     * @param array $message     Message to displayed informations of.
     * @param array $author      Author of the message.
     * @param array $permissions Permissions current user have on this message.
     * @param bool  $firstTime   To know if the js need to be linked.
     *
     * @return void
     */

    private function _displayMessageMeta(
        $message,
        $author,
        $permissions,
        $firstTime = false
    ) {
        $messageId = $message['id'];
        $messageDate = $message['date'];
        $userName = $author['username'];
        $userImage = $author['image'];
        ?>
        <ul class="meta" >
            <!-- reply option -->
            <li class="action">
                <?php
                $this->createLinks(
                    $messageId,
                    $permissions,
                    $firstTime
                );
                ?>
            </li>
            
            <li class="image">
                <?php
                $this->displayMessagePosterImage(
                    $userName,
                    $userImage
                )
                ?>
            </li>
            
            <li class="author">
                <?php $this->displayLinkToUserProfile($userName); ?>
            </li>
            
            <li class="date">
                <?php echo $this->Date->ago($messageDate); ?>
            </li>
        </ul>
    <?php
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
        
        echo '<li id="message_'.$messageId.'" class="topThread" >'."\n";
        // Root message
        $this->createRootDiv(
            $message, 
            $author, 
            $permissions
        );

        // replies
        echo '<div class="replies" id="messageBody_'.$messageId .'" >';
        if (!empty($children)) {
            echo '<ul>';
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
            echo '</ul>';
        }
        echo '</div>';
        echo '</li>';


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
        $text = sprintf(
            __('by %s', true), 
            $author
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
            
        <div class="body">
        <?php
        // Display only 200 first character of message
        $contentFirst200 = substr($content, 0, 200);
        echo nl2br(
            $this->ClickableLinks->clickableURL(
                htmlentities(
                    $contentFirst200,
                    ENT_QUOTES,
                    'UTF-8'
                )
            )
        );
        if (strlen($content) > 200) {
            echo ' [...]';
        }
        ?>
        </div>
        
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

}

?>
