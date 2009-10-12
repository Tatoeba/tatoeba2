<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  TATOEBA Project(should be changed)

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
$this->pageTitle = __('Tatoeba user : ',true) . $user['User']['username'];
//$javascript->link('users.followers_and_following.js', false);

$navigation->displayUsersNavigation($user['User']['id'], $user['User']['username']);

echo '<div id="usertop" >' ;
	echo '<h2>';
	echo $user['User']['username'];
	// if($session->read('Auth.User.id') AND isset($can_follow)){
		// if($can_follow){
			// $style2 = "style='display: none'";
			// $style1 = "";
		// }else{
			// $style1 = "style='display: none'";
			// $style2 = "";
		// }
		// echo ' (';
		// echo '<a id="start" class="followingOption" '.$style1.'>'. __('Start following this person', true). '</a>';
		// echo '<a id="stop" class="followingOption" '.$style2.'>'. __('Stop following this person', true). '</a>';
		// echo ')';
	// }
	echo '</h2>';

	/* User general information */
	echo '<div class="user" id="'.$user['User']['id'].'">';
	__('Member since : ');
	echo $date->ago($user['User']['since']);
	echo '</div>';


	/*User's menu to acced more specific and detailled informations*/
	// echo '<div id="usermenu" >';
		// echo '<ul>';
			// echo '<li>' . __('Followers',true) . '</li>';  
			// echo '<li>' . __('Contributions',true) . '</li>';  
			// echo '<li>' . __('Favorite sentences',true) . '</li>';  
			// echo '<li>' . __('Sentences',true) . '</li>';  
			// echo '<li>' . __('Comments',true) . '</li>';  
		
		// echo '</ul>';
	// echo '</div>';
echo '</div>';

/* People that the user is following */

if(count($user['Following']) > 0){
	echo '<h3>';
	__('Following');
	echo '</h3>';
	
	echo '<div class="following">';	
	echo '<ul>';
	foreach($user['Following'] as $following){
		echo '<li>'.$following['username'].'</li>';
	}
	echo '<ul>';
	echo '</div>';
}


/* People that are following the user */
if(count($user['Follower']) > 0){
	echo '<h3>';
	__('Followers');
	echo '</h3>';
	
	echo '<div class="followers">';	
	echo '<ul>';
	foreach($user['Follower'] as $follower){
		echo '<li>'.$follower['username'].'</li>';
	}
	echo '<ul>';
	echo '</div>';
}


/* Latest contributions from the user */
if(count($user['Contributions']) > 0){
	echo '<h3>';
	__('Contributions');
	echo '</h3>';

	echo '<table id="logs">';
	foreach($user['Contributions'] as $contribution){
		$logs->entry($contribution);
	}
	echo '</table>';
	
	echo '<br/>';
}

/* Latest favorites from the user */
if(count($user['Favorite']) > 0){
	echo '<h3>';
	__('Favorite sentences');
	echo ' (';
	echo $html->link(
		__('view all', true),
		array(
			"controller" => "favorites",
			"action" => "of_user",
			$user['User']['id']
		)
	);
	echo ')';
	echo '</h3>';

	echo '<table id="logs">';
	foreach($user['Favorite'] as $favorite){
		$sentences->displaySentence($favorite);
	}
	echo '</table>';
	
	echo '<br/>';
}

/* Latest sentences, translations or adoptions from the user */
if(count($user['Sentences']) > 0){
	echo '<h3>';
	__('Sentences');
	echo '</h3>';

	foreach($user['Sentences'] as $sentence){
		$sentences->displaySentence($sentence);
	}
}

/* Latest comments from the user */
if(count($user['SentenceComments']) > 0){
	echo '<h3>';
	__('Comments');
	echo '</h3>';

	echo '<table class="comments">';
	foreach($user['SentenceComments'] as $comment) {
		echo '<tr>';
			echo '<td class="title">';
			echo $html->link(
				'['. $comment['sentence_id'] . '] ',
				array(
					"controller" => "sentence_comments",
					"action" => "show",
					$comment['sentence_id']
					));
			echo '</td>';
			
			echo '<td class="dateAndUser" rowspan="2">';
			echo $date->ago($comment['created']);
			echo '<br/>';
			echo $user['User']['username'];
			echo '</td>';
		echo '</tr>';	
		
		echo '<tr>';
			echo '<td class="commentPreview">';
			echo nl2br($comments->clickableURL($comment['text']));
			echo '</td>';
		echo '</tr>';
	}
	echo '</table>';
}
?>
