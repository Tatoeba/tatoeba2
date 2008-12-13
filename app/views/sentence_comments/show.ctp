<?php
$this->pageTitle = __('Comments about : ',true) . $sentence['Sentence']['text'];

// navigation (previous, random, next)
$sentences->displayNavigation($sentence['Sentence']['id']);

echo '<div class="sentences_set">';
	// sentence menu (translate, edit, comment, etc)
	$sentences->displayMenu($sentence['Sentence']['id'], $sentence['Sentence']['lang'], $sentence['Sentence']['correctness'], $specialOptions);

	// sentence and translations
	$sentences->displayGroup($sentence['Sentence'], $sentence['Translation']);
echo '</div>';	

if(count($sentence['SentenceComment']) > 0){
	foreach($sentence['SentenceComment'] as $comment){
		$comments->displayComment($comment['User']['username'], $comment['datetime'], $comment['text']);
	}
}else{
	echo '<em>There are no comments for now</em>';
}
?>