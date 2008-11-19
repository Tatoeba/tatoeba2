<h1>Menu</h1>

<?php 
print_r($session->read('Acl'));

echo '<ul>';

// Home
echo '<li>';
echo $html->link(__('Home',true), 
	array(
		"controller" => "pages",
		"action" => "display",
		"home"
		));
echo '</li>';

// Show sentences
echo '<li>';
echo $html->link(
	__('Show sentences',true),
	array(
		"controller" => "sentences",
		"action" => "index"
	));
echo '</li>';

// Add a sentence
echo '<li>';
echo $html->link(
	__('Add a sentence',true),
	array(
		"controller" => "sentences",
		"action" => "add"
	));
echo '</li>';

// Latest activities
echo '<li>';
echo $html->link(
	__('Latest activities',true),
	array(
		"controller" => "latest_activities",
		"action" => "index"
	));
echo '</li>';

echo '</ul>';
?>