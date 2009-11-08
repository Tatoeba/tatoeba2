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
	$javascript->link('sentences_lists.edit_name.js', false);
	$javascript->link('jquery.jeditable.js', false);

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

	echo '<div class="module">';
		echo '<h2>';
		__('My lists');
		echo '</h2>';
		
		if(count($myLists) > 0){		
			echo '<div class="tips">';
			__('You can change the name of the list by clicking on it.');
			echo '</div>';
			
			echo '<ul class="sentencesLists">';
			foreach($myLists as $myList){
				echo '<li>';			
				echo '<span id="'.$myList['SentencesList']['id'].'" class="listName editable editableSentencesListName">';
				echo $myList['SentencesList']['name'];
				echo '</span>';
				echo ', <span class="username">' . $myList['User']['username'] . '</span> ';
				
				echo '[ ';
				echo $html->link(
					__('edit',true), 
					array("controller" => "sentences_lists", "action" => "edit", $myList['SentencesList']['id'])
				);
				echo ', ';
				echo $html->link(
					__('delete',true), 
					array("controller" => "sentences_lists", "action" => "delete", $myList['SentencesList']['id']),
					null,
					__('Are you sure?',true)
				);
				echo ' ] ';				
				echo '</li>';
			}
			echo '</ul>';
		}else{
			__('You don\'t have any lists yet.');
		}
	echo '</div>';

}

// All the lists
echo '<div class="module">';
	echo '<h2>';
	echo __('All lists');
	echo '</h2>';
	
	echo '<ul class="sentencesLists">';
	foreach($lists as $list){
		echo '<li>';			
			echo '<span id="'.$list['SentencesList']['id'].'" class="listName">';
			echo $list['SentencesList']['name'];
			echo '</span>';
			echo ', <span class="username">' . $list['User']['username'] . '</span> ';
			
			echo '[ ';
			echo $html->link(
				__('show',true), 
				array("controller" => "sentences_lists", "action" => "show", $list['SentencesList']['id'])
			);
			echo ' ] ';

		echo '</li>';
	}
	echo '</ul>';
echo '</div>';
?>
	
</div>