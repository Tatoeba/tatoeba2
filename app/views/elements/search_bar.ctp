<div class="search_bar">

<?php
// because cakePHP escapes the "+" and I don't want that...
$query = preg_replace("#[^(.)]*/sentences/search/([.]*)#", "$2", $_SERVER['REQUEST_URI']);

echo $form->create('Sentence', array("action" => "search"));
echo $form->input('query', array("label" => '', "value" => $session->read("unescapedQuery")));
echo $form->end(__('search',true));		
?>
</div>