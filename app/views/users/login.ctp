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
 
if  ($session->check('Message.auth')) $session->flash('auth');

$formTarget = array('action' => 'check_login');
if (isset($this->params['url']['redirectTo'])) {
    $formTarget['?'] = array('redirectTo' => $this->params['url']['redirectTo']);
}
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

<?php
echo $form->end(__('Log in',true));
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


<div id="ClickHereToRegister">
<?php
echo $html->link(
    __('Register',true),
    array(
        "controller" => "users",
        "action" => "register"
    ),
    array("class"=>"registerButton")
    );
?>
</div>
