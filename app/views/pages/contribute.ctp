<?php
echo '<h1 class="contribute">';
__('How would you like to contribute?');
echo '</h1>';

echo '<h2 class="add">';
__('Add your own sentences');
echo '</h2>';
echo '<div class="sentences_set">';
	echo '<div class="new">';
	echo $form->create('Sentence', array("action" => "add", "class" => "add"));
	echo $form->input('text', array("label" => __('Sentence : ', true)));
	echo $form->end('OK');
	echo '</div>';
echo '</div>';


echo '<br/>';

echo '<h2 class="translate">';
__('Translate existing sentences');
echo '</h2>';

__('Choose the language you would like to translate from : ');
$langArray = $languages->languagesArray();
asort($langArray);
echo $form->select('Sentence.lang', $langArray);
?>