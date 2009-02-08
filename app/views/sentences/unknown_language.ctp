<?php
echo '<h2>';
__('Unknown language');
echo '</h2>';

echo '<p>';
__('The language of the following sentences could not be detected, you have to specify it manually. ');
__('If your language is not in the list of supported languages, contact me.');
echo '</p>';

$i = 0;
echo $form->create('Sentence', array('action'=>'set_languages'));
echo '<ul>';
foreach($unknownLangSentences as $sentence){
	echo '<li>';
	echo $form->input('Sentence.'.$i.'.id', array("value" => $sentence['Sentence']['id']));
	echo $form->select('Sentence.'.$i.'.lang', $languages->languagesArray()); $i++;
	echo ' ';
	echo $sentence['Sentence']['text'];
	echo '</li>';
}
echo '</ul>';
echo $form->end(__('save',true));
?>