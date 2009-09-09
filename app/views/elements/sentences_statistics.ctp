<?php
$stats = $this->requestAction('/sentences/statistics');
if (isset($this->params['lang'])) { 
	Configure::write('Config.language',  $this->params['lang']); 
}

echo '<div class="sentencesStats">';
__('Number of sentences :');

echo '<ul>';
for($i = 0; $i < 5; $i++){
	$stat = $stats[$i];
	echo '<li class="stat stats_'.$stat['Sentence']['lang'].'" title="'.$languages->codeToName($stat['Sentence']['lang']).'">';
	echo '<span class="tooltip">'.$stat['Sentence']['lang'].' : </span>';
	echo $stat[0]['count'];
	echo '</li>';
}
echo '</ul>';

echo '<ul class="minorityLanguages" style="display:none">';
for($i = 5; $i < count($stats); $i++){
	$stat = $stats[$i];
	echo '<li class="stat stats_'.$stat['Sentence']['lang'].'" title="'.$languages->codeToName($stat['Sentence']['lang']).'">';
	echo '<span class="tooltip">'.$stat['Sentence']['lang'].' : </span>';
	echo $stat[0]['count'];
	echo '</li>';
}
echo '</ul>';

echo '<a class="statsDisplay showStats">[+] '. __('show all', true) . '</a>';
echo '<a class="statsDisplay hideStats" style="display:none">[-] '. __('top 5 only', true) . '</a>';

echo '</div>';


?>