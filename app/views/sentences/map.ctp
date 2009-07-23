<?
$i = 1+ ($page-1)*10000;
echo '<div id="sentencesMap">';
foreach($all_sentences as $sentence){
	while($i < $sentence['Sentence']['id']){
		echo '<div class="empty" title="'.$i.'"></div>';
		$i++;
	}
	echo '<div class="'.$sentence['Sentence']['lang'].'_cluster" title="'.$i.', '.$sentence['Sentence']['lang'].'">';
	//echo $i.'<br/>'.$sentence['Sentence']['lang'];
	echo '</div>';
	$i++;
}
echo '</div>';
?>