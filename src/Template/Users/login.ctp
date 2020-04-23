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
 * @link     https://tatoeba.org
 */

/**
 * Page to login.
 *
 * @category Users
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

use \Cake\Controller\Component\AuthComponent;

$title = __('Log in');
$this->set('title_for_layout', h($this->Pages->formatTitle($title)));

if  ($this->request->getSession()->check('Message.auth')) $this->Flash->render('auth');

$formTarget = array('controller' => 'users', 'action' => 'check_login');

$redirect = $this->request->getQuery(AuthComponent::QUERY_STRING_REDIRECT);
if (!is_null($redirect)) {
    $formTarget['?'] = [AuthComponent::QUERY_STRING_REDIRECT => $redirect];
}

$passwordUrl = $this->Url->build(
    array(
        "controller" => "users",
        "action" => "new_password"
    )
);

$registerUrl = $this->Url->build(
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
<div id="login-form" class="md-whiteframe-1dp">
  <h2><?= __('Log in'); ?></h2>
  <div ng-cloak>
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
        <?= __('Remember me') ?>
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
            <?php /* @translators: button to submit login form (verb) */ ?>
            <?php echo __('Log in'); ?>
        </md-button>

        <md-button class="md-primary" href="<?= $passwordUrl ?>" flex>
            <?= __('Forgot your password?'); ?>
        </md-button>

        <md-button href="<?= $registerUrl; ?>">
            <?php /* @translators: link to the Register page in the login page (verb) */ ?>
            <?php echo __('Register'); ?>
        </md-button>
    </div>
  </div>
</div>
<?php
echo $this->Form->end();
$this->Security->disableCSRFProtection();
?>
