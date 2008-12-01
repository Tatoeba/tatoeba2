<h1>Search <?php echo (isset($query)) ? ': '.$query : '' ; ?></h1>
<?php
echo $form->create(null, array("action" => "search", "type" => "get"));
echo $form->input('query');
echo $form->end('search');

if(isset($results)){
	foreach($results as $sentence){
		echo 'sentence : ' . $sentence['Sentence']['text'];
		echo ' / ';
		echo 'score : ' . $sentence['Score'];
		echo '<br/>';
	}
}
?>