<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  TATOEBA Project(should be changed)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
if (isset($this->params['lang'])) {
	Configure::write('Config.language',  $this->params['lang']);
}
?>

<?php
$sentenceComments = $this->requestAction('/sentence_comments/latest');

echo '<table class="comments">';
foreach($sentenceComments as $comment) {
	echo '<tr>';
		echo '<td class="title">';
		$sentence = (isset($comment['Sentence']['text'])) ? $comment['Sentence']['text'] : '';

		echo $html->link(
			'['. $comment['SentenceComment']['sentence_id'] . '] ' . $sentence,
			array(
				"controller" => "sentence_comments",
				"action" => "show",
				$comment['SentenceComment']['sentence_id']
				)
			);
		if(!isset($comment['Sentence']['text'])){
			echo '<em>' . __('sentence deleted', true) . '</em>';
		}
		echo '</td>';

		echo '<td class="dateAndUser" rowspan="2">';
		echo $date->ago($comment['SentenceComment']['created']);
		echo '<br/>';
		echo $html->link(
			$comment['User']['username'],
			array("controller" => "user", "action" => "profile", $comment['User']['username'])
		);
		echo '</td>';
	echo '</tr>';

	echo '<tr>';
		echo '<td class="commentPreview">';
		echo nl2br($comments->clickableURL($comment['SentenceComment']['text']));
		echo '</td>';
	echo '</tr>';
}
echo '</table>';
?>
