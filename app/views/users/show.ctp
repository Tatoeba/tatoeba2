<?php
$navigation->displayUsersNavigation($user['User']['id'], $user['User']['username']);

$this->pageTitle = __('Tatoeba user : ',true) . $user['User']['username'];

echo '<h2>'.$user['User']['username'].'</h2>';

__('Member since : ');
echo $date->ago($user['User']['since']);



// echo '<h3>';
// __('Statistics');
// echo '</h3>';

// if(count($user['UserStatistics']) > 0){
// }else{
// __('This doesn\'t have any statistics yet.');
// }


echo '<br/>';
echo '<br/>';


echo '<h3>';
__('Contributions');
echo '</h3>';

if(count($user['Contributions']) > 0){
	echo '<table id="logs">';
	foreach($user['Contributions'] as $contribution){
		$logs->entry($contribution);
	}
	echo '</table>';
}else{
__('This user didn\'t contribute yet.');
}


echo '<br/>';


echo '<h3>';
__('Sentences');
echo '</h3>';

if(count($user['Sentences']) > 0){
	foreach($user['Sentences'] as $sentence){
		$sentences->displaySentence($sentence);
	}
}else{
__('This user doesn\'t own any sentence.');
}


echo '<br/>';


echo '<h3>';
__('Comments');
echo '</h3>';

if(count($user['SentenceComments']) > 0){
}else{
__('This user didn\'t post any comment.');
}
?>