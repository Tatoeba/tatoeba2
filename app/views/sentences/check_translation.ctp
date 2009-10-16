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

<div id="second_modules">
	<div class="module">
		<h2>Mon espace</h2>
		<?php
			if(!$session->read('Auth.User.id')){
				echo $this->element('login'); 
			} else {
				echo $this->element('space'); 
			}
		?>
	</div>

</div>

<div id="main_modules">
	<div class="module main_module">
	
	</div>
	<div class="module">
		<?php
		if(isset($translation_text)){
			if(isset($sentence_id)){
				echo $javascript->link('sentences.check_translation.js', true);
				echo '<li class="same_language_warning">';
				echo '<span id="same_language_text">' ;
				echo  __("Are you sure you want to translate this sentence into a sentence in the same language ?" , true );
				echo '</span>';
				echo '<div id="same_language_ok_cancel">';
				echo    '<input id="are_you_sure_submit" type="button" value="OK" />';
				echo    '<input id="are_you_sure_cancel" type="button" value="Cancel" />';
				echo    '<input id="'.$sentence_id.'_text" type="hidden" value="'.$translation_text.'" />';
				echo '</div>';
				echo'</li>';
			}else{
				echo $javascript->link('jquery.jeditable.js', true);
				echo $javascript->link('sentences.edit_in_place.js', true);
		
				echo "<li class='direct editable translation'>";
				echo '<span id="'.$translation_lang.$translation_id.'" class="editableSentence '.$translation_lang.'">';
				echo $translation_text;
				echo '</span> ';
				echo "</li>";
			}
		}
		?>
	</div>
</div>

