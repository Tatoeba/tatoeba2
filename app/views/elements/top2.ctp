<div id="top2">
	<div><strong>TATOEBA</strong> Project</div>
	
	<div>
	<?php 
	if($session->read('Auth.User.id')){
		// Welcome message
		__('Welcome');
		echo ' '.$session->read('Auth.User.username'); 
		echo ' (group ' . $session->read('Auth.User.group_id') . ')';
	}else{
		echo $html->link(
			__('Log in',true),
			array(
				"controller" => "users",
				"action" => "login"
			));
		echo ' ';
		echo $html->link(
			__('Register',true),
			array(
				"controller" => "users",
				"action" => "register"
			));
	}
	?>
	</div>
</div>