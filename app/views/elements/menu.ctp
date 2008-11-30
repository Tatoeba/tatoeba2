<div id="menu">
<h1>Menu</h1>

<?php 
print_r($session->read('Acl'));

echo '<ul>';

// Home
echo '<li>';
echo $html->link(__('Home',true), '/');
echo '</li>';

// Show sentences
echo '<li>';
echo $html->link(
	__('Show sentences',true),
	array(
		"controller" => "sentences"
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

// Random sentence
echo '<li>';
echo $html->link(
	__('Random sentence',true),
	array(
		"controller" => "sentences",
		"action" => "show",
		"random"
	));
echo '</li>';

// Latest activities
echo '<li>';
echo $html->link(
	__('Latest activities',true),
	array(
		"controller" => "latest_activities"
	));
echo '</li>';

// Search
echo '<li>';
echo $html->link(
	__('Search',true),
	array(
		"controller" => "sentences",
		"action" => "search"
	));
echo '</li>';

// Documentation : still needs to be done
echo '<li>';
echo 'Documentation';
echo '</li>';

// Discussions
echo '<li>';
echo $html->link(
	__('Comments on sentences',true),
	array(
		"controller" => "sentence_comments"
	));
echo '</li>';

echo '</ul>';
?>
</div>