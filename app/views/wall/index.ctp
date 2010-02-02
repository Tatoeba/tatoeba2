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
        $isAuthenticated = false;
            
        if ($session->read('Auth.User.id')) {
            $isAuthenticated = true ;
            echo '<div id="sendMessageForm">'."\n";
            echo $wall->displayAddMessageToWallForm();
            echo '</div>'."\n";
        }
        // display comment part
        echo '<ol class="wall">';
        //pr ($allMessages);
        foreach ($firstMessages as $message) {    
            // TODO : remove me
            if (empty($message['User']['image'])) {
                $message['User']['image'] = 'unknown-avatar.jpg';
            }
            
            echo '<li id="message_' .$message['Wall']['id']
                .'" class="topThread" >'."\n";
            echo '<div class="message root">';
            echo '<ul class="meta">'."\n";
            // reply option
            echo '<li class="action">';
            if ($session->read('Auth.User.id')) {
                $javascript->link('jquery.scrollTo-min.js', false);
                $javascript->link('wall.reply.js', false);
                echo '<a class="replyLink ' . $message['Wall']['id'] 
                    .'" id="reply_'. $message['Wall']['id'] .'" >' 
                    . __('reply', true). '</a> - ';
            }
            echo '<a href="#message_'.$message['Wall']['id'].'">'
                .'#';
            echo '</a>';
            echo '</li>';
                 
            // image
            echo '<li class="image">';
            echo $html->link(
                $html->image(
                    'profiles/'.$message['User']['image'],
                    array(
                        'title' => __('View this user\'s profile', true)
                    )
                ),
                array('controller' => 'user', 'action' => 'profile',
                    $message['User']['username']),
                array('escape' => false)
            );
            echo '</li>';
                 
            // username
            echo '<li class="author">';
            echo $html->link(
                $message['User']['username'],
                array(
                    'controller' => 'user',
                    'action' => 'profile',
                    $message['User']['username']
                ),
                array("title" => __('View this user\'s profile', true))
            );
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
            echo '<div class="replies" id="messageBody_'
                . $message['Wall']['id']  .'" >';
            if (count($message['Reply']) >0) {
                echo '<ul>';
                foreach ($message['Reply'] as $reply ) {
                    echo $wall->createReplyDiv(
                        $allMessages[$reply['id'] - 1],
                        $allMessages, $isAuthenticated
                    );
                }
                echo '</ul>';
            }
            echo '</div>';
            echo '</li>';
        }
        echo '</ol>';
        ?>
    </div>
</div>
