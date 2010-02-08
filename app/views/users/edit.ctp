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
 * Edit view for Users model.
 *
 * @category Users
 * @package  View
 * @author   BEN YAALA Salem <salem.benyaala@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

?>
<div class="editUser">
<div class="actions">
    <ul>
        <li>
<?php
echo $html->link(
    'Delete',
    array(
        'action' => 'delete',
        $form->value('User.id')
    ),
    null,
    sprintf('Are you sure you want to delete # %s?', $form->value('User.id'))
);
?>
        </li>
        <li>
<?php echo $html->link('List Users', array('action' => 'index')); ?>
        </li>
    </ul>
</div>

<?php echo $form->create('User'); ?>
    <fieldset>
         <legend><?php echo 'Edit User'; ?></legend>
<?php
echo $form->input('id');
echo $form->input('username');
echo $form->input('email');
echo $form->input('lang');
echo $form->input('group_id');
?>
    </fieldset>
<?php echo $form->end('Submit'); ?>
</div>
