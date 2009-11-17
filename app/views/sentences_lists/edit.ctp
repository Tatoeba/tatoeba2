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

$javascript->link('sentences_lists.remove_sentence_from_list.js', false);
$javascript->link('sentences_lists.edit_name.js', false);
$javascript->link('sentences_lists.add_new_sentence_to_list.js', false);
$javascript->link('jquery.jeditable.js', false);
?>


<div id="annexe_content">
	<div class="module">
	<h2><?php __('Actions'); ?></h2>
	<ul>
		<li>
			<?php 
			echo $html->link(
				__('Back to all the lists',true)
				, array("controller"=>"sentences_lists", "action"=>"index")
			)
			?>
		</li>
		<li class="deleteList">
			<?php
			echo $html->link(
				__('Delete this list', true)
				, array("controller" => "sentences_lists", "action" => "delete", $list['SentencesList']['id'])
				, null
				, __('Are you sure?', true)
			);
			?>
		</li>
	</ul>
	</div>
	
	<div class="module">
	<h2><?php __('Tips'); ?></h2>
	<ul>
		<li><?php __('You can change the name of the list by clicking on it.'); ?></li>
		<li><?php __('You can remove a sentence from the list by clicking on the X icon.'); ?></li>
	</ul>
	</div>
</div>



<div id="main_content">
	<div class="module">
	<?php
	echo '<h2 id="'.$list['SentencesList']['id'].'" class="editable editableSentencesListName">'.$list['SentencesList']['name'].'</h2>';

	echo '<div id="newSentenceInList">';
	echo $form->input('text', array("label" => __('Add a sentence to this list : ', true)));
	echo $form->button('OK', array("id" => "submitNewSentenceToList"));
	echo '</div>';
	

	echo '<div class="sentencesListLoading" style="display:none">';
	echo $html->image('loading.gif');
	echo '</div>';
	
	echo '<ul id="'.$list['SentencesList']['id'].'" class="sentencesList">';
	if(count($list['Sentence']) > 0){
	
		
		foreach($list['Sentence'] as $sentence){
			echo '<li id="sentence'.$sentence['id'].'">';
				// delete button			
				echo '<span class="options">';
				echo '<a id="'.$sentence['id'].'" class="removeFromListButton">';
				echo $html->image('close.png');
				echo '</a>';
				echo '</span>';		
				
				// display sentence
				$sentences->displaySentenceInList($sentence);
			echo '</li>';
		}
	}
	echo '</ul>';	
	?>
	</div>
</div>