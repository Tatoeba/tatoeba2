<?php
$random = $this->requestAction('/sentences/random');
if (isset($this->params['lang'])) { 
	Configure::write('Config.language',  $this->params['lang']); 
}

$sentence = $random['Sentence'];
$translations = $random['Translation'];
$specialOptions = $random['specialOptions'];

echo '<div class="sentences_set">';
	$sentences->displayMenu($sentence['id'], $specialOptions);
	$sentences->displayGroup($sentence, $translations);
echo '</div>';	

?>