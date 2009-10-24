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


<div id="annexe_content">
	<div class="module">
		<?php
			if(!$session->read('Auth.User.id')){
				echo $this->element('login'); 
			} else {
				echo $this->element('space'); 
			}
		?>
	</div>

</div>

<div id="main_content">
	<div class="module">
		<h2><?=$paginator->counter(array('format' => __('Users (total %count%)', true))); ?></h2>
		
		<?php
		$id = ($session->read('last_user_id') > 0) ? $session->read('last_user_id') : 1;
		$navigation->displayUsersNavigation($id);
		?>

		<div class="paging">
		<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
		<?php echo $paginator->numbers(array('separator' => ''));?>
		<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
		</div>
		<table class="users">
		<tr>
			<th><?php echo $paginator->sort(__('Username', true), 'username');?></th>
			<th><?php echo $paginator->sort(__('Country', true), 'country');?></th>
			<th><?php echo $paginator->sort(__('Member since', true),'since');?></th>
			<th><?php echo $paginator->sort(__('Member status', true),'group_id');?></th>
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
	</div>
</div>


