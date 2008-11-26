<div id="top1">
	<div>Visitor(s) : xx</div>
	
	<div>Language : English | Français | Español | 日本語 | Deutsch | Italiano |</div>
	
	<div>
	<?php
	// Log out link
	if($session->read('Auth.User.id')){
		echo $html->link(
			__('Log out',true),
			array(
				"controller" => "users",
				"action" => "logout"
			)
		);
	}
	?>
	</div>
</div>