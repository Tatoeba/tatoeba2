<?php
class TooltipHelper extends AppHelper {
	var $helpers = array('Html');
	
	function display($text){
		echo '<a class="info" href="#">';
		echo $this->Html->image(
			'help.png', 
			array('alt'=> __("help", true))
		);
		echo '<span>'.$text.'</span>';
		echo '</a>';
	}
}
?>