<?php
$contributions = $this->requestAction('/contributions/latest');

echo '<table id="logs">';
foreach($contributions as $contribution){
	$type = '';
	$status = '';
	
	if($contribution['Contribution']['translation_id'] == ''){
		$type = 'sentence';
	}else{
		$type = 'translation';
	}
	
	switch($contribution['Contribution']['action']){
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
		echo $contribution['Contribution']['sentence_id'];
		echo '</td>';
		
		echo '<td>';
		echo $contribution['Contribution']['sentence_lang'];
		echo '</td>';
		
		echo '<td>';
		echo $contribution['User']['username'];
		echo '</td>';
		
		echo '<td>';
		echo $date->ago($contribution['Contribution']['datetime']);
		echo '</td>';
		
		echo '<td>';
		echo $contribution['Contribution']['text'];
		echo '</td>';
	echo '</tr>';
}
echo '</table>';
?>