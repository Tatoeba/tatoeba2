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
        echo $this->Html->link(
            $this->Html->image(
                'profiles/'. $userImage,
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
            echo $this->Form->input('content', array('label'=>""));
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
     * @param array $message     the reply to be displayed
     * @param array $allMessages contains all the messages 
     * @param array $permissions permisions the current user have on each messages
     *
     * @return void
     */
    private function _displayAllReplies($message,$allMessages,$permissions)
    {
        if (!empty($message['Reply'])) {
            echo '<ul class="toto" >'; // TODO why toto ?
            foreach ($message['Reply'] as $reply) {
                $this->createReplyDiv(
                    // this is because the allMessages array
                    // is indexed with message Id
                    $allMessages[$reply['id']],
                    $allMessages,
                    $permissions
                );
            }
             echo '</ul>' ;
        }
    }
    
    
    public function createRootDiv($message, $author, $permissions){
        $writerImage = $author['image'];
        $writerName  = $author['username'];

        if (empty($writerImage)) {
            $writerImage = 'unknown-avatar.jpg';
        }

        $messageId = $message['id'];
        
        echo '<div class="message root">';
            echo '<ul class="meta">'."\n";
                // delete, view
                echo '<li class="action">';
                    $this->createLinks(
                        $messageId,
                        $permissions,
                        true
                    ); 
                echo '</li>';
                     
                // image
                echo '<li class="image">';
                $this->displayMessagePosterImage($writerName, $writerImage);
                echo '</li>';
                     
                // username
                echo '<li class="author">';
                $this->displayLinkToUserProfile($writerName);
                echo '</li>';
                            
                // date
                echo '<li class="date">';
                echo $this->Date->ago($message['date']);
                echo '</li>';
            echo '</ul>';

            // message content
            echo '<div class="body" >';
            $this->displayContent($message['content']);
            echo '</div>';
        echo '</div>';
    }

    /**
     * Create the div containing a reply to a message and all the sub reply
     * the call is recursive
     *
     * @param array $message             the reply to be displayed
     * @param array $allMessages         contains all the messages 
     * @param array $messagesPermissions permisions the current user have
     *                                   on each messages
     *
     * @return void
     */
    public function createReplyDiv($message,$allMessages,$messagesPermissions)
    {
         // TODO : remove me

        $messageId = $message['Wall']['id'];
        $userName = $message['User']['username'];
        $userImage = $message['User']['image'];

        if (empty($userImage)) {
            $userImage = 'unknown-avatar.jpg';
        }
        echo '<li class="thread" id="message_' . $messageId . '">'."\n";
        ?>
            <div class="message">
                <ul class="meta" >
                    <!-- reply option -->
                    <li class="action">
                        <?php
                        $this->createLinks(
                            $messageId,
                            $messagesPermissions[$messageId]
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
                        <?php echo $this->Date->ago($message['Wall']['date']); ?>

                    </li>
                </ul>

                <!-- message content -->
                <div class="body">
                    <?php $this->displayContent($message['Wall']['content']); ?>
                </div>
            </div>
            <?php
            //replies
            echo '<div class="replies" id="messageBody_'.  $messageId .'" >';
            $this->_displayAllReplies(
                $message,
                $allMessages,
                $messagesPermissions
            );
            ?>

            </div>
        </li>
        <?php
    }
}

?>
