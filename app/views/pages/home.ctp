<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  TATOEBA Project(should be changed)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
$this->pageTitle = __('Tatoeba : Collecting example sentences',true);
echo $javascript->link('sentences.statistics.js', false);

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


$lang = 'eng';
if (isset($this->params['lang'])) {
	Configure::write('Config.language',  $this->params['lang']);
	$lang = $this->params['lang'];
}

$langArray = $languages->languagesArray();
asort($langArray);
$selectedLanguage = $session->read('random_lang_selected');
array_unshift($langArray, array('any' => __('any', true)));


?>
<div id="annexe_content">
	<div class="module">
		<h2><?=__('Tatoeba ?',true); ?></h2>
		<p><?=__('Tatoeba means "for example" in Japanese. The aim is not to implement a new multilingual dictionnary, but a corpus of sentence emphasizing use of different words',true); ?><a href=""><?=__('Details...',true)?></a></p>
	</div>
	<div class="module">
		<h2><?=__('News',true); ?></h2>
		
	</div>
</div>

<div id="main_content">
	
	<div class="main_module">
		<h2><?=__('What is it ?',true); ?></h2>
		<p><?=__('Tatoeba is a free project which aims to establish a patrimony by gathering people from all over the world around one of the greatest inventions of mankind : language.',true); ?><a href=""><?=__('Details...',true)?></a></p>
	</div>
	
	<div class="module">
		<h2><?php __('Random sentence'); ?> <span class="annexe">(<?='<a id="showRandom" lang='.$lang.'>' . __('show another ', true) . '</a> ';?><?=$form->select("randomLangChoice", $langArray, $selectedLanguage, null, false); ?>)</span></h2>
		<div class="random_sentences_set"></div>
	</div>
	
	<div class="module">
		<h2><?php __('Latest contributions'); ?> <span class="annexe"><?php $tooltip->displayLogsColors(); ?> (<?=$html->link(__('show more...',true), array("controller"=>"contributions")); ?>) (<?=$html->link(__('show activity timeline',true), array("controller"=>"contributions", "action"=>"activity_timeline")); ?>)</span></h2>
		<?=$this->element('latest_contributions'); ?>
	</div>
	
	<div class="module">
		<h2><?php __('Latest comments'); ?> <span class="annexe">(<?=$html->link(__('show more...',true), array("controller"=>"sentence_comments")); ?>)</span></h2>
		<?=$this->element('latest_sentence_comments'); ?>
	</div>
</div>

