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
$username = $form->value('User.username');
?>
<div id="annexe_content">
    <ul class="actions">
        <li class="delete">
        <?php
        echo $html->link(
        __d('admin', 'Delete',true),
        array(
            'action' => 'delete',
            $userId
        ),
        null,
        format(__d('admin', 'Are you sure you want to delete user #{number}?', true),
               array('number' => $userId))
        );
        ?>
        </li>
        <li>
        <?php echo $html->link(__d('admin', 'List Users',true), array('action' => 'index')); ?>
        </li>
        <li>
        <?php echo $html->link(
            __d('admin', 'Profile',true), 
            array(
                'controller' => 'user', 
                'action' => 'profile',
                $username
            )
        ); ?>
        </li>
    </ul>
</div>

<div id="main_content">
<?php
$security->enableCSRFProtection();
echo $form->create('User');
?>
    <fieldset>
    <legend><?php __d('admin', 'Edit User'); ?></legend>
    <?php
    echo $form->input('id',       array('label' => __d('admin', 'Id', true)));
    echo $form->input('username', array('label' => __d('admin', 'Username', true)));
    echo $form->input('email',    array('label' => __d('admin', 'Email', true)));
    echo $form->input('settings.lang',     array('label' => __d('admin', 'Lang', true)));
    echo $form->input('group_id', array('label' => __d('admin', 'Group', true)));
    echo $form->input(
        'level', 
        array(
            'type' => 'radio',
            'label' => __d('admin', 'Level', true),
            'options' => array(
                User::MIN_LEVEL => "-1", 
                User::MAX_LEVEL => "0"
            )
        )
    );
    echo $form->input('send_notifications', array(
        'label' => __d('admin', 'Send notifications', true)
    ));
    ?>
    </fieldset>
<?php
echo $form->end(array('label' => __d('admin', 'Submit', true)));
$security->disableCSRFProtection();
?>
</div>
