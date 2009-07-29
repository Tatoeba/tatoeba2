<?php
if (isset($this->params['lang'])) { 
	Configure::write('Config.language',  $this->params['lang']); 
}
?>

<div class="search_bar">

<?php
$languages = array(
	  'en' => __('English', true)
	, 'jp' => __('Japanese', true)
	, 'fr' => __('French', true)
	, 'de' => __('German', true)
	, 'es' => __('Spanish', true)
	, 'it' => __('Italian', true)
	, 'id' => __('Indonesian', true)
	, 'vn' => __('Vietnamese', true)
	, 'pt' => __('Portuguese', true)
	, 'ru' => __('Russian', true)
	, 'zh' => __('Chinese', true)
	, 'ko' => __('Korean', true)
	, 'nl' => __('Dutch', true)
);
asort($languages);
$selectedLanguageFrom = $session->read('search_from');
$selectedLanguageTo = $session->read('search_to');

echo $form->create('Sentence', array("action" => "search", "type" => "get"));
echo $form->select('from', $languages, $selectedLanguageFrom);
echo ' >> ';
echo $form->select('to', $languages, $selectedLanguageTo);
echo $form->input('query', array("label" => '', "value" => $session->read('search_query')));
echo $form->end(__('search',true));

echo $html->link(__('show examples',true), array("controller" => "sentences", "action" => "search"	));
?>
</div>