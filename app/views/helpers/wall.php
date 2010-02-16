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

    public $helpers = array('Html', 'Form' , 'Date');

    /**
     * create the reply link to a given message 
     *
     * @param int  $messageId       the id of the replied message
     * @param bool $isAuthenticated to know if the request come from a
     *                              connected user
     *
     * @return void
     */

    private function _createLinks($messageId, $isAuthenticated)
    {
        if ($isAuthenticated) {
            
            /* * * * * * * * * * * * * * * * *\
             *    _____ ___  ____   ___      *
             *   |_   _/ _ \|  _ \ / _ \     *
             *     | || | | | | | | | | |    *
             *     | || |_| | |_| | |_| |    *
             *     |_| \___/|____/ \___/     *
             *                               *
            \* * * * * * * * * * * * * * * * */
            $canDelete = true;
            if($canDelete){
                // delete link
                echo $this->Html->link(
                    __('delete',true),
                    array(
                        "controller"=>"wall", 
                        "action"=>"delete",
                        $messageId
                    ),
                    null,
                    __('Are you sure?', true)
                );
                echo ' - ';
            }
            /* * * * * * * * * * * * * * * * */
            
            
            // reply link
            $replyLinkId = 'reply_' . $messageId;
            $replyClasses = 'replyLink ' . $messageId;
            echo '<a class="'.$replyClasses.'" id="'.$replyLinkId.'" >';
             __("reply");
            echo '</a>';
            echo ' - ';
        }
        
        // message link
        echo '<a href="#message_'.$messageId.'">#</a>';
    }

    /**
     * display the avatar of the one who write current message 
     *
     * @param string $userName  name of the user
     * @param string $userImage filename of user's picture
     *
     * @return void
     */
    
    private function _displayMessagePosterImage($userName, $userImage)
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
     * display username as a link to his profile
     *
     * @param string $userName nickname of the user
     *
     * @return void
     */

    private function _displayLinkToUserProfile($userName)
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
     * @param array $message         the reply to be displayed
     * @param array $allMessages     contains all the messages 
     * @param bool  $isAuthenticated to know if the request come from a
     *                               connected user
     *
     * @return void
     */
    private function _displayAllReplies($message,$allMessages,$isAuthenticated)
    {
        if (!empty($message['Reply'])) {
            echo '<ul class="toto" >';
            foreach ($message['Reply'] as $reply) {
                $this->createReplyDiv(
                    $allMessages[$reply['id'] - 1],
                    $allMessages,
                    $isAuthenticated
                );
            }
             echo '</ul>' ;
        }
    }

    /**
     * Create the div containing a reply to a message and all the sub reply
     * the call is recursive
     *
     * @param array $message         the reply to be displayed
     * @param array $allMessages     contains all the messages 
     * @param bool  $isAuthenticated to know if the request come from a
     *                               connected user
     *
     * @return void
     */
    public function createReplyDiv($message,$allMessages,$isAuthenticated)
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
                        $this->_createLinks(
                            $messageId,
                            $isAuthenticated
                        );
                        ?>

                    </li>
                    <li class="image">
                        <?php
                        $this->_displayMessagePosterImage(
                            $userName,
                            $userImage
                        )
                        ?>

                    </li>
                    <li class="author">
                        <?php $this-> _displayLinkToUserProfile($userName); ?>

                    </li>
                    <li class="date">
                        <?php echo $this->Date->ago($message['Wall']['date']); ?>

                    </li>
                </ul>

                <!-- message content -->
                <div class="body">
                    <?php
                    echo nl2br(
                        htmlentities(
                            $message['Wall']['content'],
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    );
                    ?>

                </div>
            </div>
            <?php
            //replies
            echo '<div class="replies" id="messageBody_'.  $messageId .'" >';
            $this->_displayAllReplies(
                $message,
                $allMessages,
                $isAuthenticated
            );
            ?>

            </div>
        </li>
        <?php
    }
}

?>
