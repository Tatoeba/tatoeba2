<?php
class SentencesHelper extends AppHelper {

	var $helpers = array('Html', 'Form');
	
	/**
	 * $sentence : array("id" => int, "lang" => string, "text" => string)
	 * $translations : array ( $sentence )
	 * $options = array( can
	 */
	function displayGroup($sentence, $translations) {
		echo '<ul class="sentence translations">';
			// Sentence
			echo '<li class="original correctness'.$sentence['correctness'].'">'.$sentence['text'].'</li>';
			
			if(count($translations) > 0){
				// Translations
				foreach($translations as $translation){
					echo '<li class="direct translation correctness'.$translation['correctness'].'">';
						$lang = ($translation['lang'] != null) ? $translation['lang'] : '?'; 
						echo '<em>'.$lang.'</em>';
						echo $translation['text'];
					echo '</li>';
				}
			}
		echo '</ul>';
    }
	
	function displayForTranslation($sentence, $translations){
		echo '<ul class="sentence translations">';
			// Sentence
			echo '<li class="original correctness'.$sentence['correctness'].'">'.$sentence['text'].'</li>';				
			echo '<li>';
				echo $this->Form->create('Sentence', array("action" => "save_translation"));
				echo $this->Form->input('text', array("label" => __('Translation : ', true)));
				echo $this->Form->input('id', array("type" => "hidden", "value" => $sentence['id']));
				echo $this->Form->input('sentence_lang', array("type" => "hidden", "value" => $sentence['lang'])); // for logs
				echo $this->Form->end(__('OK',true));
			echo '<li>';
			
			if(count($translations) > 0){
				// Translations
				foreach($translations as $translation){
					echo '<li class="direct translation correctness'.$translation['correctness'].'">';
						echo '<em>'.$translation['lang'].'</em>';
						echo $translation['text'];
					echo '</li>';
				}
			}
		echo '</ul>';
	}
	
	function displayForCorrection($sentence){
		echo '<ul class="sentence translations">';
			// Sentence
			echo '<li class="original correctness'.$sentence['correctness'].'">'.$sentence['text'].'</li>';				
			echo '<li>';
				echo $this->Form->create('SuggestedModification', array("action" => "save_suggestion"));
				echo $this->Form->input('sentence_id', array("type" => "hidden", "value" => $sentence['id']));
				echo $this->Form->input('sentence_lang', array("type" => "hidden", "value" => $sentence['lang']));
				echo $this->Form->input('correction_text', array("label" => __('Correction : ',true), "value" => $sentence['text']));
				echo $this->Form->end(__('OK',true));
			echo '<li>';
		echo '</ul>';
	}
	
	
	function displayForEdit($sentence){
		echo '<ul class="sentence translations">';
			// Sentence
			echo '<li class="original">'.$sentence['text'].'</li>';				
			echo '<li>';
				echo $this->Form->create('Sentence', array("action" => "edit"));
				echo $this->Form->input('id', array("type" => "hidden", "value" => $sentence['id']));
				echo $this->Form->input('lang', array("type" => "hidden", "value" => $sentence['lang']));
				echo $this->Form->input('text', array("label" => __('Modification : ',true), "value" => $sentence['text']));
				echo $this->Form->end(__('OK',true));
			echo '<li>';
		echo '</ul>';
	}
	
	function displayMenu($id, $lang, $specialOptions, $score = null){
		echo '<ul class="menu">';
			echo '<li class="id">';
				$idAndLang = '<strong>' . $id . '</strong> <em>' . $lang .'</em>';
				$showUrl = $this->Html->url(
					array(
						"controller" => "sentences",
						"action" => "show",
						$id
					));
				echo '<a href="'.$showUrl.'">'.$idAndLang.'</a>';
			echo '</li>';
			
			// translate link => everyone can see
			echo '<li class="option">';
			echo $this->Html->link(
				__('Translate',true),
				array(
					"controller" => "sentences",
					"action" => "translate",
					$id
				));
			echo '</li>';
			
			// edit link => modify or suggest correction
			if(isset($specialOptions['canEdit']) AND $specialOptions['canEdit'] == true){
				echo '<li class="option">';
				echo $this->Html->link(
					__('Edit',true),
					array(
						"controller" => "sentences",
						"action" => "edit",
						$id
					));
				echo '</li>';
			}
			
			// suggest correction link
			echo '<li class="option">';
			echo $this->Html->link(
				__('Correct',true),
				array(
					"controller" => "suggested_modifications",
					"action" => "add",
					$id
				));
			echo '</li>';
			
			
			// discuss link
			if(isset($specialOptions['canComment']) AND $specialOptions['canComment'] == true){
				$action = "add";
			}else{
				$action = "show";
			}
			echo '<li class="option">';
			echo $this->Html->link(
				__('Comments',true),
				array(
					"controller" => "sentence_comments",
					"action" => $action,
					$id
				));
			echo '</li>';
			
			// logs
			echo '<li class="option">';
			echo $this->Html->link(
				__('Logs',true),
				array(
					"controller" => "contributions",
					"action" => "show",
					$id
				));
			echo '</li>';
			
			// delete link
			if(isset($specialOptions['canDelete']) AND $specialOptions['canDelete'] == true){
				echo '<li class="option">';
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
			
			if($score != null){
				echo '<li class="score">';
				echo intval($score * 100);
				echo '%';
				echo '</li>';
			}
		echo '</ul>';
	}
	
	function displayNavigation($currentId){
		echo '<div class="navigation">';
			$input = $this->params['pass'][0];
			echo $this->Form->create('Sentence', array("action" => "goTo", "type" => "get"));
			echo $this->Form->input('sentence_id', array("label" => __('Go to id : ', true), "value" => $input));
			echo $this->Form->end(__('OK',true));		
			echo '<ul>';
			
			// previous
			echo '<li class="option">';
			echo $this->Html->link(
				__('<< previous',true), 
				array(
					"controller" => "sentences",
					"action" => "show",
					$currentId-1
				)
			);
			echo '</li>';
			
			// random
			echo '<li class="option">';
			echo $this->Html->link(
				__('random',true), 
				array(
					"controller" => "sentences",
					"action" => "show",
					"random"
				)
			);
			echo '</li>';
			
			// next
			echo '<li class="option">';
			echo $this->Html->link(
				__('next >>',true), 
				array(
					"controller" => "sentences",
					"action" => "show",
					$currentId+1
				)
			);
			echo '</li>';
			
			echo '</ul>';
			
		echo '</div>';
	}
}
?>