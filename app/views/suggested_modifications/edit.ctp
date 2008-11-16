<div class="suggestedModifications form">
<?php echo $form->create('SuggestedModification');?>
	<fieldset>
 		<legend><?php __('Edit SuggestedModification');?></legend>
	<?php
		echo $form->input('id');
		echo $form->input('sentence_id');
		echo $form->input('sentence_lang');
		echo $form->input('correction_text');
		echo $form->input('submit_user_id');
		echo $form->input('submit_datetime');
		echo $form->input('apply_user_id');
		echo $form->input('apply_datetime');
		echo $form->input('was_applied');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('SuggestedModification.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('SuggestedModification.id'))); ?></li>
		<li><?php echo $html->link(__('List SuggestedModifications', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Sentences', true), array('controller'=> 'sentences', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Sentence', true), array('controller'=> 'sentences', 'action'=>'add')); ?> </li>
	</ul>
</div>
