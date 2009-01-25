<?php
if (isset($this->params['lang'])) { 
	Configure::write('Config.language',  $this->params['lang']); 
}
?>

<?php
$comments = $this->requestAction('/sentence_comments/latest');

echo '<table class="comments">';
foreach($comments as $comment) {
	echo '<tr>';
		echo '<td class="title">';
		echo $html->link(
			'['. $comment['Sentence']['id'] . '] ' . $comment['Sentence']['text'],
			array(
				"controller" => "sentence_comments",
				"action" => "show",
				$comment['Sentence']['id']
				));
		echo '</td>';
		
		echo '<td class="dateAndUser" rowspan="2">';
		echo $date->ago($comment['SentenceComment']['datetime']);
		echo '<br/>';
		echo $comment['User']['username'];
		echo '</td>';
	echo '</tr>';	
	
	echo '<tr>';
		echo '<td class="commentPreview">';
		echo nl2br($comment['SentenceComment']['text']);
		echo '</td>';
	echo '</tr>';
}
echo '</table>';
?>