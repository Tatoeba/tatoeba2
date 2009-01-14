<?php
if (isset($this->params['lang'])) { 
	Configure::write('Config.language',  $this->params['lang']); 
}
?>

<?php
$contributions = $this->requestAction('/contributions/latest');

echo '<table id="logs">';
foreach($contributions as $contribution){
	$logs->entry($contribution['Contribution'], $contribution['User']);
}
echo '</table>';
?>