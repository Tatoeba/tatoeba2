<?php
//pr($stats);

// should not be in view but never mind...
function color($number){
	if($number > 500){
		$color = 10;
	}else{
		$color = intval($number/50);
	}
	return $color;
}

$maxWidth = 600;
$maxTotal = 0;
foreach($stats as $stat){
	if($stat[0]['total'] > $maxTotal){
		$maxTotal = $stat[0]['total'];
	}
}

echo '<table id="timeline">';
foreach($stats as $stat){
	$total = $stat[0]['total'];
	$percent = $total / $maxTotal;
	$width = intval($percent * $maxWidth);
	$color = color($total);
	
	echo '<tr>';
		echo '<td class="date">';
		echo $stat[0]['day'];
		echo '</td>';
		
		echo '<td class="number color'.$color.'">';
		echo '<strong>'.$total.'</strong>';
		echo '</td>';
		
		echo '<td class="line">';
		echo '<div class="logs_stats color'.$color.'" style="width:'.$width.'px"></div>';
		echo '</td>';
	echo '</tr>';
}
echo '</table>';
?>