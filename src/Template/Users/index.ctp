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
echo $this->Paginator->counter(__d('admin', 'Users'). ' (' . __d('admin', 'total'). ': {{count}})');
?>
</h2>

<div class="paging">
  <?php $this->Pagination->display(); ?>
</div>

<table class="users">
<tr>
    <th><?php echo $this->Paginator->sort('id', __d('admin', 'ID')); ?></th>
    <th><?php echo $this->Paginator->sort('username', __d('admin', 'Username')); ?></th>
    <th><?php echo $this->Paginator->sort('email', __d('admin', 'Email')); ?></th>
    <th><?php echo $this->Paginator->sort('since', __d('admin', 'Since')); ?></th>
    <th><?php echo $this->Paginator->sort('level', __d('admin', 'Level')); ?></th>
    <th><?php echo $this->Paginator->sort('group_id', __d('admin', 'Group')); ?></th>
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
    <tr <?= $class; ?>>
        <td>
            <?php echo $user->id; ?>
        </td>
        <td>
            <?php
            echo $this->Html->link(
                $user->username,
                array(
                    'action'=>'edit',
                    $user->id
                )
            );
            ?>
        </td>
        <td>
            <?php echo $user->email; ?>
        </td>
        <td>
            <?php echo $this->Date->ago($user->since); ?>
        </td>
        <td>
            <?php echo $user->level; ?>
        </td>
        <td>
            <?php echo $user->name; ?>
        </td>
    </tr>
    <?php
}
?>
</table>


<div class="paging">
  <?php $this->Pagination->display(); ?>
</div>
