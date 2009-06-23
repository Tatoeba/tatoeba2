<?php
$this->pageTitle = __('Add comment for sentence',true);

// navigation (previous, random, next)
$navigation->displaySentenceNavigation($sentence['Sentence']['id']);

echo '<div class="sentences_set">';
	// sentence menu (translate, edit, comment, etc)
	$specialOptions['belongsTo'] = $sentence['User']['username']; // TODO set up a better mechanism
	$sentences->displayMenu($sentence['Sentence']['id'], $sentence['Sentence']['lang'], $specialOptions);

	// sentence and translations
	$sentences->displayGroup($sentence['Sentence'], $sentence['Translation']);
echo '</div>';	

$comments->displayCommentForm($sentence['Sentence']['id'], $sentence['Sentence']['text']);

if(count($sentence['SentenceComment']) > 0){
	echo '<div class="comments">';
	foreach($sentence['SentenceComment'] as $comment){
		$comments->displayComment($comment['User']['id'], $comment['User']['username'], $comment['created'], $comment['text']);
	}
	echo '</div>';
}
?>