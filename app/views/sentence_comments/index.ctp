<h1>Comments on the sentences</h1>

<ul>
<?php
foreach($comments as $comment){
	echo '<li>';
	echo $comment['Sentence']['text'];
	echo ' / ';
	echo $comment['SentenceComment']['text'];
	echo ' / ';
	echo $comment['SentenceComment']['datetime'];
	echo ' ';
	echo $html->link(__('Show',true), 
		array(
			"controller" => "sentences",
			"action" => "show",
			$comment['SentenceComment']['sentence_id']
			));
	echo '</li>';
}
?>
</ul>