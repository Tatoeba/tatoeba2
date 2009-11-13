<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  Allan SIMON <allan.simon@supinfo.com> 

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

    if ( isset($user) ){
       // pr($user);
       echo "<div class=\"messagePart\" >\n";

            echo "<div class=\"messageHeader\" >\n"; 
                //echo "<img src=\"".$message["User"]["image"]." alt=\"Avatar of the user \" />\n";
                echo "<img src=\"/img/profiles/". $message["User"]["image"]."\" alt=\"Avatar of the user \" />\n";
                echo "<span class=\"nickname\" >". $message["User"]["username"]."</span>\n";
                echo "<span> ," . $message["Wall"]["date"] . ","  . __("says :" ,true) . "</span>\n" ;
                
                if($session->read('Auth.User.id')){
                    $javascript->link('wall.reply.js',false);
                    echo '<a class="replyLink ' . $message["Wall"]["id"] .'" id="reply_'. $message["Wall"]["id"] .'" >' . __("reply",true). "</a>"; 
                }
            echo '</div>';

            echo '<div class="messageBody" id="messageBody_'.  $message["Wall"]["id"]  .'" >';
                 echo '<div class="messageTextContent" >';
                    echo nl2br( $message["Wall"]["content"]); 
                echo '</div>';
            echo '</div>';

        echo '</div>';


    }

?>
