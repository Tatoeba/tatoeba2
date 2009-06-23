<?php
$this->pageTitle = __('Logs for : ',true) . $sentence['Sentence']['text'];

// navigation (previous, random, next)
$navigation->displaySentenceNavigation($sentence['Sentence']['id']);

echo '<div class="sentences_set">';
	// sentence menu (translate, edit, comment, etc)
	$specialOptions['belongsTo'] = $sentence['User']['username']; // TODO set up a better mechanism
	$sentences->displayMenu($sentence['Sentence']['id'], $sentence['Sentence']['lang'], $specialOptions);

	// sentence and translations
	$sentence['User']['canEdit'] = $specialOptions['canEdit']; // TODO set up a better mechanism
	$sentences->displayGroup($sentence['Sentence'], $sentence['Translation'], $sentence['User']);
echo '</div>';	


echo '<h2>';
__('Logs'); 
echo ' ';
$tooltip->displayLogsColors();
echo '</h2>';

if(count($sentence['Contribution']) > 0){
	echo '<table id="logs">';
	foreach($sentence['Contribution'] as $contribution){
		$logs->entry($contribution, $contribution['User']);
	}
	echo '</table>';
}else{
	echo '<em>'. __('There is no log for this sentence', true) .'</em>';
}
?>