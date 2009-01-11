<div class="search_bar">

<?php
$query = isset($_GET['query']) ? $_GET['query'] : '';
//echo $form->create('Sentence', array("id" => "SentenceSearchForm", "url" => '/'.$this->params['lang'].'/sentences/search'));
echo $form->create('Sentence', array("action" => "search", "type" => "get"));
echo $html->image('search.png', array('alt'=> 'search'));
echo $form->input('query', array("label" => '', "value" => $query));
echo $form->end(__('search',true));

echo $html->link(__('show examples',true), array("controller" => "sentences", "action" => "search"	));
?>
</div>