<?php
$id = ($session->read('last_user_id') > 0) ? $session->read('last_user_id') : 1;
$navigation->displayUsersNavigation($id);
?>


<h2>
<?php
echo $paginator->counter(array('format' => 'Users (total %count%)'));
?>
</h2>

<div class="paging">
<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
<?php echo $paginator->numbers(array('separator' => ''));?>
<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>


<table class="users">
<tr>
	<th><?php echo $paginator->sort('username');?></th>
	<th><?php echo $paginator->sort('since');?></th>
	<th><?php echo $paginator->sort('group_id');?></th>
</tr>
<?php
$i = 0;
foreach ($users as $user):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $html->link($user['User']['username'], '/users/show/'.$user['User']['id']); ?>
		</td>
		<td>
			<?php echo $date->ago($user['User']['since']); ?>
		</td>
		<td>
			<?php echo $user['Group']['name']; ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>


<div class="paging">
<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
<?php echo $paginator->numbers(array('separator' => ''));?>
<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>