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

$title = __('Log in', true);
$this->set('title_for_layout', Sanitize::html($pages->formatTitle($title)));

if  ($session->check('Message.auth')) $session->flash('auth');

$formTarget = array('action' => 'check_login');
if (isset($this->params['url']['redirectTo'])) {
    $formTarget['?'] = array('redirectTo' => $this->params['url']['redirectTo']);
}
$security->enableCSRFProtection();
echo $form->create(
    'User',
    array(
        'url' => $formTarget,
        'id' => "UserLoginForm"
    )
    );
echo $form->input(
    'username', array(
        'label' => __('Username: ',true),
    )
);
echo $form->input(
    'password', array(
        'label' => __('Password: ',true),
    )
);
?>

<div>
<?php
echo $form->checkbox(
    'rememberMe'
); 
echo '<label for="UserRememberMe">'; __('Remember me'); echo '</label>';
?>
</div>

<md-button type="submit" class="md-raised">
    <?php __('Log in'); ?>
</md-button>

<?php
echo $form->end();
$security->disableCSRFProtection();
?>


<div id="PasswordForgotten">
<?php
echo $html->link(
    __('Forgot your password?',true),
    array(
        "controller" => "users",
        "action" => "new_password"
    )
);
?>
</div>


<div layout="row" layout-align="center center">
    <?php
    $registerUrl = $html->url(
        array(
            "controller" => "users",
            "action" => "register"
        )
    );
    ?>
    <md-button class="md-raised md-primary" href="<?= $registerUrl; ?>">
        <?php __('Register'); ?>
    </md-button>
</div>

