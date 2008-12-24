<?php
$this->pageTitle = __('Add comment for sentence',true);

// navigation (previous, random, next)
$sentences->displayNavigation($sentence['Sentence']['id']);

echo '<div class="sentences_set">';
	// sentence menu (translate, edit, comment, etc)
	$sentences->displayMenu($sentence['Sentence']['id'], $specialOptions);

	// sentence and translations
	$sentences->displayGroup($sentence['Sentence'], $sentence['Translation']);
echo '</div>';	

if(count($sentence['SentenceComment']) > 0){
	echo '<div class="comments">';
	foreach($sentence['SentenceComment'] as $comment){
		$comments->displayComment($comment['User']['username'], $comment['datetime'], $comment['text']);
	}
	echo '</div>';
}

$comments->displayCommentForm($sentence['Sentence']['id']);
?>