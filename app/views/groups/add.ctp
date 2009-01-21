<div class="groups form">
<?php echo $form->create('Group');?>
	<fieldset>
 		<legend><?php echo 'Add Group';?></legend>
	<?php
		echo $form->input('name');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link('List Groups', array('action'=>'index'));?></li>
	</ul>
</div>
