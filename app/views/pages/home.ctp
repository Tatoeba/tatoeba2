<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

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
	<?php
	if(!$session->read('Auth.User.id')){
	?>
	<div class="module">
		<h2><?php __('Join the community!'); ?></h2>
		<?php __("The more contributors there are, the more useful Tatoeba will become! Besides, by contributing, not only you will be helpful to the rest of the world, but you will also get to learn a lot."); ?>
		<p><?php 
		echo $html->link(
			'gros bouton register',
			array("controller" => "users", "action" => "register")
		);
		?></p>
	</div>
	<?php
	}
	?>
	
	<div class="module">
	<h2><?php __('Number of sentences') ?></h2>
	<?php echo $this->element('sentences_statistics'); ?>
	</div>
</div>

<div id="main_content">

	<?php
	if(!$session->read('Auth.User.id')){
	?>
	<div class="main_module">
		<h2><?php __('What is Tatoeba?'); ?></h2>
		<p>
		<?php 
			__('At its core, Tatoeba is a large database of <strong>example sentences</strong> translated into several languages. But as a whole, it is much more than that.');
			echo ' ' . $html->link(__('Learn more...',true), array('controller' => 'pages', 'action' => 'about')); 
		?>
		</p>
	</div>
	
	<div class="module">
		<h2><?php __('What can I do in Tatoeba?'); ?></h2>
		<div class="keyIdea">
			<span class="keyword">Learn</span> <span class="sub-keyword">languages</span>
			<ul>
				<li>Search sentences</li>
				<li>Create lists</li>
			</ul>
		</div>
		
		<div class="keyIdea">
			<span class="keyword">Share</span> <span class="sub-keyword">your knowledge</span>
			<ul>
				<li>Translate sentences</li>
				<li>Correct the mistakes</li>
			</ul>
		</div>
		
		<div class="keyIdea">
			<span class="keyword">Interact</span> <span class="sub-keyword">with the community</span>
			<ul>
				<li>Post comments</li>
				<li>Contact other members</li>
			</ul>
		</div>
	</div>
	
	<?php
	}
	?>
	
	<div class="module">
		<?php echo $this->element('random_sentence'); ?>
	</div>
	
	<?php
	if($session->read('Auth.User.id')){
	?>
		<div class="module">
			<h2><?php __('Latest contributions'); ?> <span class="annexe"><?php $tooltip->displayLogsColors(); ?> (<?=$html->link(__('show more...',true), array("controller"=>"contributions")); ?>) (<?=$html->link(__('show activity timeline',true), array("controller"=>"contributions", "action"=>"activity_timeline")); ?>)</span></h2>
			<?=$this->element('latest_contributions'); ?>
		</div>
		
		<div class="module">
			<h2><?php __('Latest comments'); ?> <span class="annexe">(<?=$html->link(__('show more...',true), array("controller"=>"sentence_comments")); ?>)</span></h2>
			<?=$this->element('latest_sentence_comments'); ?>
		</div>
	<?php
	}
	?>
</div>

