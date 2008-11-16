<?php $this->pageTitle = __('Sentences in Tatoeba',true) ?>

<h1><?php __('Sentences') ?></h1>
<ul>
<?php 
foreach ($sentences as $sentence):
	echo '<li>';
	echo $sentence['Sentence']['id'];
	echo '. ';
	echo $html->link(
		$sentence['Sentence']['text'], 
		array(
			"controller"=>"sentences", 
			"action"=>"show",
			$sentence['Sentence']['id']
		)
	);
	echo '</li>';
endforeach; 
?>
</ul>