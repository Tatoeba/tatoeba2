<?php
$this->pageTitle = __('Comments on the sentence : ',true) . $sentence['Sentence']['text'];

// navigation (previous, random, next)
$navigation->displaySentenceNavigation($sentence['Sentence']['id']);

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

echo '<h2>';
__('Comments');
echo ' ';
$tooltip->display(__('If you see any mistake, don\'t hesitate to post a comment about it!',true));
echo '</h2>';

echo '<a name="comments"></a>';
echo '<div class="comments">';
if(count($sentence['SentenceComment']) > 0){
	foreach($sentence['SentenceComment'] as $comment){
		$comments->displayComment(
			$comment['User']['id'],
			$comment['User']['username'], 
			$comment['created'], 
			$comment['text']
		);
	}
}else{
	echo '<em>' . __('There are no comments for now.', true) .'</em>';	
}
echo '</div>';
?>