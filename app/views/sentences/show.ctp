<?php
if($sentence != null){
	$javascript->link('sentences.show_translations.js', false);
	$this->pageTitle = __('Example sentence : ',true) . $sentence['Sentence']['text'];

	// navigation (previous, random, next)
	$navigation->displaySentenceNavigation($sentence['Sentence']['id']);
	
	echo '<div class="sentences_set">';
		// sentence menu (translate, edit, comment, etc)
		$sentences->displayMenu($sentence['Sentence']['id'], $specialOptions);

		// sentence and translations
		$t = (isset($sentence['Translation'])) ? $sentence['Translation'] : array();
		$sentences->displayGroup($sentence['Sentence'], $t);
	echo '</div>';
	
	//$tooltip->displayAdoptTooltip();
	
	echo '<a href="#" id="test">Afficher machin</a>';
	echo '<div id="test_result"></div>';
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