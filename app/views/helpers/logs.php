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
class LogsHelper extends AppHelper {

	var $helpers = array('Date', 'Html');
	
	function entry($contribution, $user = null){
		$type = '';
		$status = '';
		
		if($contribution['translation_id'] == ''){
			$type = 'sentence';
		}else{
			$type = 'link';
		}
		
		switch($contribution['action']){
			case 'suggest' : 
				$type = 'correction';
				$status = 'Suggested'; 
				break;
			case 'insert' :
				$status = 'Added';
				break;
			case 'update' :
				$status = 'Modified';
				break;
			case 'delete' :
				$status = 'Deleted';
				break;
		}
		
		echo '<tr class="'.$type.$status.'">';
			echo '<td class="sentence_id">';
			echo $this->Html->link(
				$contribution['sentence_id'],
				array(
					"controller" => "sentences",
					"action" => "show",
					$contribution['sentence_id']
				)
			);
			echo '</td>';
			
			echo '<td class="lang">';
			if($type == 'link'){
				echo '&raquo;';
				//echo ($contribution['translation_lang'] == '') ? '?' : $contribution['translation_lang'];
			} else {
				if ($contribution['sentence_lang'] == '') {
					echo '?';
				} else {
					echo $this->Html->image(
						$contribution['sentence_lang'].".png", 
						array("alt" => $contribution['sentence_lang'],
							"class" => "flag"));
				}
				
			}
			echo '</td>';
			
			echo '<td class="text">';
			if($type == 'link'){
				echo $this->Html->link(
				$contribution['translation_id'],
				array(
					"controller" => "sentences",
					"action" => "show",
					$contribution['translation_id']
				)
			);
			}else{
				echo $contribution['text'];
			}
			echo '</td>';
			
			echo '<td class="username">';
			if(isset($user['username'])){
				echo $this->Html->link($user['username'], array("controller" => "users", "action" => "show", $user['id']));
			}
			echo '</td>';
			
			echo '<td class="date">';
			echo $this->Date->ago($contribution['datetime']);
			echo '</td>';
		echo '</tr>';
	}
	
	function annexeEntry($contribution, $user = null){
		$type = '';
		$status = '';
		
		if($contribution['translation_id'] == ''){
			$type = 'sentence';
		}else{
			$type = 'link';
		}
		
		switch($contribution['action']){
			case 'suggest' : 
				$type = 'correction';
				$status = 'Suggested'; 
				break;
			case 'insert' :
				$status = 'Added';
				break;
			case 'update' :
				$status = 'Modified';
				break;
			case 'delete' :
				$status = 'Deleted';
				break;
		}
		
		echo '<div class="annexeLogEntry '.$type.$status.'">';
			echo '<div>';
				if(isset($user['username'])){
					echo $this->Html->link($user['username'], array("controller" => "users", "action" => "show", $user['id']));
					echo ' - ';
				}
				echo $this->Date->ago($contribution['datetime']);
			echo '</div>';
			
			echo '<div>';
			echo $this->Html->link(
				$contribution['sentence_id'],
				array(
					"controller" => "sentences",
					"action" => "show",
					$contribution['sentence_id']
				)
			);
			
			if($type == 'link'){
				
				echo ' &raquo; ';
				
				echo $this->Html->link(
				$contribution['translation_id'],
				array(
					"controller" => "sentences",
					"action" => "show",
					$contribution['translation_id']
				));
				
			} else {
				echo ' <span class="text">' . $contribution['text'] . '</span>';
			}
			echo '</div>';
		echo '</div>';
	}
}
?>
