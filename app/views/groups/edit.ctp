<div class="groups form">
<?php echo $form->create('Group');?>
	<fieldset>
 		<legend><?php echo 'Edit Group';?></legend>
	<?php
		echo $form->input('id');
		echo $form->input('name');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link('Delete', array('action'=>'delete', $form->value('Group.id')), null, sprintf('Are you sure you want to delete # %s?', $form->value('Group.id'))); ?></li>
		<li><?php echo $html->link('List Groups', array('action'=>'index'));?></li>
	</ul>
</div>
