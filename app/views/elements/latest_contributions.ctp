<?php
$contributions = $this->requestAction('/contributions/latest');
if (isset($this->params['lang'])) { 
	Configure::write('Config.language',  $this->params['lang']); 
}

echo '<table id="logs">';
foreach($contributions as $contribution){
	$logs->entry($contribution['Contribution'], $contribution['User']);
	//$logs->entry($contribution['Contribution']);
}
echo '</table>';
?>