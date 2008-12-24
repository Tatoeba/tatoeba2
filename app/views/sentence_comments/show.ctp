<?php
$this->pageTitle = __('Comments the sentence : ',true) . $sentence['Sentence']['text'];

// navigation (previous, random, next)
$sentences->displayNavigation($sentence['Sentence']['id']);

echo '<div class="sentences_set">';
	$sentences->displayMenu($sentence['Sentence']['id'], $specialOptions);
	$sentences->displayGroup($sentence['Sentence'], $sentence['Translation']);
echo '</div>';

echo '<div class="addComment">';
echo $html->link(
	__('Add a comment',true),
	array("controller" => "sentence_comments", "action" => "add", $sentence['Sentence']['id'])
);
echo '</div>';

echo '<div class="comments">';
if(count($sentence['SentenceComment']) > 0){
	foreach($sentence['SentenceComment'] as $comment){
		$comments->displayComment($comment['User']['username'], $comment['datetime'], $comment['text']);
	}
}else{
	echo '<em>' . __('There are no comments for now.', true) .'</em>';	
}
echo '</div>';
?>