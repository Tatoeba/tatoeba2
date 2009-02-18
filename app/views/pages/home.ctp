<?php 
$this->pageTitle = __('Tatoeba : Collecting example sentences',true); 

// Warning message prompting the user to specify languages
if($session->read('Auth.User.id')){
	$count_unknown_language = $this->requestAction('/sentences/count_unknown_language');
	if($count_unknown_language > 0){
		echo '<div id="flashMessage">';
		__('The language of some the sentences you have added could not be detected. ');
		echo $html->link(__('Click here.', true), '/sentences/unknown_language/');
		echo '</div>';
	}
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
echo '<div class="element" style="font-size:16px">';
echo '<h2>';
__('Welcome on Tatoeba Project');
echo '</h2>';

echo '<p>';
__('This project aims to build a multilingual aligned corpus. In other words, to collect sentences translated in several languages. These sentences can be downloaded for free here : ');
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
echo '</h2>';
echo $this->element('random_sentence');
echo '</div>';
$tooltip->displayAdoptTooltip();


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