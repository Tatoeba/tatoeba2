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
<div id="annexe_content">
	<div class="module">
	<h2><?php __('Notifiy mistakes') ?></h2>
	<p>
	<?php
	__('Do not hesitate to post a comment if you see a mistake!');
	?>
	</p>
	<p>
	<?php
	__('NOTE : If the sentence does not belong to anyone and you know how to correct the mistake, feel free to correct it without posting any comment. You will have to adopt the sentence before you can edit it.');
	?>
	</p>
	</div>
</div>

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
		echo '<h2>';
		__('Comments');
		echo '</h2>';
		
		echo '<a name="comments"></a>';
		echo '<ol class="comments">';
		if(count($sentenceComments) > 0){
			foreach($sentenceComments as $comment){
				$comments->displaySentenceComment($comment);
			}
		}else{
			echo '<em>' . __('There are no comments for now.', true) .'</em>';
		}
		echo '</ol>';
		
		if($sentenceExists){
			echo '<a name="add_comment"></a>';
			echo '<h2>';
			__('Add a comment');
			echo '</h2>';
			if($session->read('Auth.User.id')){
				$comments->displayCommentForm($sentence['Sentence']['id'], $sentence['Sentence']['text']);
			}else{
				echo '<p>';
				echo sprintf(
					__('You need to be logged in to add a comment. If you are not registered, you can <a href="">register here</a>.',true),
					$html->link(array("controller"=>"users", "action"=>"register"))
				);
				echo '</p>';
			}
		}
		?>
	</div>
</div>


