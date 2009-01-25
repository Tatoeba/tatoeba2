<?php
class CommentsHelper extends AppHelper {

	var $helpers = array('Form', 'Date', 'Tooltip');

	function displayComment($username, $datetime, $comment){
		echo '<div class="comment">';
			echo '<div class="header">';
			echo '<span class="username">'.$username.'</span>';
			echo '<span class="date">'.$this->Date->ago($datetime).'</span>';
			echo '</div>';
			
			echo '<div class="content">';
			echo nl2br($comment);
			echo '</div>';
		echo '</div>';
	}
	
	function displayCommentForm($sentence_id){
		echo $this->Form->create('SentenceComment', array("action"=>"save"));
		$this->Tooltip->display(__('Even though you can comment on the whole group of sentences, keep in mind that your comment will be <strong>linked only to the main sentence</strong> (at the top).',true));
		__('Add a comment : ');
		echo $this->Form->input('sentence_id', array("type"=>"hidden", "value"=>$sentence_id));
		echo $this->Form->input('text', array("label"=> ""));
		echo $this->Form->end('Submit');
	}
	
}