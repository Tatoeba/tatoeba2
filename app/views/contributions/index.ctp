<div id="logsLegend">
<span class="sentenceAdded"><?php __('sentence added') ?></span>
<span class="translationAdded"><?php __('translation added') ?></span>
<span class="sentenceModified"><?php __('sentence modified') ?></span>
<span class="correctionSuggested"><?php __('correction suggested') ?></span>
<span class="sentenceDeleted"><?php __('sentence deleted') ?></span>
<span class="translationDeleted"><?php __('translation deleted') ?></span>
</div>

<table id="logs">
<?php
foreach ($contributions as $contribution){
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
?>
</table>