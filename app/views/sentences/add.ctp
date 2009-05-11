<?php
$javascript->link('sentences.contribute.js', false);

echo '<h2 class="add">';
__('Add another sentence');
echo '</h2>';

echo '<div class="sentences_set">';
	echo '<div class="new">';
	echo $form->input('text', array("label" => __('Sentence : ', true), "id" => "newSentenceText"));
	echo $form->button('OK', array("id" => "submitNewSentence"));
	echo '</div>';
echo '</div>';

echo '<br/>';

echo '<h2>';
__('Sentences added');
echo '</h2>';

echo '<div id="sentencesAdded">';
	if(isset($sentence)){
		echo '<div class="sentences_set">';
		// sentence menu (translate, edit, comment, etc)
		$sentences->displayMenu($sentence['Sentence']['id'], $specialOptions);

		// sentence and translations
		$sentences->displayForTranslation($sentence['Sentence'], array());
		echo '</div>';
	}
echo '</div>';
?>