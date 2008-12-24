<div class="search_bar">

<?php
echo $form->create('Sentence', array("id" => "SentenceSearchForm", "url" => '/'.$this->params['lang'].'/sentences/search'));
echo $form->input('query', array("label" => '', "value" => htmlentities($session->read("unescapedQuery"))));
echo $form->end(__('search',true));

echo $html->link(__('show examples',true), array("controller" => "sentences", "action" => "search"	));
?>
</div>