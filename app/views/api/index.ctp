<?php
if(!$loggedin){
	echo $form->create('User', array('url' => array('controller' => 'api', 'action' => 'login')));
	echo $form->input('username');
	echo $form->input('password');
	echo $form->end('Login');
}else{
	echo $form->create('User', array('url' => array('controller' => 'api', 'action' => 'logout')));
	echo $form->end('Logout');
}
?>