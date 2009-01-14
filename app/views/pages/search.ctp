<?php 
echo '<h2>';
__('Search one word : ');
echo $html->link(
	'example',
	array(
		"controller" => "sentences",
		"action" => "search",
		"?query=example"
	));
echo '</h2>';
__('Nothing much to explain here. This will search for sentences containing the word "example".');


echo '<h2>';
__('Using quotes : ');
echo $html->link(
	'"I would like"',
	array(
		"controller" => "sentences",
		"action" => "search",
		"?query=".urlencode("\"I would like\"")
	));
echo '</h2>';
__('This will search for sentences containing "I would like". If you remove the quotes, see below.');


echo '<h2>';
__('Search with OR operator : ');	
echo $html->link(
	'I would like',
	array(
		"controller" => "sentences",
		"action" => "search",
		"?query=I would like"
	));
echo '</h2>';
__('This will search for sentences containing "would" or "like". ');
__('Words with <strong>3 characters or less are ignored</strong> if they are not inside of quotes, which is why "I" is not taken into account here.');

echo '<h2>';
__('Search with AND operator : ');
echo $html->link(
	'+here +there',
	array(
		"controller" => "sentences",
		"action" => "search",
		"?query=".urlencode("+here +there")
	));
echo '</h2>';	
__('This will search for sentences containing "here" and "there".');
	
echo '<h2>';
__('Search with NOT operator : ');	
echo $html->link(
	'here -there',
	array(
		"controller" => "sentences",
		"action" => "search",
		"?query=".urlencode("here -there")
	));
echo '</h2>';
__('This will search for sentences containing "here" but not "there".');

echo '<h2>';
__('Grouping : ');
echo $html->link(
	'+like +(television sleep)',
	array(
		"controller" => "sentences",
		"action" => "search",
		"?query=".urlencode("+like +(television sleep)")
	));
echo '</h2>';
__('This will search for sentences containing "like" and "music", or "like" and "sleep".');

?>

