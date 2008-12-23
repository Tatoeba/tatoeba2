<?php
if($sentence != null){
	$this->pageTitle = __('Example sentence : ',true) . $sentence['Sentence']['text'];

	// navigation (previous, random, next)
	$sentences->displayNavigation($sentence['Sentence']['id']);

	echo '<div class="sentences_set">';
		// sentence menu (translate, edit, comment, etc)
		$sentences->displayMenu($sentence['Sentence']['id'], $specialOptions);

		// sentence and translations
		$sentences->displayGroup($sentence['Sentence'], $sentence['Translation']);
	echo '</div>';

	
	// LOGS
	echo '<h2>'. __('Latest log',true) .'</h2>';
	if(count($sentence['Contribution']) > 0){
		echo '<table id="logs">';
		$logs->entry($sentence['Contribution'][0], $sentence['Contribution'][0]['User']);
		echo '</table>';
	}else{
		__('There are no logs for this sentence.');
	}

	
	// LATEST COMMENT
	echo '<h2>'. __('Latest comment',true) .'</h2>';
	if(count($sentence['SentenceComment']) > 0){
		pr($sentence['SentenceComment'][0]);
	}else{
		__('There are no comments for this sentence.');
	}
}else{
	$this->pageTitle = __('Sentence does not exist : ', true) . $this->params['pass'][0];
	
	// navigation (previous, random, next)
	$sentences->displayNavigation('random');
	
	echo '<div class="error">';
	__('There is no sentence with id ');
	echo $this->params['pass'][0];
	echo '</div>';
}
?>