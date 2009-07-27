<?php
if(isset($translation_text)){
	if(isset($sentence_id)){
		echo $javascript->link('sentences.check_translation.js', true);
		echo '<li class="same_language_warning">';
		echo '<span id="same_language_text">' ;
		echo  __("Are you sure you want to translate this sentence by a sentence in the same language ?" , true );
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
	}}
?>