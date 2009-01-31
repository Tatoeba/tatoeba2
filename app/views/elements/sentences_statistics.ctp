<?php
$stats = $this->requestAction('/sentences/statistics');
if (isset($this->params['lang'])) { 
	Configure::write('Config.language',  $this->params['lang']); 
}

echo '<div class="sentencesStats">';
__('Number of sentences :');
echo '<ul>';
foreach($stats as $stat){
	echo '<li class="stat stats_'.$stat['Sentence']['lang'].'">';
	echo '<span class="tooltip">'.$stat['Sentence']['lang'].' : </span>';
	echo $stat[0]['count'];
	echo '</li>';
}
echo '</ul>';
echo '</div>';
?>