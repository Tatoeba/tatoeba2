<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang (tranglich@gmail.com)

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


echo '<div id="main_content">';

echo '<div class="module">';
echo '<h2>';
__('Create a new list');
echo '</h2>';

echo $form->create('SentencesList');
echo $form->input('name');
echo $form->end('create');
echo '</div>';


echo '<div class="module">';
echo '<h2>';
__('Lists');
echo '</h2>';


echo '<ul>';
foreach($lists as $list){
	echo '<li>';
	echo $html->link(
		$list['SentencesList']['name'], 
		array("controller" => "sentences_lists", "action" => "show", $list['SentencesList']['id'])
	);
	echo ', <em>' . $list['User']['username'] . '</em>';
	echo '</li>';
}
echo '</ul>';
echo '</div>';

echo '</div>';
?>
