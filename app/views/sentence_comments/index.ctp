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

?>

<div id="second_modules">
	<div class="module">
		<h2>Mon espace</h2>
		<?php
			if(!$session->read('Auth.User.id')){
				echo $this->element('login'); 
			} else {
				echo $this->element('space'); 
			}
		?>
	</div>

</div>

<div id="main_modules">
	<div class="module">
		<h2>Comments</h2>
		<?php
		foreach($sentenceComments as $lang => $commentsInLang){
			if($lang != 'unknown'){
				echo '<h3>'.$languages->codeToName($lang).'</h3>';
			}else{
				echo '<h3>'.__('Other languages', true).'</h3>';
			}
			
			if(count($commentsInLang) > 0){
				echo '<table class="comments">';
				foreach($commentsInLang as $comment){
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
						echo $date->ago($comment['SentenceComment']['created']);
						echo '<br/>';
						echo $html->link(
							$comment['User']['username'], 
							array("controller" => "users", "action" => "show", $comment['User']['id'])	
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
			}else{
				__('There are no comments in this language');
			}
		}
		?>
	</div>
</div>


