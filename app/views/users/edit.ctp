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

$userId = $form->value('User.id');
?>
<div class="editUser">
<div class="actions">
    <ul>
        <li class="delete">
        <?php
        echo $html->link(
        __('Delete',true),
        array(
            'action' => 'delete',
            $userId
        ),
        null,
        sprintf(__('Are you sure you want to delete user #%s?',true), $userId)
        );
        ?>
        </li>
        <li>
        <?php echo $html->link(__('List Users',true), array('action' => 'index')); ?>
        </li>
    </ul>
</div>

<?php 
// HACK / quick fix
echo '<form id="UserEditForm" method="post" action="/'.$this->params['lang'].'/users/edit/'.$userId.'">';
// Because...
//   echo $form->create('User'); 
// will echo this:
//   <form id="UserEditForm" method="post" action="/users/edit/218/lang:eng">
// And that doesn't work because the URL doesn't start with a language.
// So until someone figures out what's wrong, I'm just hardcoding the HTML...

$form->create('User'); // But we still need to call $form->create() 
                       // to retrieve the user data...
?>
    <fieldset>
    <legend><?php __('Edit User'); ?></legend>
    <?php
    echo $form->input('id');
    echo $form->input('username');
    echo $form->input('email');
    echo $form->input('lang');
    echo $form->input('group_id');
    echo $form->input('level', 
                      array('type' => 'radio', 
                            'options' => range(User::MIN_LEVEL, User::MAX_LEVEL)));
    echo $form->input('send_notifications');
    ?>
    </fieldset>
<?php echo $form->end('Submit'); ?>
</div>
