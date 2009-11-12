<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  TATOEBA Project(should be changed)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
if  ($session->check('Message.auth')) $session->flash('auth');


echo '<div id="Login">';
	echo $form->create('User', array('action' => 'login'));
	echo $form->input('username', array('label' => __('Username : ',true)));
	echo $form->input('password', array('label' => __('Password : ',true)));
	echo $form->checkbox('rememberMe'); 
	echo '<label for="UserRememberMe">'; __('Remember me'); echo '</label>';
	echo '<br/>';
	echo $form->end(__('Log in',true));

	echo '<div id="PasswordForgotten">';
	echo $html->link(
		__('Password forgotten?',true),
		array(
			"controller" => "users",
			"action" => "new_password"
		));
	echo '</div>';
echo '</div>';


echo '<div id="ClickHereToRegister">';
echo $html->link(
	__('Register',true),
	array(
		"controller" => "users",
		"action" => "register"
	),
	array("class"=>"registerButton")
	);
echo '</div>';

?>
