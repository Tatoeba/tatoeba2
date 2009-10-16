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



echo $html->image('/users/captcha_image', array("id" => "captcha"));
echo '<a href="javascript:void(0);" onclick="javascript:document.images.captcha.src=\''. $html->url('/users/captcha_image') .'?\' + Math.round(Math.random(0)*1000)+1">Reload image</a>';

echo $form->input('captcha', array("label" => __('Code displayed above :',true)));

echo $form->submit(__('Register',true), array("id" => "registerButton"));

echo $form->end();
?>
