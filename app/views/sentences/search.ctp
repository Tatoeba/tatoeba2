<h1>Search <?php echo (isset($query)) ? ': '.$query : '' ; ?></h1>
<?php
echo $form->create(null, array("action" => "search", "type" => "get"));
echo $form->input('query');
echo $form->end('search');

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
}
?>