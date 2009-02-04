<?php
$query = isset($_GET['query']) ? $_GET['query'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'romaji';

echo $form->create('Tool', array("action" => "kakasi", "type" => "get"));
echo $form->select('type', array('romaji' => 'romaji', 'furigana' => 'furigana'), $type);
echo $form->textarea('query', array("label" => '', "value" => $query));
echo $form->end(__('convert',true));

$kakasi->convert($query, $type);
?>