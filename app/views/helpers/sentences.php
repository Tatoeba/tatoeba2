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
class SentencesHelper extends AppHelper {

	var $helpers = array('Html', 'Form', 'Tooltip', 'Kakasi', 'Javascript');
	
	/**
	 * Display a single sentence.
	 */
	function displaySentence($sentence) {
		echo '<div class="original sentence">';
		// Sentence
		echo '<span class="correctness'.$sentence['correctness'].' '.$sentence['lang'].'">';
		echo $this->Html->link($sentence['text'], array("controller" => "sentences", "action" => "show", $sentence['id']));
		echo '</span> ';
		
		// TODO I don't like how there's a lot of copy-paste of these if-blocks 
		// in the rest of the code. Should factorize that somehow.
		if($sentence['lang'] == 'jp'){
			$this->displayRomaji($sentence['text']);
		}
		if($sentence['lang'] == 'zh'){
			$this->displayPinyin($sentence['text']);
		}
		
		echo '</div>';
	}
	
	/**
	 * Display romaji.
	 */
	function displayRomaji($text){
		echo '<span class="romaji" title="'.__('WARNING : the romaji is automatically generated and is not always reliable.',true).'">';
			$this->Kakasi->convert($text, 'romaji');
		echo '</span>';
	}
	
	/**
	 * Display pinyin.
	 */
	function displayPinyin($text){
		echo '<span class="pinyin">';
		$curl = curl_init();
		curl_setopt ($curl, CURLOPT_URL, "http://adsotrans.com/popup/pinyin.php?text=".$text);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec ($curl);
		$pinyin = substr($result, 14);
		$pinyin = substr($pinyin, 0, -44);
		echo $pinyin;
		echo '</span>';
	}
	
	/**
	 * Display a single sentence for edit in place.
	 */
	function displayEditableSentence($sentence) {
		echo '<div class="editable original sentence">';
			// Sentence
			echo '<span id="'.$sentence['lang'].$sentence['id'].'" class="editableSentence correctness'.$sentence['correctness'].' '.$sentence['lang'].'">';
			echo $sentence['text'];
			echo '</span> ';
			if($sentence['lang'] == 'jp'){
				$this->displayRomaji($sentence['text']);
			}
			if($sentence['lang'] == 'zh'){
				$this->displayPinyin($sentence['text']);
			}
		echo '</div>';
	}
	
	/**
	 * Diplay a sentence and its translations.
	 */
	function displayGroup($sentence, $translations, $user = null) {
		echo '<div class="sentence">';
		
		// Sentence
		$this->Javascript->link('jquery.jeditable.js', false);
		$this->Javascript->link('sentences.edit_in_place.js', false);
		
		$editable = '';
		$editableSentence = '';
		$tooltip = __('This sentence does not belong to anyone, you can adopt it', true);
		if($user != null){
			if(isset($user['canEdit']) AND $user['canEdit']){
				$editable = 'editable';
				$editableSentence = 'editableSentence';
			}
			if(isset($user['username']) AND $user['username'] != ''){
				$tooltip = __('This sentence belongs to :', true) .' '.$user['username'];
			}
		}
		
		echo '<div title="'.$tooltip.'" class="'.$editable.' original correctness'.$sentence['correctness'].'">';
		echo '<span id="'.$sentence['lang'].$sentence['id'].'" class="'.$editableSentence.' '.$sentence['lang'].'">'.$sentence['text'].'</span> ';
		if($sentence['lang'] == 'jp'){
			$this->displayRomaji($sentence['text']);
		}
		if($sentence['lang'] == 'zh'){
			$this->displayPinyin($sentence['text']);
		}
		echo '</div>';
		
		echo '<ul id="translation_for_'.$sentence['id'].'" class="addTranslations">';
		echo '</ul>';
		
		echo '<ul id="'.$sentence['id'].'_translations" class="translations">';
		if(count($translations) > 0){
			// direct translations
			$this->displayTranslations($translations, 'show');
			
			// indirect translations
			$this->displayIndirectTranslations($sentence, 'show');
		}
		echo '</ul>';
		
		echo '</div>';
    }
	 
	/**
	 * Display direct translations.
	 */
	function displayTranslations($translations, $action){			
		$controller = (preg_match("/sentence_comments|contributions/", $this->params['controller'])) ? $this->params['controller'] : "sentences";
		
		foreach($translations as $translation){
			echo '<li class="direct translation correctness'.$translation['correctness'].'">';
			echo $this->Html->link(
				$translation['text'],
				array(
					"controller" => $controller,
					"action" => $action,
					$translation['id']
				),
				array("class" => $translation['lang'])
			);
			if($translation['lang'] == 'jp'){
				$this->displayRomaji($translation['text']);
			}
			if($translation['lang'] == 'zh'){
				$this->displayPinyin($translation['text']);
			}
			echo '</li>';
		}
	}
	
	/**
	 * Display indirect translations, that is to say translations of translations.
	 */
	function displayIndirectTranslations($sentence, $action){
		if(isset($sentence['Translation'])){
			$translations = $sentence['Translation'];
			$translationsIds = array($sentence['id']);
			$indirectTranslations = array();
			
			foreach($translations as $translation){
				$translationsIds[] = $translation['id'];
				if(isset($translation['IndirectTranslation'])){
					foreach($translation['IndirectTranslation'] as $indirectTranslation){
						if($indirectTranslation['id'] != $sentence['Sentence']['id']){
							$indirectTranslations[] = $indirectTranslation;
						}
					}
				}
			}
			
			if(count($indirectTranslations) > 0){
				foreach($indirectTranslations as $translation){
					if(!in_array($translation['id'], $translationsIds) AND $translation['lang'] != $sentenceLang){
						echo '<li class="indirect translation correctness'.$translation['correctness'].'">';
						echo $this->Html->link(
							$translation['text'],
							array(
								"controller" => "sentences",
								"action" => $action,
								$translation['id']
							),
							array("class" => $translation['lang'])
						);
						echo '</li>';
					}
				}
			}
		}
	}
	
	/**
	 * Display group of sentence with a text input to add a translation.
	 */
	function displayForTranslation($sentence, $translations){
		echo '<div class="sentence">';
		
			// Sentence
			echo '<div class="original correctness'.$sentence['correctness'].' '.$sentence['lang'].'">'.$sentence['text'].'</div>';
			
			// Translations
			echo '<ul class="translations">';
				echo '<li class="form direct">';
					echo $this->Form->create('Sentence', array("action" => "save_translation"));
					if(count($translations) > 0){
						$this->Tooltip->displayMainSentenceTooltip();
					}
					echo $this->Form->input('text', array("label" => ''));
					echo $this->Form->input('id', array("type" => "hidden", "value" => $sentence['id']));
					echo $this->Form->input('sentence_lang', array("type" => "hidden", "value" => $sentence['lang'])); // for logs
					echo $this->Form->end(__('OK',true));
				echo '<li>';
			echo '</ul>';
		echo '</div>';
	}
	
	/**
	 * Display sentences, direct translations and indirect ones. User can also enter the id
	 * of another sentence if he wants to link that sentence to the current sentence.
	 */
	function displayForLink($sentence, $translations){
		echo '<div class="sentence">';
		
			// Sentence
			echo '<div class="original correctness'.$sentence['correctness'].'">';
			echo '<span class="'.$sentence['lang'].'">'.$sentence['text'].'</span>';
			echo '</div>';
			
			echo '<ul class="translations">';
			if(count($translations) > 0){
				// direct translations
				$this->displayTranslations($translations, 'link');
				
				// indirect translations
				$this->displayIndirectTranslations($sentence, 'link');
			}
			echo '</ul>';
		echo '</div>';
	}
	
	/**
	 * Display group of sentence with a text input to make a modification.
	 */
	function displayForEdit($sentence){
		echo '<div class="sentence">';
		
			// Sentence
			echo '<span class="original correctness'.$sentence['correctness'].' '.$sentence['lang'].'">'.$sentence['text'].'</span>';
			
			echo '<ul>';
				echo '<li class="form">';
				echo $this->Form->create('Sentence', array("action" => "edit"));
				echo $this->Form->input('id', array("type" => "hidden", "value" => $sentence['id']));
				echo $this->Form->input('lang', array("type" => "hidden", "value" => $sentence['lang']));
				echo $this->Form->input('text', array("label" => __('Modification : ',true), "value" => $sentence['text']));
				echo $this->Form->end(__('OK',true));
				echo '<li>';
			echo '</ul>';
		echo '</div>';
	}
	
	/**
	 * Sentence options (translate, edit, correct, comments, logs, edit, ...)
	 */
	function displayMenu($id, $lang, $specialOptions, $score = null){		
		echo '<ul class="menu" id="'. $id .'" lang="'.$lang.'">';
			if($score != null){
				echo '<li class="score">';
				echo intval($score * 100);
				echo '%';
				echo '</li>';
			}
			
			echo '<li class="'.$this->optionClass('show').'">';
				echo $this->Html->link(
				$id,
				array(
					"controller" => "sentences",
					"action" => "show",
					$id
				));
			echo '</li>';
			
			// translate link
			if($specialOptions['canTranslate']){
				$this->Javascript->link('sentences.add_translation.js', false);
				echo '<li class="'.$this->optionClass('translate').' translateLink">';
				echo '<a>' . __('Translate',true) . '</a>';
				echo '</li>';
			}
			
			// "link" link => everyone can see
			// echo '<li class="'.$this->optionClass('link').'">';
			// echo $this->Html->link(
				// __('Link',true),
				// array(
					// "controller" => "sentences",
					// "action" => "link",
					// $id
				// ));
			// echo '</li>';
			
			// discuss link
			echo '<li class="'.$this->optionClass('comments').'">';
			echo $this->Html->link(
				__('Comments',true),
				array(
					"controller" => "sentence_comments",
					"action" => "show",
					$id
				));
			echo '</li>';
			
			// logs
			echo '<li class="'.$this->optionClass('logs').'">';
			echo $this->Html->link(
				__('Logs',true),
				array(
					"controller" => "contributions",
					"action" => "show",
					$id
				));
			echo '</li>';
			
			// adopt
			if(isset($specialOptions['canAdopt']) AND $specialOptions['canAdopt'] == true){
				echo '<li class="option">';
				echo $this->Html->link(
					__('Adopt',true),
					array(
						"controller" => "sentences",
						"action" => "adopt",
						$id
					));
				echo '</li>';
			}
			
			// let go
			if(isset($specialOptions['canLetGo']) AND $specialOptions['canLetGo'] == true){
				echo '<li class="option">';
				echo $this->Html->link(
					__('Let go',true),
					array(
						"controller" => "sentences",
						"action" => "let_go",
						$id
					));
				echo '</li>';
			}
			
			if(isset($specialOptions['canFavorite']) AND $specialOptions['canFavorite'] == true){
				
				$this->Javascript->link('favorites.add.js', false);
				echo '<li class="option favorite add" id="favorite_'.$id.'">';
				echo '<a>' . __('Favorite',true) . '</a>' ; 
				echo '</li>';
			}

			if(isset($specialOptions['canUnFavorite']) AND $specialOptions['canUnFavorite'] == true){
				
				$this->Javascript->link('favorites.add.js', false);
				echo '<li class="option favorite remove" id="favorite_'.$id.'">';
				echo '<a>' . __('Unfavorite',true) . '</a>' ; 
				echo '</li>';
			}
			
			if(isset($specialOptions['canAddToList']) AND $specialOptions['canAddToList'] == true){
				//$this->Javascript->link('sentences_lists.menu.js', false);
				$lists = $this->requestAction('/sentences_lists/choices');
				echo '<li class="option addToList">';
				//echo '<a>' . __('Add to list',true) . '</a>' ; 
				echo '<select>';
				foreach($lists as $list){
					echo '<option value="'.$list['SentencesList']['id'].'">';
					echo $list['SentencesList']['name'];
					echo '</option>';
				}
				echo '</select>';
				echo '</li>';
			}
			
			// delete link
			if(isset($specialOptions['canDelete']) AND $specialOptions['canDelete'] == true){
				echo '<li class="option delete">';
				echo $this->Html->link(
					__('Delete',true), 
					array(
						"controller" => "sentences",
						"action" => "delete",
						$id
					), 
					null, 
					'Are you sure?');
				echo '</li>';
			}
			
			echo $this->Html->image('loading-small.gif', array("id"=>"favorite_".$id."_in_process", "style"=>"display:none"));
			
			if(isset($specialOptions['belongsTo'])){
				echo '<li class="belongsTo">- ';
				echo __('belongs to ', true);
				echo $specialOptions['belongsTo'];
				echo '</li>';
			}
			
		echo '</ul>';
	}
	
	function optionClass($optionName){
		$options = array(
			  'show' => array("controller" => "sentences", "action" => "show")
			, 'translate' => array("controller" => "sentences", "action" => "translate")
			, 'link' => array("controller" => "sentences", "action" => "link")
			, 'edit' => array("controller" => "sentences", "action" => "edit")
			, 'correct' => array("controller" => "suggested_modifications", "action" => "add")
			, 'comments' => array("controller" => "sentence_comments", "action" => "show")
			, 'logs' => array("controller" => "contributions", "action" => "show")
		);
		
		foreach($options as $name => $route){
			if($name == $optionName){
				$currentRoute = array("controller" => $this->params['controller'], "action" => $this->params['action']);
				if($route == $currentRoute){
					return 'selected';
				}
			}
		}
		
		return 'option';
	}
}
?>
