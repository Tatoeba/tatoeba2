<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Page that lists all the members.
 *
 * @category Users
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */ 

$this->pageTitle = 'Tatoeba - ' . __('Members', true);

$id = ($session->read('last_user_id') > 0) ? $session->read('last_user_id') : 1;
$navigation->displayUsersNavigation($id);
?>

<div id="annexe_content">
	<div class="module">
	<h2><?php __('Top members') ?></h2>
	<?php
	
    echo '<table id="topMembers">';
	echo '<tr>';
	echo '<th>' . __('rank', true) . '</th>';
	echo '<th>' . __('username', true) . '</th>';
	echo '<th>' . __('number of contributions', true) . '</th>';
	echo '</tr>';

	foreach($topContributors as $i=>$topContributor){
		$css = 'class=';
		if($topContributor['group_id'] == 1){
			$css .= '"admin"';
		} elseif ($topContributor['group_id'] == 4){
			$css .= '"normal"';
		}

		echo '<tr '.$css.'><td>';
		echo $i +1 ;
		echo '</td><td>';
		echo $html->link($topContributor['userName'],
            array("controller"=>"user",
                 "action"=>"profile",
                  $topContributor['userName']
            )
        );
		echo '</td><td>';
		echo $topContributor['numberOfContributions'];
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
		<h2><?=$paginator->counter(array('format' => __('Members (total %count%)', true))); ?></h2>

		<div class="paging">
		<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
		<?php echo $paginator->numbers(array('separator' => ''));?>
		<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
		</div>
		<table class="users">
		<tr>
			<th></th>
			<th><?php echo $paginator->sort(__('Username', true), 'username');?></th>
			<th><?php echo $paginator->sort(__('Member since', true),'since');?></th>
			<th><?php echo $paginator->sort(__('Last login', true),'last_time_active');?></th>
			<th><?php echo $paginator->sort(__('Member status', true),'group_id');?></th>
		</tr>
		<?php
		foreach ($users as $i=>$user):
			$class = '';
			if (($i % 2) == 0) {
				$class = ' class="altrow"';
			}
		?>
			<tr>
				<td>
					<?php 
					$image = (empty($user['User']['image'])) ? 'unknown-avatar.jpg' : $user['User']['image'];
					echo $html->link(
						$html->image(
							'profiles_36/'.$image,
							array("alt"=>$user['User']['username'])
						)
						, array("controller"=>'user', "action"=>'profile', $user['User']['username'])
						, array("escape"=>false)
					); 
					?>
				</td>
				<td>
					<?php
					echo $html->link(
						$user['User']['username']
						, array("controller"=>'user', "action"=>'profile', $user['User']['username'])
					); 
					?>
				</td>
				<td>
					<?php echo $date->ago($user['User']['since']); ?>
				</td>
				<td>
					<?php echo $date->ago($user['User']['last_time_active'], true); ?>
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


