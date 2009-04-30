<?php
$sentence = $random['Sentence'];
$translations = isset($random['Translation']) ? $random['Translation'] : null;
$specialOptions = $random['specialOptions'];

echo '<div class="sentences_set">';
	$sentences->displayMenu($sentence['id'], $specialOptions);
	$sentences->displayGroup($sentence, $translations);
echo '</div>';
?>