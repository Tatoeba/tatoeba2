<?php 
$this->pageTitle = __('Is it correct : ',true) . $sentence['Sentence']['text'];

// navigation (previous, random, next)
$navigation->displaySentenceNavigation($sentence['Sentence']['id']);

echo '<div class="sentences_set">';
	// sentence menu (translate, edit, comment, etc)
	$sentences->displayMenu($sentence['Sentence']['id'], $specialOptions);

	// sentence and translations
	$sentences->displayForEdit($sentence['Sentence'], $sentence['Translation']);
echo '</div>';

echo '<div class="addComment">';
echo $html->link(
	__('Add a comment',true),
	array("controller" => "sentence_comments", "action" => "add", $sentence['Sentence']['id'])
);
echo '</div>';

echo '<h2>';
__('Comments');
echo '</h2>';


if(count($sentence['SentenceComment']) > 0){
	echo '<div class="comments">';
	foreach($sentence['SentenceComment'] as $comment){
		$comments->displayComment($comment['User']['id'], $comment['User']['username'], $comment['datetime'], $comment['text']);
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