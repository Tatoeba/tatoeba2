<?php
$this->pageTitle = 'Contribute in Tatoeba';

echo '<h2 class="add">';
__('Add a new sentence');
echo '</h2>';

echo '<div class="sentences_set">';
	echo '<div class="sentence">';
	echo '<div class="new">';
	echo $form->create('Sentence');
	echo $form->input('text', array("label" => __('Sentence : ',true)));
	echo $form->end('OK');
	echo '</div>';
	echo '</div>';
echo '</div>';

echo '<br/>';
echo '<br/>';


echo '<h2 class="translate">';
__('Translate the sentence');

echo ' (';
echo $html->link(__('show another',true), "#", array("id" => "showRandom", "lang" => $this->params['lang']));
echo ')';
echo '</h2>';

echo '<div class="random_sentences_set"></div>';


?>