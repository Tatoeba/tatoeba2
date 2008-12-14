<div>
<?php $this->pageTitle = __('Tatoeba : Collecting example sentences',true); ?>

<h2><?php __('Welcome on Tatoeba Project'); ?></h2>
<p>
<?php __('This project aims to build a multilingual corpus. In other words, to collect sentences translated in several languages.'); ?>
</p>


<h2><?php __('Random sentence'); ?></h2>
<?php
echo $this->element('random_sentence');
?>


<h2><?php __('Latest contributions'); ?></h2>
<h2><?php __('Latest comments'); ?></h2>
<?php
echo $this->element('latest_sentence_comments', array('cache'=>'+1 hour'));
?>
</div>