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
	<?php
		echo '<h2>Go to...</h2>';
				
		echo $form->create('SentenceAnnotation', array("action" => "show"));
		echo $form->input('sentence_id', array("label" => "Sentence nº"));
		echo $form->end('OK');
	?>
	</div>

	<div class="module">
	<?php
		echo '<h2>Add new index</h2>';
		
		if(isset($sentence)){
			echo $form->create('SentenceAnnotation', array("action" => "save"));
			echo $form->hidden(
				'SentenceAnnotation.sentence_id'
				, array("value" => $sentence['Sentence']['id'])
			);
			echo $form->input('meaning_id');			
			echo $form->textarea('text', array(
				  "label" => ''
				, "cols" => 24
				, "rows" => 3
			));
			echo $form->end('save');
		}
	?>
	</div>
</div>

<div id="main_content">
	<div class="module">
	<?php
	if(isset($sentence)){
		echo '<h2>';
		echo sprintf ( __('Sentence nº%s', true) , $sentence['Sentence']['id']);
		echo '</h2>';
		
		echo '<p class="original">'.$sentence['Sentence']['text'].'</p>';
		
		foreach($annotations as $annotation){
			echo '<hr/>';
			echo '<p>'.$annotation['SentenceAnnotation']['text'].'</p>';
			
			echo $form->create('SentenceAnnotation', array("action" => "save"));
			
			// hidden ids necessary for saving
			echo $form->hidden(
				'SentenceAnnotation.id'
				, array("value" => $annotation['SentenceAnnotation']['id'])
			);
			echo $form->hidden(
				'SentenceAnnotation.sentence_id'
				, array("value" => $annotation['SentenceAnnotation']['sentence_id'])
			);
			
			// id of the "meaning" (i.e. English sentence for Tanaka sentences annotations)
			echo $form->input('meaning_id', array(
				"value" => $annotation['SentenceAnnotation']['meaning_id']
			));			
			
			// annotations text
			echo $form->textarea('text', array(
				"label" => ''
				, "value" => $annotation['SentenceAnnotation']['text']
				, "cols" => 60
				, "rows" => 3
			));
			
			// delete link
			echo $html->link(
				'delete'
				, array(
					"controller" => "sentence_annotations"
					, "action" => "delete"
					, $annotation['SentenceAnnotation']['id']
					, $annotation['SentenceAnnotation']['sentence_id']
				)
				, array("style"=>"float:right")
				, 'Are you sure?'
			);			
			
			// save button
			echo $form->end('save');
		}
	}
	?>
	</div>
</div>