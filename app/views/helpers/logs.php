<?php
class LogsHelper extends AppHelper {

	var $helpers = array('Date');
	
	function entry($contribution, $user){
		$type = '';
		$status = '';
		
		if($contribution['translation_id'] == ''){
			$type = 'sentence';
		}else{
			$type = 'translation';
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
			echo '<td>';
			echo $contribution['sentence_id'];
			echo '</td>';
			
			echo '<td>';
			echo $contribution['sentence_lang'];
			echo '</td>';
			
			echo '<td>';
			if(isset($user['username'])){
				echo $user['username'];
			}else{
				echo '?';
			}
			echo '</td>';
			
			echo '<td>';
			echo $this->Date->ago($contribution['datetime']);
			echo '</td>';
			
			echo '<td>';
			echo $contribution['text'];
			echo '</td>';
		echo '</tr>';
	}
}
?>