<?php
$this->pageTitle = __('Potential translations for : ',true) . $sentence['Sentence']['text'];

// navigation (previous, random, next)
$navigation->displaySentenceNavigation($sentence['Sentence']['id']);

echo '<div class="sentences_set">';
	// sentence menu (translate, edit, comment, etc)
	$sentences->displayMenu($sentence['Sentence']['id'], $sentence['Sentence']['lang'], $specialOptions);

	// sentence and translations
	//$sentences->displayForLink($sentence['Sentence'], $sentence['Translation']);
echo '</div>';
?>