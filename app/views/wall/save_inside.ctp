<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  Allan SIMON <allan.simon@supinfo.com>,
 * HO Ngoc Phuong Trang <tranglich@gmail.com>
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


if (isset($user)) {

    // TODO the code can still be refactor a bit with wall helper
    
    // NOTE I took out the "reply" option here because :
    // 1) User is not supposed to want to reply to himself right after
    //    posting his reply... Instead he should post another reply
    //    to the message he's replied to.
    // 2) We should actually try to limit the the "deepness" of a thread.
    //    Three levels of reply should be the maximum, I think.
    $messageId = $message["Wall"]["id"];
    $userName = $message['User']['username'];
    $userImage = $message['User']['image'];
    $messageContent =  $message['Wall']['content'];

    echo '<li class="new thread" id="message_' . $messageId . '">'."\n";

    '<div class="message">';
    echo "<ul class=\"meta\" >\n"; 
    // image
    echo '<li class="image">';
    $wall->displayMessagePosterImage(
        $userName,
        $userImage
    );
    echo '</li>';
                
    // username
    echo '<li class="author">';
    $wall->displayLinkToUserProfile($userName);
    echo '</li>';
                
    // date
    echo '<li class="date">';
    echo $date->ago($message["Wall"]["date"]);
    echo '</li>';
    echo '</ul>';
    
    // message content
    echo '<div class="body">';
    $wall->displayContent($messageContent);
    echo '</div>';                
    echo '</div>';
    
    echo '</li>';

}

?>
