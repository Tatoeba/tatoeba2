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
<div id="second_modules">
	<div class="module">
		<h2>Mon espace</h2>
		<?php
			if(!$session->read('Auth.User.id')){
				echo $this->element('login'); 
			} else {
				echo $this->element('space'); 
			}
		?>
	</div>
	<div class="module">
		<h2>Tatoeba ? Kezako ?</h2>
		<p>"Tatoeba" () signifie en japonais "par exemple". Le but n'est pas de concevoir un nouveau dictionnaire multilingue, mais un corpus de phrases montrant l'utilisation de différents mots <a href="">Plus d'infos...</a></p>
	</div>
	<div class="module">
		<h2>Actualités</h2>
		<p>Ce 	projet a pour but de construire un corpus aligné multilingue. En	d'autres termes, de collecter des phrases traduites dans plusieurs	langues. <a href="http://tatoeba.fr/fre/sentences/contribute">Tout le monde peut contribuer! </a>Ces phrases peuvent être téléchargées ici : <a href="http://tatoeba.fr/fre/pages/download-tatoeba-example-sentences">Téléchargements</a>.</p>
	</div>
</div>

<div id="main_modules">
	<div class="module main_module">
		<h2><?php __('Welcome to Tatoeba Project'); ?></h2>
		<p>
		<?php 
		__('This project aims to build a multilingual aligned corpus. In other words, to collect sentences translated in several languages. ');
		echo $html->link(__('Everyone can contribute! ',true), array("controller" => "sentences", "action" => "contribute"));
		__('These sentences can be downloaded for free here : '); 
		echo $html->link(
			__('Downloads',true), 
			array("controller" => "pages", "action" => "download-tatoeba-example-sentences")
		);
		?>
		</p>
		</div>
	<div class="module">
		<h2>Qu'est ce que c'est ?</h2>
		<p>Tatoeba est un projet libre et vise à mettre en place un patrimoine en essayant de réunir les gens des 4 coins de la Terre autour d'une des plus grandes inventions de l'Humanité: le langage. <a href="">Plus d'infos...</a></p>
	</div>
	<div class="module">
		<h2><?php __('Random sentence'); ?> (<?='<a id="showRandom" lang='.$lang.'>' . __('show another ', true) . '</a>';?><?=$form->select("randomLangChoice", $langArray, $selectedLanguage, null, false); ?>)</h2>
		<div class="random_sentences_set"></div>
	</div>
	<div class="module">
		<h2><?php __('Latest contributions'); ?> <?php $tooltip->displayLogsColors(); ?> (<?=$html->link(__('show more...',true), array("controller"=>"contributions")); ?>) (<?=$html->link(__('show activity timeline',true), array("controller"=>"contributions", "action"=>"activity_timeline")); ?>)</h2>
		<?=$this->element('latest_contributions'); ?>

	</div>
	<div class="module">
		<h2><?php __('Latest comments'); ?> (<?=$html->link(__('show more...',true), array("controller"=>"sentence_comments")); ?>)</h2>
		<?=$this->element('latest_sentence_comments'); ?>
	</div>
</div>

