<?php
$comments = $this->requestAction('/sentence_comments/latest');

echo '<table>';
foreach($comments as $comment) {
	echo '<tr>';
		echo '<td>';
		echo $comment['User']['username'];
		echo '</td>';
		
		echo '<td>';
		echo $date->ago($comment['SentenceComment']['datetime']);
		echo '</td>';
		
		echo '<td>';
		echo $comment['SentenceComment']['text'];
		echo '</td>';
	echo '</tr>';
}
echo '</table>';
?>