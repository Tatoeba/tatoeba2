<?php
	
// navigation (previous, random, next)
$navigation->displaySentenceNavigation();
	
if($sentenceExists){

	$this->pageTitle = __('Logs for : ',true) . $sentence['Sentence']['text'];

	echo '<div class="sentences_set">';
		// sentence menu (translate, edit, comment, etc)
		$specialOptions['belongsTo'] = $sentence['User']['username']; // TODO set up a better mechanism
		$sentences->displayMenu($sentence['Sentence']['id'], $sentence['Sentence']['lang'], $specialOptions);

		// sentence and translations
		$sentence['User']['canEdit'] = $specialOptions['canEdit']; // TODO set up a better mechanism
		$sentences->displayGroup($sentence['Sentence'], $sentence['Translation'], $sentence['User']);
	echo '</div>';	


	$contributions = $sentence['Contribution'];
	
}else{
	
	$this->pageTitle = __('Logs for sentence nº',true) . $this->params['pass'][0];
	
	echo '<em>';
	__('The sentence has been deleted');
	echo '</em>';
}

echo '<h2>';
__('Logs'); 
echo ' ';
$tooltip->displayLogsColors();
echo '</h2>';

if(count($contributions) > 0){
	echo '<table id="logs">';
	foreach($contributions as $contribution){
		if($sentenceExists){
			$logs->entry($contribution, $contribution['User']);
		}else{
			$logs->entry($contribution['Contribution'], $contribution['User']);
		}
	}
	echo '</table>';
}else{
	echo '<em>'. __('There is no log for this sentence', true) .'</em>';
}
?>