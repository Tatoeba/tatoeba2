<?php
$this->pageTitle = __('Logs for : ',true) . $sentence['Sentence']['text'];

// navigation (previous, random, next)
$sentences->displayNavigation($sentence['Sentence']['id']);

echo '<div class="sentences_set">';
	// sentence menu (translate, edit, comment, etc)
	$sentences->displayMenu($sentence['Sentence']['id'], $sentence['Sentence']['lang'], $specialOptions);

	// sentence and translations
	$sentences->displayGroup($sentence['Sentence'], $sentence['Translation']);
echo '</div>';	


echo '<h2>'. __('Logs', true) .'</h2>';

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