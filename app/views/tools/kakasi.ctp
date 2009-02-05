<?php
$javascript->link('furigana', false);
$this->pageTitle = __('Convert Japanese text into romaji or furigana',true);

echo '<h2>';
__('Convert Japanese text into romaji or furigana (powered by <a target="_blank" href="http://kakasi.namazu.org/">KAKASI</a>)');
echo '</h2>';

$query = isset($_GET['query']) ? $_GET['query'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'romaji';

if($query != ''){
	echo '<div id="conversion">';
	$kakasi->convert($query, $type);
	echo '</div>';
}

echo $form->create('Tool', array("action" => "kakasi", "type" => "get"));
echo $form->textarea('query', array("label" => '', "value" => $query));
echo '<p>';
__('Convert japanese text into : ');
echo $form->radio(
	'type', 
	array('romaji' => 'romaji', 'furigana' => 'furigana'), 
	array('value' => $type, 'legend' => '')
);
echo '</p>';
echo $form->end(__('Convert',true));
?>

<script>
furigana();
</script>