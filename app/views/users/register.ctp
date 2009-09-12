<?php
if(isset($error)){
	echo $error;
}
if (isset($user)){
   echo '<div id="UserUsername_error" class="error-message">frefreufrenuigrnreuigrngreuigner</div>';

}

echo $javascript->link('users.check_registration.js', true);


echo $form->create('User', array("action" => "register"));

echo $form->input('username', array(
	"label" => __('Username :',true)
));
echo $form->input('password', array(
	"label" => __('Password :',true)
));
echo $form->input('email', array(
	"label" => __('Email :',true)
));


echo '<div>';
echo $html->image('/users/captcha_image', array("id" => "captcha"));
echo '<a href="javascript:void(0);" onclick="javascript:document.images.captcha.src=\''. $html->url('/users/captcha_image') .'?\' + Math.round(Math.random(0)*1000)+1">Reload image</a>';
echo '</div>';
echo $form->input('captcha', array("label" => __('Code displayed above :',true)));

echo $form->submit(__('Register',true), array("id" => "registerButton"));

echo $form->end();
?>
