<?php
$this->pageTitle = __('Add comment for sentence',true);
?>

<h1><?php __('Add a comment'); ?></h1>
<?php
echo $form->create('SentenceComment', array("action"=>"save"));
echo $form->input('sentence_id', array("type"=>"hidden"));
echo $form->input('text');
echo $form->end('Submit');
?>