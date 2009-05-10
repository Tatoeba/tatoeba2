<?php 
$this->pageTitle = __('Tatoeba : Collecting example sentences',true); 

// Warning message prompting the user to specify languages
if($session->read('Auth.User.id')){
	$count_unknown_language = $this->requestAction('/sentences/count_unknown_language');
	if($count_unknown_language > 0){
		echo '<div id="flashMessage">';
		__('The language of some the sentences you have added could not be detected. ');
		echo $html->link(__('Click here.', true), array("controller" => "sentences", "action" => "unknown_language"));
		echo '</div>';
	}
	$javascript->link('sentences.add_translation.js', false);
}


$key = isset($this->params['lang']) ? $this->params['lang'] : 'eng';
echo $this->element('sentences_statistics', array('cache' => 
		array(
			'time' => '+12 hours', 
			'key' => $key
		)
	)
); 
?>

<div id="homepage">
<?php 
$lang = 'eng';
if (isset($this->params['lang'])) { 
	Configure::write('Config.language',  $this->params['lang']); 
	$lang = $this->params['lang'];
}
echo '<div class="element" style="font-size:16px">';
echo '<h2>';
__('Welcome on Tatoeba Project');
echo '</h2>';

echo '<p>';
__('This project aims to build a multilingual aligned corpus. In other words, to collect sentences translated in several languages. ');
echo $html->link(__('Everyone can contribute! ',true), array("controller" => "sentences", "action" => "contribute"));
__('These sentences can be downloaded for free here : ');
echo $html->link(
	__('Downloads',true), 
	array("controller" => "pages", "action" => "download-tatoeba-example-sentences")
);
echo '.';
echo '</p>';
echo '</div>';

echo '<div class="element">';
echo '<h2>';
__('Random sentence'); 
echo ' (';
echo '<a id="showRandom" lang='.$lang.'>' . __('show another',true) . '</a>';
echo ')</h2>';
echo '<div class="random_sentences_set"></div>';
echo '</div>';


echo '<div class="element">';
echo '<h2>';
__('Latest contributions');
echo ' ';
$tooltip->displayLogsColors();
echo ' (';
echo $html->link(__('show more...',true), array("controller"=>"contributions"));
echo ')</h2>';
echo $this->element('latest_contributions');
echo '</div>';

echo '<div class="element">';
echo '<h2>';
__('Latest comments');
echo '</h2>';
echo $this->element('latest_sentence_comments');
echo '</div>';
?>
</div>