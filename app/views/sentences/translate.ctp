<?php
echo '<h1>' . __('Translate this sentence',true) . '</h1>';

echo $form->create('Sentence', array("action" => "save_translation"));
echo $sentence['Sentence']['id'].'. '.$sentence['Sentence']['text'];
echo $form->input('lang');
echo $form->input('text');
echo $form->input('id', array("type" => "hidden"));
echo $form->input('sentence_lang', array("type" => "hidden", "value" => $sentence['Sentence']['lang'])); // for logs
echo $form->end(__('Save translation',true));
?>