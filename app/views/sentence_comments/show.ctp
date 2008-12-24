<?php
$this->pageTitle = __('Comments the sentence : ',true) . $sentence['Sentence']['text'];

// navigation (previous, random, next)
$sentences->displayNavigation($sentence['Sentence']['id']);

echo '<div class="sentences_set">';
	$sentences->displayMenu($sentence['Sentence']['id'], $specialOptions);
	$sentences->displayGroup($sentence['Sentence'], $sentence['Translation']);
echo '</div>';


if(count($sentence['SentenceComment']) > 0){
	echo '<div class="comments">';
	foreach($sentence['SentenceComment'] as $comment){
		$comments->displayComment($comment['User']['username'], $comment['datetime'], $comment['text']);
	}
	echo '</div>';
}else{
	echo '<em>' . __('There are no comments for now.', true) .'</em>';
	
	if($specialOptions['canComment'] == false){
		echo '<br/>';
		echo '<em>'. __('You can add a comment if you are registered and logged in.', true) .'</em>';
	}
}
?>