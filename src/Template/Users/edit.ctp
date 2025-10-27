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
 * @link     https://tatoeba.org
 */
use App\Model\Entity\User;

/**
 * Edit view for Users model.
 *
 * @category Users
 * @package  View
 * @author   BEN YAALA Salem <salem.benyaala@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

$userId = $user->id;
$username = $user->username;
?>
<div id="annexe_content">
    <ul class="actions">
        <li class="delete">
        <?php
        echo $this->Html->link(
            __d('admin', 'Delete'),
            [
                'action' => 'delete',
                $userId
            ],
            [
                'confirm' => format(
                    __d('admin', 'Are you sure you want to delete user #{number}?'),
                    ['number' => $userId]
                )
            ]
        );
        ?>
        </li>
        <li>
        <?php echo $this->Html->link(__d('admin', 'List Users'), array('action' => 'index')); ?>
        </li>
        <li>
        <?php echo $this->Html->link(
            __d('admin', 'Profile'), 
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
$this->Security->enableCSRFProtection();
echo $this->Form->create($user, array('id' => 'UserEditForm'));
?>
    <fieldset>
    <legend><?php echo __d('admin', 'Edit User'); ?></legend>
    <?php
    echo $this->Form->input('id',       array('label' => __d('admin', 'Id')));
    echo $this->Form->input('username', array('label' => __d('admin', 'Username')));
    echo $this->Form->input('settings.lang',     array('label' => __d('admin', 'Lang')));
    echo $this->Form->input('role', array(
        'options' => array_combine($groups, $groups),
        'label' => __d('admin', 'Group'),
    ));
    echo $this->Form->input('is_spamdexing', array(
        'label' => __d('admin', 'Spamdexing status'),
        'options' => array(
            '1' => __d('admin', 'Unknown (links restricted)'),
            '0' => __d('admin', 'Verified (links not restricted)'),
            ''  => __d('admin', 'Legacy (links not restricted)'),
        ),
        'disabled' => is_null($user->is_spamdexing) ? [] : [''],
        'val' => is_null($user->is_spamdexing) ? '' : (int)$user->is_spamdexing,
    ));
    echo $this->Form->input(
        'level', 
        array(
            'type' => 'radio',
            'label' => __d('admin', 'Level'),
            'options' => array(
                User::MIN_LEVEL => "-1", 
                User::MAX_LEVEL => "0"
            )
        )
    );
    ?>

    <br>
    <details>
        <summary style="cursor:pointer"><?= __d('admin', 'About spamdexing status') ?></summary>
        <ul>
            <li><?= __d('admin', '"Unknown" users cannot include any outbound link in their profile. In addition, when trying to post a sentence comment or wall post that includes outbound links, they are warned that their links must be legitimate. If they choose to continue, the message is posted and an e-mail is sent to moderators.') ?></li>
            <li><?= __d('admin', '"Verified" and "legacy" users can include any link anywhere without restriction.') ?></li>
            <li><?= __d('admin', '"Legacy" users created their account before this restriction was implemented.') ?></li>
        </ul>
    </details>

<?php
    echo '<br>';
    echo $this->Form->input('send_notifications', array(
        'label' => __d('admin', 'Send notifications')
    ));

    echo $this->Form->input('settings.can_switch_license', [
        'type' => 'checkbox',
        'label' => __('Can switch license')
    ]);
    ?>
    </fieldset>
<?php
echo $this->Form->submit(__d('admin', 'Submit'));
echo $this->Form->end();
$this->Security->disableCSRFProtection();
?>
</div>
