<?php
if  ($session->check('Message.auth')) $session->flash('auth');

echo '<div id="ClickHereToRegister">';
echo $html->link(
	__('Click here to register',true),
	array(
		"controller" => "users",
		"action" => "register"
	));
echo '</div>';

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

?>