<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  TATOEBA Project(should be changed)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
echo '<h1>';
__('Examples of search');
echo '</h1>';

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
__('This will search for sentences containing the word "example".');
echo '<br/>';
__('Note that if you specify the language, the search will not always be an exact search. More concretely, if you are searching "thinking", and do NOT specify any language, it will return sentences with EXACTLY the word "think". But if you specify that the language is English, it will also return sentences with "thinks" and "thinking".');


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
__('This will search for sentences containing "like" and "television", or "like" and "sleep".');
?>

