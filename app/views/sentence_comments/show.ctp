<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

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


// navigation (previous, random, next)
$navigation->displaySentenceNavigation();
?>

<div id="main_content">
	<div class="module">
		<?php
		if($sentenceExists){
			echo '<h2>' . __('Sentence nº', true) . $sentence['Sentence']['id'] . '</h2>';
			
			$this->pageTitle = __('Comments on the sentence : ',true) . $sentence['Sentence']['text'];

			echo '<div class="sentences_set">';
				$specialOptions['belongsTo'] = $sentence['User']['username']; // TODO set up a better mechanism
				$sentences->displayMenu($sentence['Sentence']['id'], $sentence['Sentence']['lang'], $specialOptions);

				$sentence['User']['canEdit'] = $specialOptions['canEdit']; // TODO set up a better mechanism
				$sentences->displayGroup($sentence['Sentence'], $sentence['Translation'], $sentence['User']);
			echo '</div>';

		}else{

			$this->pageTitle = __('Comments for sentence nº',true) . $this->params['pass'][0];

			echo '<em>';
			__('The sentence has been deleted');
			echo '</em>';

		}
	?>
	</div>
	
	<div class="module">
	<?php		
		echo '<div class="addComment">';
		echo $html->link(
			__('Add a comment',true),
			array("controller" => "sentence_comments", "action" => "add", $sentence['Sentence']['id'])
		);
		echo '</div>';		
		
		echo '<h2>';
		__('Comments');
		echo '</h2>';
		
		echo '<a name="comments"></a>';
		echo '<div class="comments">';
		if(count($sentenceComments) > 0){
			foreach($sentenceComments as $comment){
				$comments->displayComment(
					$comment['User']['id'],
					$comment['User']['username'],
					$comment['SentenceComment']['created'],
					$comment['SentenceComment']['text']
				);
			}
		}else{
			echo '<em>' . __('There are no comments for now.', true) .'</em>';
		}
		echo '</div>';
		?>
	</div>
</div>


