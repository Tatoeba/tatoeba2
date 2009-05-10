<?php
if(isset($query)){
	$query = stripslashes($query);
	
	echo '<h2>Search : ' . $query . ', <em>' . $resultsInfo['sentencesCount'] . ' result(s)</em></h2>';
	
	if(isset($results)){
		
		$pagination->displaySearchPagination($resultsInfo['pagesCount'], $resultsInfo['currentPage'], $query, $from);
		
		$i = 0;
		
		foreach($results as $sentence){
			echo '<div class="sentences_set search">';
			// sentence menu (translate, edit, comment, etc)
			$sentences->displayMenu($sentence['Sentence']['id'], $sentence['Sentence']['lang'], $specialOptions, $scores[$i]);
			$i++;

			// sentence and translations
			$sentences->displayGroup($sentence['Sentence'], $sentence['Translation']);
			echo '</div>';
		}
		
		$pagination->displaySearchPagination($resultsInfo['pagesCount'], $resultsInfo['currentPage'], $query, $from);
		
	}else{
		__('No results for this search');
	}
}
?>