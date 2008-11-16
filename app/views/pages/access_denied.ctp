<h1><?php __('You are now allowed to view this page. Please log in first'); ?></h1>
<?php
echo $html->link(
	__('Log in',true),
	array(
		"controller" => "users",
		"action" => "login"
	));
?>