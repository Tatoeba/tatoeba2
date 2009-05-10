<?php
echo $javascript->link('sentences.add_translation.js', true);

$sentence = $random['Sentence'];
$translations = isset($random['Translation']) ? $random['Translation'] : null;
$specialOptions = $random['specialOptions'];

echo '<div class="sentences_set">';
	$sentences->displayMenu($sentence['id'], $sentence['lang'], $specialOptions);
	if($type == 'translate'){
		$sentences->displayForTranslation($sentence, $translations);
	}else{
		$sentences->displayGroup($sentence, $translations);
	}
echo '</div>';
?>