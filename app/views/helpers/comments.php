<?php
class CommentsHelper extends AppHelper {

	var $helpers = array('Form', 'Date');

	function displayComment($username, $datetime, $comment){
		echo '<div class="comment">';
			echo '<div class="header">';
			echo '<span class="username">'.$username.'</span>';
			echo '<span class="date">'.$this->Date->ago($datetime).'</span>';
			echo '</div>';
			
			echo '<div class="content">';
			echo $comment;
			echo '</div>';
		echo '</div>';
	}
	
	function displayCommentForm($sentence_id){
		echo $this->Form->create('SentenceComment', array("action"=>"save"));
		echo $this->Form->input('sentence_id', array("type"=>"hidden", "value"=>$sentence_id));
		echo $this->Form->input('text', array("label"=> __('Enter your comment : ',true)));
		echo $this->Form->end('Submit');
	}
	
}