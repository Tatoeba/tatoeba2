<?php
if(isset($query)){
	echo '<h2>Search : ' . $session->read("unescapedQuery"). '</h2>';
	
	if(isset($results)){
		foreach($results as $sentence){
			echo '<div class="sentences_set">';
			// sentence menu (translate, edit, comment, etc)
			$sentences->displayMenu($sentence['Sentence']['id'], $sentence['Sentence']['lang'], $sentence['Sentence']['correctness'], $specialOptions);

			// sentence and translations
			$sentences->displayGroup($sentence['Sentence'], $sentence['Translation']);
			echo '</div>';	
			echo 'score : ' . $sentence['Score'];
			echo '<br/>';
		}
	}else{
		__('No results for this search');
	}
}else{
	echo $this->element('search_explanations');
}
?>