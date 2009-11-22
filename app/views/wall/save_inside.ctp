<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  Allan SIMON <allan.simon@supinfo.com>,
	HO Ngoc Phuong Trang <tranglich@gmail.com>

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
	
	// TODO use a helper method to display this. It is actually pretty much
	// a copy-paste of the create_reply_div() method in the WallHelper.
	
	echo '<li class="new thread" id="message_' . $message["Wall"]["id"] . '">'."\n";
		echo '<div class="message">';
			echo "<ul class=\"meta\" >\n"; 
				// image
				echo '<li class="image">';
				echo $html->link(
					$html->image(
						'profiles/'. $message["User"]["image"], 
						array(
							"alt"=>$message["User"]["username"],
							"title"=>__("View this user's profile",true)
						)
					),
					array("controller"=>"user", "action"=>"profile", $message["User"]["username"]),
					array("escape"=>false)
				);
				echo '</li>';
				
				// username
				echo '<li class="author">';
				echo $html->link(
					$message["User"]["username"],
					array("controller"=>"private_messages", "action"=>"write", $message["User"]["username"])
				);
				echo '</li>';
				
				// date
				echo '<li class="date">';
				echo $date->ago($message["Wall"]["date"]);
				echo '</li>';
			echo '</ul>';
		
			// message content
			echo '<div class="body">';
				echo nl2br( $message["Wall"]["content"]);
			echo '</div>';				
		echo '</div>';

	echo '</li>';

}

?>
