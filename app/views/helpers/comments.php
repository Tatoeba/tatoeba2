<?php
class CommentsHelper extends AppHelper {

	var $helpers = array('Form');

	function displayComment($username, $date, $comment){
		echo '<div class="comment">';
			echo '<div class="header">';
			echo '<span class="username">'.$username.'</span>';
			echo '<span class="date">'.$date.'</span>';
			echo '</div>';
			
			echo '<div class="content">';
			echo $comment;
			echo '</div>';
		echo '</div>';
	}
	
	function displayCommentForm($sentence_id){
		echo $this->Form->create('SentenceComment', array("action"=>"save"));
		echo $this->Form->input('sentence_id', array("type"=>"hidden", "value"=>$sentence_id));
		echo $this->Form->input('text', array("label"=>""));
		echo $this->Form->end('Submit');
	}
	
}