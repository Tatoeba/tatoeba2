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
?>

<div id="annexe_content">
	<div class="module">
		<?php
			if(!$session->read('Auth.User.id')){
				echo $this->element('login'); 
			} else {
				echo $this->element('space'); 
			}
		?>
	</div>

</div>

<div id="main_content">
	<div class="module">
		<?php
		echo '<h1 class="contribute">';
		__('How would you like to contribute?');
		echo '</h1>';
		
		echo '<h2 class="add">';
		__('Add your own sentences');
		echo '</h2>';
		echo '<div class="sentences_set">';
			echo '<div class="new">';
			echo $form->create('Sentence', array("action" => "add", "class" => "add"));
			echo $form->input('text', array("label" => __('Sentence : ', true)));
			echo $form->end('OK');
			echo '</div>';
		echo '</div>';
		
		
		echo '<br/>';
		
		echo '<h2 class="translate">';
		__('Translate existing sentences');
		echo '</h2>';
		
		__('Choose the language you would like to translate from : ');
		$langArray = $languages->languagesArray();
		asort($langArray);
		echo $form->select('Sentence.lang', $langArray);
		?>
	</div>
</div>

