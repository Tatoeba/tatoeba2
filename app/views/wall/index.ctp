<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009 Allan SIMON <allan.simon@supinfo.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
general view for the wall, here are displayed all the messages

*/



$this->pageTitle = __('Wall',true);

?>
<div id="annexe_content" >
    <div class="module" >
		<h2><?php __('Tips'); ?></h2>
        <?php __('Here ask questions about how to use Tatoeba, general questions about translation, report if you see bugs or strange stuff, or simply
        exchange with the other users.');
        echo "<br />";
        __('Have fun! Don\'t be shy to send message in whatever language you want! '); ?>
    </div>

    <div class="module" >
        <h2><?php __('Last send Messages')?></h2>
        <ul>
            <?php
                for($i = 0 ; $i < min(10,count($tenLastMessages)) ; $i++){
                    echo '<li>';
                        echo '<a href="#message_' . $tenLastMessages[$i]['Wall']['id'].'" >' .
                          $date->ago($tenLastMessages[$i]["Wall"]["date"]) .", " .  __('by ',true) . $tenLastMessages[$i]["User"]["username"]
                         .'</a>'; 
                    echo '</li>';
                }
            ?>
        </ul>
    </div>
</div>

<div id="main_content">
    <div class="module">
        <h2><?php echo __("Wall",true) ?></h2>
        <?php
            // leave a comment part
            $isAuthenticated = false ;

             if($session->read('Auth.User.id')){
                $isAuthenticated = true ;
                 echo '<div id="sendMessageForm">'."\n";
                     echo $wall->displayAddMessageToWallForm();
                 echo '</div>'."\n";
             }
            // display comment part
             echo "<div>\n" ;
             //pr ($allMessages);
             foreach($firstMessages as $message){
                 // TODO : remove me
                if ( empty($message['User']['image'])){
                    $message['User']['image'] = 'unknown-avatar.jpg';
                }

                echo '<div id="message_' .$message['Wall']['id'] .'"  class="messagePart primaryMessage" >'."\n";
                    echo '<div class="messageHeader" >'."\n";
                        echo '<img src="/img/profiles/'. $message['User']['image'].'" alt="'.__('User\'s avatar',true).'" />'."\n";
                        echo '<a href="/user/profile/' . $message['User']['username'] . '" ><span class="nickname" >'.
                        $message["User"]["username"].'</span></a>'."\n";
                        echo '<span> ,' . $message["Wall"]["date"] . ','  . __('says :', true) . '</span>'."\n" ;

                        if($session->read('Auth.User.id')){
                            $javascript->link('jquery.scrollTo-min.js',false);
                            $javascript->link('wall.reply.js',false);
                            echo '<a class="replyLink ' . $message["Wall"]["id"] .'" id="reply_'. $message["Wall"]["id"] .'" >' . __("reply",true). "</a>";
                        }
                    echo '</div>';

                    echo '<div class="messageBody" id="messageBody_'.  $message["Wall"]["id"]  .'" >';
                        echo '<div class="messageTextContent" >';
                            echo nl2br( $message["Wall"]["content"]);
                        echo '</div>';
                        //pr($message);
                        foreach( $message['Reply'] as $reply ){
                           echo $wall->create_reply_div($allMessages[$reply['id'] - 1], $allMessages, $isAuthenticated);
                        }
                    echo '</div>';

                echo '</div>';

             }
             echo '</div>';

        ?>
    </div>
</div>
