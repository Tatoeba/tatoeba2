<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
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
	<?php // TRANG VERSION ?>
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
             echo '<ol class="wall">';
             //pr ($allMessages);
             foreach($firstMessages as $message){
                 // TODO : remove me
                if ( empty($message['User']['image'])){
                    $message['User']['image'] = 'unknown-avatar.jpg';
                }

                echo '<li id="message_' .$message['Wall']['id'] .'"  class="topThread" >'."\n";
					echo '<div class="message root">';
	                    echo '<ul class="meta">'."\n";
							// reply option
	                        if($session->read('Auth.User.id')){
	                            $javascript->link('jquery.scrollTo-min.js',false);
	                            $javascript->link('wall.reply.js',false);
								echo '<li class="action">';
	                            echo '<a class="replyLink ' . $message["Wall"]["id"] .'" id="reply_'. $message["Wall"]["id"] .'" >' . __("reply",true). "</a>";
								echo '</li>';
	                        }
							
							// image
							echo '<li class="image">';
								echo '<img src="/img/profiles/'. $message['User']['image'].'" alt="'.__('User\'s avatar',true).'" />'."\n";
							echo '</li>';
							
							// username
							echo '<li class="author">';
								echo '<a href="/user/profile/' . $message['User']['username'] . '" ><span class="nickname" >'.
								$message["User"]["username"].'</span></a>'."\n";
							echo '</li>';
	                        
							// date
							echo '<li class="date">';
	                        echo $date->ago($message["Wall"]["date"]);
							echo '</li>';
	                    echo '</ul>';
						
						// message content
						echo '<div class="body" >'; 
						echo nl2br( $message["Wall"]["content"]);
						echo '</div>';
					echo '</div>';
					
					// replies
					echo '<div class="replies" id="messageBody_'.  $message["Wall"]["id"]  .'" >';
						foreach( $message['Reply'] as $reply ){
						   echo $wall->create_reply_div($allMessages[$reply['id'] - 1], $allMessages, $isAuthenticated);
						}
					echo '</div>';					
				echo '</li>';
             }
             echo '</ol>';

        ?>
    </div>
</div>
