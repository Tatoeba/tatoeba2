<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

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

<div id="annexe_content">
	
	<?php
	if($session->read('Auth.User.id')){	
	?>
		<div class="module">
		<h2><?php __('Create a new list'); ?></h2>
		<?php
			echo $form->create('SentencesList');
			echo $form->input('name');
			echo $form->end('create');
		?>
		</div>
		
		<div class="module">
		<h2><?php __('Still beta'); ?></h2>
		<p><?php __('This feature is still very basic. We will improve it as we get more time and some feedbacks from users.'); ?></p>
		<p class="more_link"><?php echo $html->link(__('Feedback',true), array("controller"=>"pages", "action"=>"contact")); ?></p>
		</div>
		
	<?php
	}else{
	?>
		<div class="module">
			<h2><?php __('About the lists'); ?></h2>
			<p><?php __('Lists makes it possible to gather and organize sentences in Tatoeba.'); ?></p>
			<p><?php __('You can create all kinds of lists! "Preparation for summer trip to Mexico", "English test nº4", "My favorite geek quotes"...'); ?></p>
		</div>
		
		<div class="module">
			<h2><?php __('Registration needed'); ?></h2>
			<p><?php __('You can create lists only if you are registered.'); ?></p>

			<p><?php 
			echo $html->link(
				__('Register',true),
				array("controller" => "users", "action" => "register"),
				array("class" => "registerButton")
			);
			?></p>		
			
			<p><?php __('If you are already registered, please log in.'); ?></p>
		</div>
	<?php
	}
	?>
	
	
	<?php
	if(count($myLists) > 0){	
	?>
		<div class="module">
		<h2><?php __('Tips'); ?></h2>
		<?php __('Click on the name of your list if you would like to change the name. You will be able to edit it in place.'); ?>
		</div>
	<?php
	}
	?>
</div>

<div id="main_content">
	
<?php
if(isset($myLists)){

	// Lists of the user
	echo '<div class="module">';
		echo '<h2>';
		__('My lists');
		echo '</h2>';
		
		if(count($myLists) > 0){		
			$javascript->link('sentences_lists.edit_name.js', false);
			$javascript->link('jquery.jeditable.js', false);
			
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