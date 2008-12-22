<div id="top2">
	<div class="tatoeba_title"><strong>TATOEBA</strong> <em>Project</em></div>
	
	<div id="UserMenu">
	<?php 
	if($session->read('Auth.User.id')){
		// Welcome message
		__('Welcome');
		echo ' '.$session->read('Auth.User.username'); 
		echo ' (group ' . $session->read('Auth.User.group_id') . ')';
	}else{
		echo '<span>';
		echo $html->link(
			__('Log in',true),
			array(
				"controller" => "users",
				"action" => "login"
			));
		echo '</span>';
		
		echo '<span>';
		echo $html->link(
			__('Register',true),
			array(
				"controller" => "users",
				"action" => "register"
			));
		echo '</span>';
	}
	?>
	</div>
</div>