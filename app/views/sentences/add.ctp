<?php
$this->pageTitle = 'Add a sentence in Tatoeba';
?>

<h1><?php __('Add a sentence'); ?></h1>
<?php
echo $form->create('Sentence');
echo $form->input('lang');
echo $form->input('text');
echo $form->end('Save');
?>