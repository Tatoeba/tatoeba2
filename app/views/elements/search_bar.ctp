<?php
if (isset($this->params['lang'])) { 
	Configure::write('Config.language',  $this->params['lang']); 
}
?>

<div class="search_bar">

<?php
$query = isset($_GET['query']) ? $_GET['query'] : '';
$languages = array(
	  'en' => __('English', true)
	, 'jp' => __('Japanese', true)
	, 'fr' => __('French', true)
	, 'de' => __('German', true)
	, 'es' => __('Spanish', true)
	, 'it' => __('Italian', true)
	, 'vn' => __('Vietnamese', true)
	, 'ru' => __('Russian', true)
	, 'ch' => __('Chinese', true)
	, 'ko' => __('Korean', true)
	, 'nl' => __('Dutch', true)
);
$selectedLanguage = isset($_GET['from']) ? $_GET['from'] : null;

echo $form->create('Sentence', array("action" => "search", "type" => "get"));
echo $form->select('from', $languages, $selectedLanguage);
echo $form->input('query', array("label" => '', "value" => stripslashes($query)));
echo $form->end(__('search',true));

echo $html->link(__('show examples',true), array("controller" => "sentences", "action" => "search"	));
?>
</div>