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
helper used to display a form to add a message to a wall
*/
class WallHelper extends AppHelper {

    var $helpers = array('Html', 'Form' , 'Date');

    function displayAddMessageToWallForm(){
        /* we use models=>wall to force use wall, instead cakephp would have
           called "walls/save' which is not what we want 
        */
        __('Add a Message : ');
        echo $this->Form->create('' , array("models" => "wall" , "action" => "save")) ;
        //echo $this->Form->text('',array('class'=>'messageTitle'));
        echo $this->Form->input('content',array('label'=>""));
        echo $this->Form->hidden('replyTo',array('value'=>"" ));
        echo "<div>";
            echo $this->Form->end(__('Send',true));
        echo "</div>";
    }
function create_reply_div($message,$allMessages,$isAuthenticated){
                echo "<div class=\"messagePart\" >\n";
                    echo "<div class=\"replyHeader\" >\n"; 
                        echo "<img src=\"/img/profiles/". $message["User"]["image"]."\" alt=\"Avatar of the user \" />\n";
                        echo "<span class=\"nickname\" >". $message["User"]["username"]."</span>\n";
                        echo "<span> ," . $message["Wall"]["date"] . ","  . __("says :" ,true) . "</span>\n" ;
                        
                        if($isAuthenticated){
                            echo '<a class="replyLink ' . $message["Wall"]["id"] .'" id="reply_'. $message["Wall"]["id"] .'" >' . __("reply",true). "</a>"; 
                        }
                    echo '</div>';

                    echo '<div class="messageBody" id="messageBody_'.  $message["Wall"]["id"]  .'" >';
                        echo '<div class="messageTextContent" >';
                            echo nl2br( $message["Wall"]["content"]);
                        echo '</div>';

                        //pr($message);
                        foreach( $message['Reply'] as $reply ){
                            $this->create_reply_div($allMessages[$reply['id'] - 1],$allMessages,$isAuthenticated);
                        } 
                    echo '</div>';

                echo '</div>';
}
}

?>
