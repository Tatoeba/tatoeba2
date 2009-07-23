<?php
if(isset($translation_text)){
	echo $javascript->link('sentences.check_translation.js', true);
	echo '<li class="same_language_warning">';
	echo '<span id="same_language_text">' ;
	echo  __("Are you sure you want to translate this sentence by a sentence in the same language ?" , true );
	echo '</span>';
	echo '<div id="same_language_ok_cancel">';
	echo    '<input id="are_you_sure_submit" type="button" value="OK" />';
	echo    '<input id="are_you_sure_cancel" type="button" value="Cancel" />';
	echo    '<input id="'.$translation_id.'_text" type="hidden" value="'.$translation_text.'" />';
	echo '</div>';
	echo'</li>';}
?>