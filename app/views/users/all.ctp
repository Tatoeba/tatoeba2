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



$id = ($session->read('last_user_id') > 0) ? $session->read('last_user_id') : 1;
$navigation->displayUsersNavigation($id);
?>

<div id="annexe_content">
	<div class="module">
	<h2><?php __('Top members') ?></h2>
	<?php
	$stats = $this->requestAction('/contributions/statistics/1');
	echo '<table id="topMembers">';
	echo '<tr>';
	echo '<th>' . __('rank', true) . '</th>';
	echo '<th>' . __('username', true) . '</th>';
	echo '<th>' . __('number of contributions', true) . '</th>';
	echo '</tr>';
	
	$i = 1;	
	foreach($stats as $stat){
		$css = 'class=';
		if($stat['User']['group_id'] == 1){
			$css .= '"admin"';
		}
		if($stat['User']['group_id'] == 4){
			$css .= '"normal"';
		}
		
		echo '<tr '.$css.'><td>';
		echo $i; $i++;
		echo '</td><td>';
		echo $html->link($stat['User']['username'], array("controller"=>"users", "action"=>"show", $stat['User']['id']));
		echo '</td><td>';
		echo $stat['0']['total'];
		echo '</td></tr>';
	}
	echo '</table>';
	?>
	<p class="more_link">
		<?php 
		echo $html->link(
			__('Show entire list',true),
			array(
				"controller" => "contributions",
				"action" => "statistics"
			)
		); 
		?>
		</p>
	</div>
</div>

<div id="main_content">
	<div class="module">
		<h2><?=$paginator->counter(array('format' => __('Users (total %count%)', true))); ?></h2>
		
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
					<?php echo $user['Country']['name']; ?>
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


