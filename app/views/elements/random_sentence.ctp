<?php
$random = $this->requestAction('/sentences/random');

$sentence = $random['Sentence'];
$translations = $random['Translation'];
$specialOptions = $random['specialOptions'];

echo '<div class="sentences_set">';
	
	$sentences->displayMenu($sentence['id'], $sentence['lang'], $sentence['correctness'], $specialOptions);
	$sentences->displayGroup($sentence, $translations);
	/*
	echo '<ul class="sentence translations">';
		// Sentence
		echo '<li class="original">'.$sentence['text'].'</li>';
		
		if(count($translations) > 0){
			// Translations
			foreach($translations as $translation){
				echo '<li class="direct translation">';
					echo '<em>'.$translation['lang'].'</em>';
					echo $translation['text'];
				echo '</li>';
			}
		}
	echo '</ul>';
	*/
echo '</div>';	

?>