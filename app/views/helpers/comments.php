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
class CommentsHelper extends AppHelper {

	var $helpers = array('Form', 'Date', 'Tooltip', 'Html');

	function displayComment($id, $username, $datetime, $comment){
		echo '<div class="comment">';
			echo '<div class="header">';
			echo '<span class="username">';
			echo $this->Html->link($username, array("controller" => "users", "action" => "show", $id));
			echo '</span>';
			echo '<span class="date">'.$this->Date->ago($datetime).'</span>';
			echo '</div>';
			
			echo '<div class="content">';
			$comment = $this->clickableURL($comment);
			echo nl2br($comment);
			echo '</div>';
		echo '</div>';
	}
	
	function clickableURL($comment){
		$comment = preg_replace('#(https?://[^<)\s ]{0,50})[^<)\s ]{0,}#i', '<a target="_blank" href=\'$0\'>$1</a>', $comment);
		return $comment;
	}
	
	function displayCommentForm($sentence_id, $sentence_text){
		echo $this->Form->create('SentenceComment', array("action"=>"save"));
		$this->Tooltip->display(__('Even though you can comment on the whole group of sentences, keep in mind that your comment will be <strong>linked only to the main sentence</strong> (at the top).',true));
		__('Add a comment : ');
		echo $this->Form->input('sentence_id', array("type"=>"hidden", "value"=>$sentence_id));
		echo $this->Form->input('sentence_text', array("type"=>"hidden", "value"=>$sentence_text));
		echo $this->Form->input('text', array("label"=> ""));
		echo $this->Form->end('Submit');
	}
	
}
