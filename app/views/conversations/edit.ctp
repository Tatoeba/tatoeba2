<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang (tranglich@gmail.com)

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
$selectedLanguageFrom = 'en';
$selectedLanguageTo = 'zh';
?>

<div id="annexe_content">
	<div class="module">
		<?php
			if(!$session->read('Auth.User.id')){
				echo $this->element('login'); 
			} else {
				echo $this->element('space'); 
			}
		?>
	</div>

</div>

<div id="main_content">
	<div class="module">
		<h2><?=__('Conversations', true); ?></h2>
		
		<?php
		if ($mode == 'new') {
			echo $form->select('from', $languages, $selectedLanguageFrom);
			echo $form->select('to', $languages, $selectedLanguageTo);
			
			?>
			<h3><?=__('Add a new conversation', true); ?></h3>
			
			<?php
			echo $form->create('Conversation');
			echo '<div id="sentencesList">';
			echo $form->input('title', array('label' => __('Title', true).' : '));
			echo $form->inputs(
				array(
					'legend' => 'Sentence 1',
					'speaker1' => array('label' => __('Speaker', true).' : ', 'class' => 'speaker'),
					'content_from1' => array('label' =>'Content ('.$selectedLanguageFrom.')', 'class' => 'content_from'),
					'content_to1' => array('label' =>'Content ('.$selectedLanguageTo.')', 'class' => 'content_to')));
					
			echo $form->inputs(
				array(
					'legend' => 'Sentence 2',
					'speaker2' => array('label' => __('Speaker', true).' : ', 'class' => 'speaker'),
					'content_from2' => array('label' =>'Content ('.$selectedLanguageFrom.')', 'class' => 'content_from'),
					'content_to2' => array('label' =>'Content ('.$selectedLanguageTo.')', 'class' => 'content_to')));
			echo $form->hidden('nb_replies', array('value' => '2'));
			echo $form->hidden('lang_from', array('value' => $selectedLanguageFrom));
			echo $form->hidden('lang_to', array('value' => $selectedLanguageTo));
			
			echo '</div>';
			?>
			<div id="testForm"></div>
			<a id="addNewReply"><?=__('Add a new reply', true); ?></a>
			<?php
			echo $form->end(__('Save this conversation', true));
		}
		?>
		
	</div>
	<div class="module">
		<h2><?=__('Last added conversations', true); ?></h2>

	</div>
</div>

<?php
?>