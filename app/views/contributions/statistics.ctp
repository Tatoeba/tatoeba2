<?php
//pr($stats);
echo '<table id="usersStatistics">';
	echo '<tr>';
	echo '<th>' . __('rank', true) . '</th>';
	echo '<th>' . __('username', true) . '</th>';
	echo '<th>' . __('member since', true) . '</th>';
	echo '<th>' . __('number of contributions', true) . '</th>';
	echo '</tr>';
	
$i = 0;	
foreach($stats as $stat){
	$css = 'class=';
	if($stat['User']['group_id'] == 1){
		$css .= '"admin"';
	}
	if($stat['User']['group_id'] == 4){
		$css .= '"normal"';
	}
	if($stat['User']['group_id'] == 5){
		$css .= '"pending"';
	}
	
	echo '<tr '.$css.'><td>';
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