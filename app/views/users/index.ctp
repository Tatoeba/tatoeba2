<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  TATOEBA Project(should be changed)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
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
	<th class="num"></th>
	<th><?php echo $paginator->sort('id');?></th>
	<th><?php echo $paginator->sort('username');?></th>
	<th><?php echo $paginator->sort('email');?></th>
	<th><?php echo $paginator->sort('lang');?></th>
	<th><?php echo $paginator->sort('since');?></th>
	<th><?php echo $paginator->sort('last_time_active');?></th>
	<th><?php echo $paginator->sort('group_id');?></th>
	<th class="actions"></th>
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
		<td class="num">
			<?php echo $i; ?>
		</td>
		<td>
			<?php echo $user['User']['id']; ?>
		</td>
		<td>
			<?php echo $user['User']['username']; ?>
		</td>
		<td>
			<?php echo $user['User']['email']; ?>
		</td>
		<td>
			<?php echo $user['User']['lang']; ?>
		</td>
		<td>
			<?php echo $date->ago($user['User']['since']); ?>
		</td>
		<td>
			<?php echo $date->ago($user['User']['last_time_active'],true); ?>
		</td>
		<td>
			<?php echo $user['Group']['name']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link('Edit', array('action'=>'edit', $user['User']['id'])); ?>
			<?php echo $html->link('Delete', array('action'=>'delete', $user['User']['id']), null, sprintf('Are you sure you want to delete # %s?', $user['User']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>


<div class="paging">
<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
<?php echo $paginator->numbers(array('separator' => ''));?>
<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
