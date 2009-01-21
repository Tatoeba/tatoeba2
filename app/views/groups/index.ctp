<div class="groups index">
<h2><?php echo 'Groups';?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => 'Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('id');?></th>
	<th><?php echo $paginator->sort('name');?></th>
	<th><?php echo $paginator->sort('created');?></th>
	<th><?php echo $paginator->sort('modified');?></th>
	<th class="actions"><?php echo 'Actions';?></th>
</tr>
<?php
$i = 0;
foreach ($groups as $group):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $group['Group']['id']; ?>
		</td>
		<td>
			<?php echo $group['Group']['name']; ?>
		</td>
		<td>
			<?php echo $group['Group']['created']; ?>
		</td>
		<td>
			<?php echo $group['Group']['modified']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link('View', array('action'=>'view', $group['Group']['id'])); ?>
			<?php echo $html->link('Edit', array('action'=>'edit', $group['Group']['id'])); ?>
			<?php echo $html->link('Delete', array('action'=>'delete', $group['Group']['id']), null, sprintf('Are you sure you want to delete # %s?', $group['Group']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link('New Group', array('action'=>'add')); ?></li>
	</ul>
</div>
