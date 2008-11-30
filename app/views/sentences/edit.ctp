<?php 
echo '<h1>' . __('Edit this sentence',true) . '</h1>';

echo $form->create('Sentence', array("action" => "edit"));
echo $form->input('text');
echo $form->input('id', array("type" => "hidden"));
echo $form->input('lang', array("type" => "hidden"));
echo $form->end(__('Save sentence',true));
?>