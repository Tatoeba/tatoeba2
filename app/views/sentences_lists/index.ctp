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
?>

<div id="main_content">
	
<?php
if(isset($myLists)){
	// Form to create a new list
	echo '<div class="module">';
		echo '<h2>';
		__('Create a new list');
		echo '</h2>';
		
		echo $form->create('SentencesList');
		echo $form->input('name');
		echo $form->end('create');
	echo '</div>';
	
	// Lists of the user
	echo $javascript->link('sentences_lists.edit_name.js', false);
	echo $javascript->link('jquery.jeditable.js', false);
	echo '<div class="module">';
		echo '<h2>';
		__('My lists');
		echo '</h2>';
		
		echo '<ul>';
		foreach($myLists as $myList){
			echo '<li>';
			echo '<span id="'.$myList['SentencesList']['id'].'" class="editable editableSentencesListName">';
			echo $myList['SentencesList']['name'];
			echo '</span>';
			echo ', <em>' . $myList['User']['username'] . '</em> ';
			
			echo '(';
			echo $html->link(
				__('show',true), 
				array("controller" => "sentences_lists", "action" => "show", $myList['SentencesList']['id'])
			);
			echo ', ';
			echo $html->link(
				__('delete',true), 
				array("controller" => "sentences_lists", "action" => "delete", $myList['SentencesList']['id']),
				null,
				__('Are you sure?',true)
			);
			echo ')';
			echo '</li>';
		}
		echo '</ul>';
	echo '</div>';
}

// All the lists
echo '<div class="module">';
	echo '<h2>';
	echo __('All lists');
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
?>
	
</div>