<?php
$random = $this->requestAction('/sentences/random');

$sentence = $random['Sentence'];
$translations = $random['Translation'];
$specialOptions = $random['specialOptions'];

echo '<div class="sentences_set">';
	$sentences->displayMenu($sentence['id'], $sentence['lang'], $sentence['correctness'], $specialOptions);
	$sentences->displayGroup($sentence, $translations);
echo '</div>';	

?>