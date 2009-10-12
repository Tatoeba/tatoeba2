<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  TATOEBA Project(should be changed)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
//pr($stats);

// should not be in view but never mind...
function color($number){
	if($number > 200){
		$color = 10;
	}else{
		$color = intval($number/20);
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
