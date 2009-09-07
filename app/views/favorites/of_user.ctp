<?php
$navigation->displayUsersNavigation($user['User']['id'], $user['User']['username']);

echo '<h3>';
__('Favorite sentences');
echo '</h3>';
if(count($user['Favorite']) > 0){
	foreach($user['Favorite'] as $favorite){
		$sentences->displaySentence($favorite);
	}
}else{
	__('This user does not have any favorites.');
}
?>