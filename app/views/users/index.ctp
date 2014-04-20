<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  BEN YAALA Salem <salem.benyaala@gmail.com>
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
 * @author   BEN YAALA Salem <salem.benyaala@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * index view for Users.
 *
 * @category Users
 * @package  View
 * @author   BEN YAALA Salem <salem.benyaala@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

?>
<h2>
<?php
echo $paginator->counter(array('format' => __('Users',true). ' (' . __('total',true). ': %count%)'));
?>
</h2>

<div class="paging">
<?php
echo $paginator->prev(
    '<< ' . __('previous', true),
    array(),
    null,
    array('class' => 'disabled')
);
?>
<?php echo $paginator->numbers(array('separator' => '')); ?>
<?php
echo $paginator->next(
    __('next', true) . ' >>',
    array(),
    null,
    array('class' => 'disabled')
);
?>
</div>

<!-- In CakePHP 1.2, the fields are in the order "title, key". In 2.x, they're in the opposite order. -->
<table class="users">
<tr>
    <th><?php echo $paginator->sort(__('ID',true),'id'); ?></th>
    <th><?php echo $paginator->sort(__('Username',true),'username'); ?></th>
    <th><?php echo $paginator->sort(__('Email',true),'email'); ?></th>
    <th><?php echo $paginator->sort(__('Since',true),'since'); ?></th>
    <th><?php echo $paginator->sort(__('Last Time Active',true),'last_name_active'); ?></th>
    <th><?php echo $paginator->sort(__('Group',true),'group'); ?></th>
    <th class="actions"></th>
</tr>
<?php
$i = 0;
foreach ($users as $user) {
    $class = null;
    
    if ($i++ % 2 == 0) {
        $class = ' class="altrow"';
    }
    ?>
    <tr<?php echo $class; ?>>
        <td>
            <?php echo $user['User']['id']; ?>
        </td>
        <td>
            <?php 
            echo $html->link(
                $user['User']['username'], 
                array(
                    'action'=>'edit', 
                    $user['User']['id']
                )
            ); 
            ?>
        </td>
        <td>
            <?php echo $user['User']['email']; ?>
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
    <?php
}
?>
</table>


<div class="paging">
<?php
echo $paginator->prev(
    '<< ' . __('previous', true),
    array(),
    null,
    array('class' => 'disabled')
);
?>
<?php echo $paginator->numbers(array('separator' => '')); ?>
<?php
echo $paginator->next(
    __('next', true) . ' >>',
    array(),
    null,
    array('class' => 'disabled')
);
?>
</div>
