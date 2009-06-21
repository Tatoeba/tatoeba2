<?php
echo '<table id="usersStatistics">';
	echo '<tr>';
	echo '<th>' . __('rank', true) . '</th>';
	echo '<th>' . __('username', true) . '</th>';
	echo '<th>' . __('member since', true) . '</th>';
	echo '<th>' . __('number of contributions', true) . '</th>';
	echo '</tr>';
	
$i = 0;	
foreach($stats as $stat){
	echo '<tr><td>';
	echo $i; $i++;
	echo '</td><td>';
	echo $html->link($stat['User']['username'], array("controller"=>"users", "action"=>"show", $stat['User']['id']));
	echo '</td><td>';
	echo $date->ago($stat['User']['since']);
	echo '</td><td>';
	echo $stat['0']['total'];
	echo '</td></tr>';
}
echo '</table>';
?>