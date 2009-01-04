<?php
if(isset($error)){
	echo $error;
}
echo $form->create('User', array("action" => "register"));
echo $form->input('username', array("label" => __('Username :',true)));
echo $form->input('password', array("label" => __('Password :',true)));
echo $form->input('password_confirm', array("label" => __('Confirm password :',true), "type" => "password"));
echo $form->input('email', array("label" => __('Email :',true)));
echo $form->end(__('Register',true));
?>