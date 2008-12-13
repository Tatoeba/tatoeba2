<?php
$this->pageTitle = __('Please translate : ',true) . $sentence['Sentence']['text'];

// navigation (previous, random, next)
$sentences->displayNavigation($sentence['Sentence']['id']);

echo '<div class="sentences_set">';
	// sentence menu (translate, edit, comment, etc)
	$sentences->displayMenu($sentence['Sentence']['id'], $sentence['Sentence']['lang'], $sentence['Sentence']['correctness'], $specialOptions);

	// sentence and translations
	$sentences->displayForTranslation($sentence['Sentence'], $sentence['Translation']);
echo '</div>';
?>