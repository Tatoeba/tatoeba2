<?php 
echo '<h2>';
__('Search one word : ');
echo $html->link(
	__('example',true),
	array(
		"controller" => "sentences",
		"action" => "search",
		"example"
	));
echo '</h2>';
__('Nothing much to explain here. This will search for sentences containing the word "example".');


echo '<h2>';
__('Using quotes : ');
echo $html->link(
	__('"I would like"',true),
	array(
		"controller" => "sentences",
		"action" => "search",
		urlencode("\"I would like\"")
	));
echo '</h2>';
__('This will search for sentences containing "I would like". If you remove the quotes, see below.');


echo '<h2>';
__('Search with OR operator : ');	
echo $html->link(
	__('I would like',true),
	array(
		"controller" => "sentences",
		"action" => "search",
		"I would like"
	));
echo '</h2>';
__('This will search for sentences containing "I" or "would" or "like".');


echo '<h2>';
__('Search with AND operator : ');
echo $html->link(
	__('+here +there',true),
	array(
		"controller" => "sentences",
		"action" => "search",
		urlencode("+here +there")
	));
echo '</h2>';	
__('This will search for sentences containing "here" and "there".');
	
echo '<h2>';
__('Search with NOT operator : ');	
echo $html->link(
	__('here -there',true),
	array(
		"controller" => "sentences",
		"action" => "search",
		urlencode("here -there")
	));
echo '</h2>';
__('This will search for sentences containing "here" but not "there".');

echo '<h2>';
__('Grouping : ');
echo $html->link(
	__('+like +(music sleep)',true),
	array(
		"controller" => "sentences",
		"action" => "search",
		"+like (music sleep)"
	));
echo '</h2>';
__('This will search for sentences containing "like" and "music", or "like" and "sleep".');

?>

