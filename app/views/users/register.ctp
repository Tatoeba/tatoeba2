<?php
if(isset($error)){
	echo $error;
}
echo $form->create('User', array("action" => "register"));
echo $form->input('username', array("label" => __('Username :',true)));
echo $form->input('password', array("label" => __('Password :',true)));
echo $form->input('email', array("label" => __('Email :',true)));
echo $html->image('/users/captcha_image');
echo $form->input('captcha', array("label" => __('Code displayed above :',true)));
echo $form->end(__('Register',true));
?>