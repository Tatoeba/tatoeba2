<?php
$javascript->link('jquery.jeditable.js', false);
$javascript->link('sentences.edit_in_place.js', false);
foreach($user_sentences as $sentence){
	$sentences->displayEditableSentence($sentence['Sentence']);
}
?>