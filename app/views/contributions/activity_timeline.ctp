<?php
//pr($stats);

// should not be in view but never mind...
function color($number){
	if($number > 1000){
		$color = 10;
	}else{
		$color = intval($number/1000);
	}
	return $color;
}

echo '<div id="legend">';
for($i=0; $i < 11; $i++){
	echo '<div class="color'.$i.'">';
	echo 100*$i.'+';
	echo '</div>';
}
echo '</div>';

echo '<div id="timeline">';
foreach($stats as $stat){
	echo '<div class="logs_stats color'.color($stat[0]['total']).'">';
	echo $stat[0]['day'];
	echo '<strong>'.$stat[0]['total'].'</strong>';
	echo '</div>';
}
echo '</div>';
?>