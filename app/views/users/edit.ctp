<div class="editUser">
<div class="actions">
	<ul>
		<li><?php echo $html->link('Delete', array('action'=>'delete', $form->value('User.id')), null, sprintf('Are you sure you want to delete # %s?', $form->value('User.id'))); ?></li>
		<li><?php echo $html->link('List Users', array('action'=>'index'));?></li>
		<li><?php echo $html->link('List Groups', array('controller'=> 'groups', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link('New Group', array('controller'=> 'groups', 'action'=>'add')); ?> </li>
	</ul>
</div>

<?php echo $form->create('User');?>
	<fieldset>
 		<legend><?php echo 'Edit User';?></legend>
	<?php
		echo $form->input('id');
		echo $form->input('username');
		echo $form->input('email');
		echo $form->input('lang');
		echo $form->input('group_id');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
