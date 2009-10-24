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
$this->pageTitle = __('Comments on the sentence : ',true) . $sentence['Sentence']['text'];

?>

<div id="annexe_content">
	<div class="module">
		<?php
			if(!$session->read('Auth.User.id')){
				echo $this->element('login'); 
			} else {
				echo $this->element('space'); 
			}
		?>
	</div>
</div>

<div id="main_content">
	<div class="module">
		<?php
		// navigation (previous, random, next)
		$navigation->displaySentenceNavigation($sentence['Sentence']['id']);
		
		echo '<div class="sentences_set">';
			$specialOptions['belongsTo'] = $sentence['User']['username']; // TODO set up a better mechanism
			$sentences->displayMenu($sentence['Sentence']['id'], $sentence['Sentence']['lang'], $specialOptions);
			
			$sentence['User']['canEdit'] = $specialOptions['canEdit']; // TODO set up a better mechanism
			$sentences->displayGroup($sentence['Sentence'], $sentence['Translation'], $sentence['User']);
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
	</div>
</div>


