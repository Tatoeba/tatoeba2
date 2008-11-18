<h1><?php __('Registration') ?></h1>
<?php
if($error){
	echo $error;
}
echo $form->create('User', array('action' => 'register'));
echo $form->input('username');
echo $form->input('password');
echo $form->input('password_confirm', array('type' => 'password'));
echo $form->input('email');
echo $form->submit();
echo $form->end();
?>