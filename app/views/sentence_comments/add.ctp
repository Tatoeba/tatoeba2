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
$this->pageTitle = __('Add comment for sentence',true);
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
	<?php
	// navigation (previous, random, next)
	$navigation->displaySentenceNavigation($sentence['Sentence']['id']);
	
	echo '<div class="sentences_set">';
		// sentence menu (translate, edit, comment, etc)
		$specialOptions['belongsTo'] = $sentence['User']['username']; // TODO set up a better mechanism
		$sentences->displayMenu($sentence['Sentence']['id'], $sentence['Sentence']['lang'], $specialOptions);
	
		// sentence and translations
		$sentence['User']['canEdit'] = $specialOptions['canEdit']; // TODO set up a better mechanism
		$sentences->displayGroup($sentence['Sentence'], $sentence['Translation'], $sentence['User']);
	echo '</div>';	
	
	$comments->displayCommentForm($sentence['Sentence']['id'], $sentence['Sentence']['text']);
	
	if(count($sentence['SentenceComment']) > 0){
		echo '<div class="comments">';
		foreach($sentence['SentenceComment'] as $comment){
			$comments->displayComment($comment['User']['id'], $comment['User']['username'], $comment['created'], $comment['text']);
		}
		echo '</div>';
	}
	?>
	</div>
</div>

