<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009 Allan SIMON <allan.simon@supinfo.com>
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

/*
general view for the wall, here are displayed all the messages

*/

$this->pageTitle = __('Wall', true);

?>
<div id="annexe_content" >
    <div class="module" >
        <h2><?php __('Tips'); ?></h2>
        <p>
            <?php
            __(
                'Here you can ask general questions like how to use Tatoeba,' .
                'report bugs or strange behaviors, or simply socialize with the'.
                ' rest of the community.'
            );
            ?>
         </p>

        <p><?php __("Have fun! Don't be shy!"); ?></p>
    </div>

    <div class="module" >
        <h2><?php __('Latest messages'); ?></h2>
        <ul>
            <?php
            $mesg = count($tenLastMessages);
            for ($i = 0 ; $i < min(10, $mesg); $i++) {
                $currentMessage = $tenLastMessages[$i] ;
                echo '<li>';
                echo '<a href="#message_' . $currentMessage['Wall']['id'] .
                    '" >' . $date->ago($currentMessage['Wall']['date'])
                     . ", "
                     . __('by ', true)
                     . $currentMessage['User']['username'];
                echo '</a>';
                echo '</li>';
            };
            ?>
        </ul>
    </div>
</div>

<div id="main_content">
    <div class="module">
        <h2><?php echo __("Wall", true); ?></h2>
        <?php
        // leave a comment part
            
        if ($isAuthenticated) {
            echo '<div id="sendMessageForm">'."\n";
            echo $wall->displayAddMessageToWallForm();
            echo '</div>'."\n";
        }
        ?>
        <ol class="wall">
        <?php
        // display comment part
        foreach ($firstMessages as $message) {
            
            $writerImage = $message['User']['image'];
            $writerName  = $message['User']['username'];

            if (empty($writerImage)) {
                $writerImage = 'unknown-avatar.jpg';
            }

            $messageId = $message['Wall']['id'];
            
            echo '<li id="message_'.$messageId.'" class="topThread" >'."\n";
            echo '<div class="message root">';
            echo '<ul class="meta">'."\n";
            
            echo '<li class="action">';
                $wall->createLinks(
                    $messageId,
                    $messagesPermissions[$messageId],
                    true
                ); 
            echo '</li>';
                 
            // image

            echo '<li class="image">';
            $wall->displayMessagePosterImage($writerName, $writerImage);
            echo '</li>';
                 
            // username
            echo '<li class="author">';
            $wall->displayLinkToUserProfile($writerName);
            echo '</li>';
                        
            // date
            echo '<li class="date">';
            echo $date->ago($message['Wall']['date']);
            echo '</li>';
            echo '</ul>';

            // message content
            echo '<div class="body" >';
            echo nl2br(
                htmlentities($message['Wall']['content'], ENT_QUOTES, 'UTF-8')
            );
            echo '</div>';
            echo '</div>';

            // replies
            echo '<div class="replies" id="messageBody_'.$messageId .'" >';
            if (count($message['Reply']) >0) {
                echo '<ul>';
                foreach ($message['Reply'] as $reply ) {
                    echo $wall->createReplyDiv(
                        // this is because the allMessages array
                        // is indexed with message Id
                        $allMessages[$reply['id']],
                        $allMessages,
                        $messagesPermissions
                    );
                }
                echo '</ul>';
            }
            echo '</div>';
            echo '</li>';
        }
        ?>
        </ol>
    </div>
</div>
