<?php
$this->pageTitle = __('Tatoeba user : ',true) . $user['User']['username'];
$javascript->link('users.followers_and_following.js', false);

$navigation->displayUsersNavigation($user['User']['id'], $user['User']['username']);

echo '<h2>';
echo $user['User']['username'];
echo ' ';
if($can_follow){
	echo '(<a class="startFollowing">'. __('Start following this person', true). '</a>)';
}else{
	echo '(<a class="stopFollowing">'. __('Stop following this person', true). '</a>)';
}
echo '</h2>';

/* User general information */
echo '<div class="user" id="'.$user['User']['id'].'">';
__('Member since : ');
echo $date->ago($user['User']['since']);
echo '</div>';


/* People that the user is following */
echo '<div class="following">';
if(count($user['Following']) > 0){
	echo '<h3>';
	__('Following');
	echo '</h3>';
	
	echo '<ul>';
	foreach($user['Following'] as $following){
		echo '<li>'.$following['username'].'</li>';
	}
	echo '<ul>';
}
echo '</div>';


/* People that are following the user */
echo '<div class="followers">';
if(count($user['Follower']) > 0){
	echo '<h3>';
	__('Followers');
	echo '</h3>';
	
	echo '<ul>';
	foreach($user['Follower'] as $follower){
		echo '<li>'.$follower['username'].'</li>';
	}
	echo '<ul>';
}
echo '</div>';


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