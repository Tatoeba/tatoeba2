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
helper used to display a form to add a message to a wall
*/
class WallHelper extends AppHelper {

    var $helpers = array('Html', 'Form' , 'Date');

    function displayAddMessageToWallForm(){
        /* we use models=>wall to force use wall, instead cakephp would have
           called "walls/save' which is not what we want
        */
        __('Add a Message : ');
        echo $this->Form->create('' , array( "action" => "save")) ;
        echo "<fieldset>";
            echo $this->Form->input('content',array('label'=>""));
            echo $this->Form->hidden('replyTo',array('value'=>"" ));
        echo "</fieldset>";
		echo $this->Form->submit(__('Send',true));
        echo '<div class="divCancelFormLink" >';
		    echo '<a class="cancelFormLink" >' . __("cancel",true) . '</a>';
        echo '</div>';
        echo $this->Form->end();

    }

	function create_reply_div($message,$allMessages,$isAuthenticated){
		 // TODO : remove me
		if ( empty($message['User']['image'])){
			$message['User']['image'] = 'unknown-avatar.jpg';
		}
		echo '<li class="thread" id="message_' . $message['Wall']['id'] . '">'."\n";
			echo '<div class="message">';
				echo "<ul class=\"meta\" >\n";
					// reply option
					echo '<li class="action">';
					if($isAuthenticated){
						echo '<a class="replyLink ' . $message['Wall']['id'] .'" id="reply_'. $message['Wall']['id'] .'" >' . __("reply",true). '</a> - ';
					}
					echo '<a href="#message_'.$message['Wall']['id'].'">#</a>';
					echo '</li>';

					// image
					echo '<li class="image">';
					echo $this->Html->link(
						$this->Html->image(
							'profiles/'. $message['User']['image']
							, array(
								"alt"=>$message['User']['username']
								, "title"=>__("View this user's profile",true)
							)
						)
						, array("controller"=>"user", "action"=>"profile", $message['User']['username'])
						, array("escape"=>false)
					);
					echo '</li>';

					// username
					echo '<li class="author">';
					echo $this->Html->link(
						$message['User']['username']
						, array("controller"=>"user", "action"=>"profile", $message['User']['username'])
						, array("title"=>__("View this user's profile",true))
					);
					echo '</li>';

					// date
					echo '<li class="date">';
					echo $this->Date->ago($message['Wall']['date']);
					echo '</li>';
				echo '</ul>';

				// message content
				echo '<div class="body">';
					echo nl2br( htmlentities( $message['Wall']['content'], ENT_QUOTES, 'UTF-8'));
				echo '</div>';
			echo '</div>';

			// replies
			echo '<div class="replies" id="messageBody_'.  $message['Wall']['id']  .'" >';

                if ( ! empty($message['Reply'] )){
                echo '<ul class="toto" >';
				    foreach( $message['Reply'] as $reply ){
					    $this->create_reply_div($allMessages[$reply['id'] - 1],$allMessages,$isAuthenticated);
				    }
                echo '</ul>' ;
                }
			echo '</div>';

		echo '</li>';
	}
}

?>
