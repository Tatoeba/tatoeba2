<?php
if(isset($error)){
	echo $error;
}
if (isset($user)){
   echo '<div id="UserUsername_error" class="error-message">frefreufrenuigrnreuigrngreuigner</div>';

}
echo $javascript->link('jquery.js', true);
echo $javascript->link('register.check_form.js', true);


echo $form->create('User', array("action" => "register"));
echo $form->input('username', array("label" => __('Username :',true),"class" => "validated" ));
echo '<div id="UserUsername_error" class="error-message"></div>';
echo '<div id="UserUsername_ok" class="ok-message"></div>';

echo $form->input('password', array("label" => __('Password :',true),"class" => "validated" ));
echo '<div id="UserPassword_error" class="error-message"></div>';
echo '<div id="UserPassword_ok" class="ok-message"></div>';

echo $form->input('email', array("label" => __('Email :',true),"class" => "validated" ));
echo '<div id="UserEmail_error" class="error-message"></div>';
echo '<div id="UserEmail_ok" class="ok-message"></div>';

echo $html->image('/users/captcha_image', array("id" => "captcha"));
echo '<a href="javascript:void(0);" onclick="javascript:document.images.captcha.src=\''. $html->url('/users/captcha_image') .'?\' + Math.round(Math.random(0)*1000)+1">Reload image</a>';
echo $form->input('captcha', array("label" => __('Code displayed above :',true)));
echo $form->end(__('Register',true), array("id" => "registerButton"));
?>
