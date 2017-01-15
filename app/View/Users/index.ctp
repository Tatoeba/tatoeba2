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
echo $this->Paginator->counter(array('format' => __d('admin', 'Users'). ' (' . __d('admin', 'total'). ': %count%)'));
?>
</h2>

<div class="paging">
<?php
echo $this->Paginator->prev(
    '<< ' . __d('admin', 'previous'),
    array(),
    null,
    array('class' => 'disabled')
);
?>
<?php echo $this->Paginator->numbers(array('separator' => '')); ?>
<?php
echo $this->Paginator->next(
    __d('admin', 'next') . ' >>',
    array(),
    null,
    array('class' => 'disabled')
);
?>
</div>

<!-- In CakePHP 1.2, the fields are in the order "title, key". In 2.x, they're in the opposite order. -->
<table class="users">
<tr>
    <th><?php echo $this->Paginator->sort(__d('admin', 'ID'),'id'); ?></th>
    <th><?php echo $this->Paginator->sort(__d('admin', 'Username'),'username'); ?></th>
    <th><?php echo $this->Paginator->sort(__d('admin', 'Email'),'email'); ?></th>
    <th><?php echo $this->Paginator->sort(__d('admin', 'Since'),'since'); ?></th>
    <th><?php echo $this->Paginator->sort(__d('admin', 'Level'),'level'); ?></th>
    <th><?php echo $this->Paginator->sort(__d('admin', 'Group'),'group_id'); ?></th>
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
            echo $this->Html->link(
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
            <?php echo $this->Date->ago($user['User']['since']); ?>
        </td>
        <td>
            <?php echo $user['User']['level']; ?>
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
echo $this->Paginator->prev(
    '<< ' . __d('admin', 'previous'),
    array(),
    null,
    array('class' => 'disabled')
);
?>
<?php echo $this->Paginator->numbers(array('separator' => '')); ?>
<?php
echo $this->Paginator->next(
    __d('admin', 'next') . ' >>',
    array(),
    null,
    array('class' => 'disabled')
);
?>
</div>
