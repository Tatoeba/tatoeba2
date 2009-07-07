<?php
if(isset($translation_text)){
	echo $javascript->link('jquery.jeditable.js', true);
	echo $javascript->link('sentences.edit_in_place.js', true);
	
	echo "<li class='direct editable translation'>";
	echo '<span id="'.$translation_lang.$translation_id.'" class="editableSentence '.$translation_lang.'">';
	echo $translation_text;
	echo '</span> ';
	echo "</li>";
	
}


/*	
	echo "<li >";
	echo '<span id="'.$sentence_id.'">';
	if ($is_the_same_language){
	echo "same" ;
	}else{
	echo "not the same hohoho";
	}	
	
	echo '</span> ';
	echo "</li>";
*/	

?>
