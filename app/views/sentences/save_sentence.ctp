<?php
if(isset($sentence_text)){
	
	echo $sentence_text;
	
}elseif(isset($sentence)){

	echo '<div class="sentences_set">';
	// sentence menu (translate, edit, comment, etc)
	$sentences->displayMenu($sentence['Sentence']['id'], $specialOptions);
	
	// sentence and translations
	$sentences->displayForTranslation($sentence['Sentence'], array());
	echo '</div>';
	
}
?>