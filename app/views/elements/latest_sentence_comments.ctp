<?php
$comments = $this->requestAction('/sentence_comments/latest');
echo '<ul>';
foreach($comments as $comment) {
	echo '<li>';
	echo $comment['SentenceComment']['text'];
	echo '</li>';
}
echo '</ul>';
?>