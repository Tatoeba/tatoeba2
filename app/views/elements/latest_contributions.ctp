<?php
$contributions = $this->requestAction('/contributions/latest');

foreach($contributions as $contribution){
	echo $contribution['Contribution']['text'];
	echo '<br/>';
}
?>