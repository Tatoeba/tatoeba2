<?php
if (isset($this->params['lang'])) { 
	Configure::write('Config.language',  $this->params['lang']); 
}
?>

<div id="top2">
	<a href="/" class="tatoeba_title"><strong>TATOEBA</strong> <em>Project</em></a>
	
	<cake:nocache>
	<div id="UserMenu">
	<?php 
	if($session->read('Auth.User.id')){
		// Welcome message
		echo '<div>';
		__('Welcome');
		echo ' '.$session->read('Auth.User.username'); 
		echo '</div>';
		
		echo '<div>';
		echo $html->link(
			__('See my page',true),
			array("controller" => "users", "action" => "show/".$session->read('Auth.User.id' ))
		);
		echo '</div>';
		
		echo '<div>';
		echo $html->link(
			__('Edit my information',true),
			array("controller" => "users", "action" => "my_profile")
		);
		echo '</div>';
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
	</cake:nocache>
</div>
