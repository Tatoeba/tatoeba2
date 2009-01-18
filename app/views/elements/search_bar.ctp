<?php
if (isset($this->params['lang'])) { 
	Configure::write('Config.language',  $this->params['lang']); 
}
?>

<div class="search_bar">

<?php
$query = isset($_GET['query']) ? $_GET['query'] : '';
echo $form->create('Sentence', array("action" => "search", "type" => "get"));
echo $form->input('query', array("label" => '', "value" => stripslashes($query)));
echo $form->end(__('search',true));

echo $html->link(__('show examples',true), array("controller" => "sentences", "action" => "search"	));
?>
</div>