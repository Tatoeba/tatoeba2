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
 * Page to login.
 *
 * @category Users
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

$title = __('Log in');
$this->set('title_for_layout', Sanitize::html($this->Pages->formatTitle($title)));

if  ($this->Session->check('Message.auth')) $this->Session->flash('auth');

$formTarget = array('controller' => 'users', 'action' => 'check_login');

if (isset($this->request->query['redirectTo'])) {
    $formTarget['?'] = array('redirectTo' => $this->request->query['redirectTo']);
}

$passwordUrl = $this->Html->url(
    array(
        "controller" => "users",
        "action" => "new_password"
    )
);

$registerUrl = $this->Html->url(
    array(
        "controller" => "users",
        "action" => "register"
    )
);

$this->Security->enableCSRFProtection();
echo $this->Form->create(
    'User',
    array(
        'url' => $formTarget,
        'id' => "UserLoginForm"
    )
);

?>
<div md-whiteframe="1" id="login-form">
    <h2><? echo __('Log in'); ?></h2>
    <md-input-container class="md-block">
        <?php
        echo $this->Form->input(
            'username', array(
                'label' => __('Username'),
            )
        );
        ?>
    </md-input-container>

    <md-input-container class="md-block">
        <?php
        echo $this->Form->input(
            'password', array(
                'label' => __('Password'),
            )
        );
        ?>
    </md-input-container>

    <md-checkbox
        ng-model='rememberLogin'
        ng-false-value='0'
        ng-true-value='1' ng-init='rememberLogin = 0'
        class='md-primary'>
        <label><? echo __('Remember me') ?></label>
    </md-checkbox>
    <?php
    echo $this->Form->checkbox(
        'rememberMe',
        array(
        'value' => '{{rememberLogin}}',
        'ng-checked' => 'rememberLogin == 1',
        'class' => 'ng-hide'
        )
    );
    ?>

    <div layout="column">
        <md-button type="submit" class="md-raised md-primary">
            <?php echo __('Log in'); ?>
        </md-button>

        <md-button class="md-primary" href="<?= $passwordUrl ?>" flex>
            <? echo __('Forgot your password?'); ?>
        </md-button>

        <md-button href="<?= $registerUrl; ?>">
            <?php echo __('Register'); ?>
        </md-button>
    </div>
</div>
<?php
echo $this->Form->end();
$this->Security->disableCSRFProtection();
?>
