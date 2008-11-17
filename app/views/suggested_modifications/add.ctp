<div class="suggestedModifications form">
<?php echo $form->create('SuggestedModification', array("action" => "save_suggestion"));?>
	<fieldset>
 		<legend><?php __('Add SuggestedModification');?></legend>
	<?php
		//echo $form->input('sentence_id');
		//echo $form->input('sentence_lang');
		echo $form->input('correction_text');
		//echo $form->input('submit_user_id');
		//echo $form->input('submit_datetime');
		//echo $form->input('apply_user_id');
		//echo $form->input('apply_datetime');
		//echo $form->input('was_applied');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>