<?php
class TooltipHelper extends AppHelper {
	var $helpers = array('Html');
	
	function display($text){
		echo '<a class="info" href="#">';
		echo $this->Html->image(
			'attention_with_cursor.png', 
			array('alt'=> '!')
		);
		echo '<span>'.$text.'</span>';
		echo '</a>';
	}
	
	function displayLogsColors(){
		$tooltipText  = __('Meaning of the colors :',true);
		$tooltipText .= '<table id="logsLegend">';
		$tooltipText .= '<tr>';
		$tooltipText .= '<td class="sentenceAdded">' . __('sentence added',true) .'</td>';
		$tooltipText .= '<td class="linkAdded">' . __('link added',true) .'</td>';
		$tooltipText .= '</tr>';

		$tooltipText .= '<tr>';
		$tooltipText .= '<td class="sentenceDeleted">' . __('sentence deleted',true) . '</td>';
		$tooltipText .= '<td class="linkDeleted">' . __('link deleted',true) . '</td>';
		$tooltipText .= '</tr>';
		
		$tooltipText .= '<tr>';
		$tooltipText .= '<td class="sentenceModified">' . __('sentence modified',true) . '</td>';
		//$tooltipText .= '<td class="correctionSuggested">' . __('correction suggested',true) . '</td>';
		$tooltipText .= '<td></td>';
		$tooltipText .= '</tr>';
		
		$tooltipText .= '</table>';
		$this->display($tooltipText);
	}
}
?>