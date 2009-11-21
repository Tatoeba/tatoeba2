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
App::import('Core', 'Sanitize');

class CommentsHelper extends AppHelper {

	var $helpers = array('Form', 'Date', 'Html');
	
	/**
	 * Display a sentence comment block.
	 * If $displayAsThread is set to true, it will display the "view" button
	 * and the sentence in relation to the comment.
	 */
	function displaySentenceComment($comment, $displayAsThread = false){
		$sentenceComment = isset($comment['SentenceComment']) ? $comment['SentenceComment'] : $comment;
		
		echo '<li>';
			echo '<ul class="meta">';
				$image = (empty($comment['User']['image'])) ? 'unknown-avatar.jpg' : $comment['User']['image'];
				
				// view button
				if($displayAsThread){
					echo '<li class="viewButton">';
					echo $this->Html->link(
						$this->Html->image(
							'view.png',
							array("title" => __('View all comments on the related sentence',true))
						),
						array("controller" => "sentence_comments", "action" => "show", $sentenceComment['sentence_id'].'#comments'),
						array("escape" => false)
					);
					echo '</li>';
				}
				
				// user avatar
				echo '<li class="image">';
				echo $this->Html->link(
					$this->Html->image('profiles/'.$image, array("title" => __('View this user\'s profile', true)))
					, array("controller" => "user", "action" => "profile", $comment['User']['username'])
					, array("escape" => false)
				);
				echo '<li>';
				
				// author
				echo '<li class="author">';
				echo $this->Html->link(
					$comment['User']['username']
					, array('controller' => 'privateMessages', 'action' => 'write', $comment['User']['username'])
					, array("title" => __('Contact this user',true))
				);
				echo '</li>';
				
				// date
				echo '<li class="date">'.$this->Date->ago($sentenceComment['created']).'</li>';
			echo '</ul>';

			
			echo '<div class="body">';
				// sentence
				if($displayAsThread){
					echo '<div class="sentence">';
					if(isset($comment['Sentence']['text'])){
						echo $this->Html->link(
							$comment['Sentence']['text']
							, array("controller"=>"sentences", "action"=>"show", $comment['Sentence']['id'].'#comments')
						);
					}else{
						echo '<em>'.__('sentence deleted',true).'</em>';
					}
					echo '</div>';
				}
				
				// comment text
				$commentText = $this->clickableURL($sentenceComment['text']);
				echo nl2br($commentText);
				echo '</div>';
		echo '</li>';
	}

	function clickableURL($comment){
		$comment = preg_replace('#(https?://[^<)\s ]{0,50})[^<)\s ]{0,}#i', '<a target="_blank" href=\'$0\'>$1</a>', $comment);
		return $comment;
	}

	function displayCommentForm($sentence_id, $sentence_text){
		echo $this->Form->create('SentenceComment', array("action"=>"save"));
		echo $this->Form->input('sentence_id', array("type"=>"hidden", "value"=>$sentence_id));
		echo $this->Form->input('sentence_text', array("type"=>"hidden", "value"=>$sentence_text));
		echo $this->Form->input('text', array("label"=> "", "cols"=>"64", "rows"=>"6"));
		echo $this->Form->end('Submit');
	}

}
