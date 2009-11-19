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
	<div class="module">
	<h2><?php __('Actions'); ?></h2>
	<ul class="sentencesListActions">
		<li>
			<?php 
			echo $html->link(
				__('Back to all the lists',true)
				, array("controller"=>"sentences_lists", "action"=>"index")
			)
			?>
		</li>
		
		<li>
			<?php 
			__('Show translations :'); echo ' ';
			$langArray = $languages->languagesArray();
			asort($langArray);
			$path  = '/' . Configure::read('Config.language') . '/sentences_lists/show/' . $list['SentencesList']['id'] . '/';
			echo $form->select(
				"translationLangChoice"
				, $langArray
				, null
				, array("onchange" => "$(location).attr('href', '".$path."' + this.value);")
			); 
			?>
		</li>
		
		<?php 
		if($session->read('Auth.User.id')){ 
		?>
		<li>
			<?php 
			echo $html->link(
				__('Edit list',true)
				, array("controller"=>"sentences_lists", "action"=>"edit", $list['SentencesList']['id'])
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
		<?php
		}
		?>
	</ul>
	</div>

	
	<div class="module">
	<h2><?php __('Printable versions'); ?></h2>
	<ul class="sentencesListActions">
		<li>
			<?php 
			echo $html->link(
				__('Print as exercise',true)
				, array("controller"=>"sentences_lists", "action"=>"print_as_exercise", $list['SentencesList']['id'], 'hide_romanization')
				, array("target" => "_blank", "class" => "printAsExerciseOption")
			);
			?>
		</li>
		<li>
			<?php 
			$translationParam = isset($translationsLang) ? $translationsLang : '';
			echo $html->link(
				__('Print as correction',true)
				, array("controller"=>"sentences_lists", "action"=>"print_as_correction", $list['SentencesList']['id'], $translationParam, 'hide_romanization')
				, array("target" => "_blank", "class" => "printAsCorrectionOption")
			);
			?>
		</li>
		<li>
			<?php 
			$javascript->link('sentences_lists.romanization_option.js', false);
			echo $form->checkbox(
				'display_romanization'
				, array("id" => "romanizationOption", "class" => "display")
			);
			echo ' ';
			__('Check this box to display romanization in the print version');
			?>
		</li>
	</ul>
	</div>
</div>
	
<div id="main_content">
	<div class="module">
	<h2><?php echo $list['SentencesList']['name'] ?></h2>

	<?php
	if(count($list['Sentence']) > 0){
		echo '<ul id="'.$list['SentencesList']['id'].'" class="sentencesList">';
		foreach($list['Sentence'] as $sentence){
			echo '<li id="sentence'.$sentence['id'].'">';
				// display sentence
				if(isset($translationsLang)){
					$sentences->displaySentenceInList($sentence, $translationsLang);
				}else{
					$sentences->displaySentenceInList($sentence);
				}
			echo '</li>';
		}
		echo '</ul>';
	}else{
		__('This list does not have any sentence');
	}
	?>
	</div>
</div>