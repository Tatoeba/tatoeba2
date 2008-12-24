<?php
$this->pageTitle = __('Correction for : ',true) . $sentence['Sentence']['text'];

// navigation (previous, random, next)
$sentences->displayNavigation($sentence['Sentence']['id']);

echo '<div class="sentences_set">';
	// sentence menu (translate, edit, comment, etc)
	$sentences->displayMenu($sentence['Sentence']['id'], $specialOptions);

	// sentence and translations
	$sentences->displayForCorrection($sentence['Sentence'], $sentence['Translation']);
echo '</div>';

echo '<h2>';
__('Comments');
echo ' (';
echo $html->link(
	__('Add a comment',true),
	array("controller" => "sentence_comments", "action" => "add", $sentence['Sentence']['id'])
);
echo ')';
echo '</h2>';


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