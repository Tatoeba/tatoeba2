<?php
class LogsHelper extends AppHelper {

	var $helpers = array('Date', 'Html');
	
	//function entry($contribution, $user){
	function entry($contribution){
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
			echo '<td class="id">';
			echo $this->Html->link(
				$contribution['sentence_id'],
				array(
					"controller" => "contributions",
					"action" => "show",
					$contribution['sentence_id']
				)
			);
			echo '</td>';
			
			echo '<td class="lang">';
			if($type.$status == 'linkAdded'){
				echo '>>>';
				//echo ($contribution['translation_lang'] == '') ? '?' : $contribution['translation_lang'];
			}else{
				echo ($contribution['sentence_lang'] == '') ? '?' : $contribution['sentence_lang'];
			}
			echo '</td>';
			
			echo '<td class="text">';
			if($type.$status == 'linkAdded'){
				echo $this->Html->link(
				$contribution['translation_id'],
				array(
					"controller" => "contributions",
					"action" => "show",
					$contribution['translation_id']
				)
			);
			}else{
				echo $contribution['text'];
			}
			echo '</td>';
			
			// echo '<td class="username">';
			// if(isset($user['username'])){
				// echo $user['username'];
			// }else{
				// echo '?';
			// }
			// echo '</td>';
			
			echo '<td class="date">';
			echo $this->Date->ago($contribution['datetime']);
			echo '</td>';
		echo '</tr>';
	}
}
?>