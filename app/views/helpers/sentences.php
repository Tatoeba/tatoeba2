<?php
class SentencesHelper extends AppHelper {

	var $helpers = array('Html', 'Form', 'Tooltip');
	
	/**
	 * Display a single sentence.
	 */
	function displaySentence($sentence) {
		echo '<div class="sentence">';
		// Sentence
		echo '<span class="original correctness'.$sentence['correctness'].' '.$sentence['lang'].'">';
		echo $this->Html->link($sentence['text'], array("controller" => "sentences", "action" => "show", $sentence['id']));
		echo '</span>';
		echo '</div>';
	}
	
	/**
	 * Diplay a sentence and its translations.
	 */
	function displayGroup($sentence, $translations) {
		echo '<div class="sentence">';
		
		// Sentence
		echo '<div class="original correctness'.$sentence['correctness'].'">';
		echo '<span class="'.$sentence['lang'].'">'.$sentence['text'].'</span>';
		echo '</div>';
		
		// Translations
		if(count($translations) > 0){	
			$controller = (preg_match("/sentence_comments|contributions/", $this->params['controller'])) ? $this->params['controller'] : "sentences";
			
			$translationsIds = array($sentence['id']);
			$indirectTranslations = array();
			echo '<ul class="translations">';
			foreach($translations as $translation){
				echo '<li class="direct translation correctness'.$translation['correctness'].'">';
				echo $this->Html->link(
					$translation['text'],
					array(
						"controller" => $controller,
						"action" => "show",
						$translation['id']
					),
					array("class" => $translation['lang'])
				);
				echo '</li>';
				
				$translationsIds[] = $translation['id'];
				if(isset($translation['IndirectTranslation'])){
					foreach($translation['IndirectTranslation'] as $indirectTranslation){
						$indirectTranslations[] = $indirectTranslation;
					}
				}
			}
		
			// indirect translations
			$this->displayIndirectTranslations($indirectTranslations, $translationsIds, $sentence['lang']);
			echo '</ul>';
		}
		
		echo '</div>';
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
				
				if(count($translations) > 0){
					$translationsIds = array($sentence['id']);
					$indirectTranslations = array();
					
					foreach($translations as $translation){
						echo '<li class="direct translation correctness'.$translation['correctness'].'">';
						echo $this->Html->link(
							$translation['text'],
							array(
								"controller" => "sentences",
								"action" => "translate",
								$translation['id']
							),
							array("class" => $translation['lang'])
						);
						echo '</li>';
						
						$translationsIds[] = $translation['id'];
						if(isset($translation['IndirectTranslation'])){
							foreach($translation['IndirectTranslation'] as $indirectTranslation){
								$indirectTranslations[] = $indirectTranslation;
							}
						}
					}
					
					// indirect translations
					$this->displayIndirectTranslations($indirectTranslations, $translationsIds, $sentence['lang']);
				}
			echo '</ul>';
			
		echo '</div>';
	}
	
	function displayIndirectTranslations($indirectTranslations, $translationsIds, $sentenceLang){
		if(count($indirectTranslations) > 0){
			foreach($indirectTranslations as $translation){
				if(!in_array($translation['id'], $translationsIds) AND $translation['lang'] != $sentenceLang){
					echo '<li class="indirect translation correctness'.$translation['correctness'].'">';
					echo $this->Html->link(
						$translation['text'],
						array(
							"controller" => "sentences",
							"action" => "translate",
							$translation['id']
						),
						array("class" => $translation['lang'])
					);
					echo '</li>';
				}
			}
		}
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
			
			if(count($translations) > 0){
				
				$translationsIds = array($sentence['id']);
				$indirectTranslations = array();
				
				echo '<ul class="translations">';
				// form to link to a sentence
				echo '<li class="form link">';
					echo $this->Form->create('SentencesTranslations', array("action" => "add"));
					echo $this->Form->input('translation_id', array("label" => 'Link to sentence nº '));
					echo $this->Form->input('id', array("type" => "hidden", "value" => $sentence['id']));
					echo $this->Form->end(__('OK',true));
				echo '<li>';
				
				// direct translations
				foreach($translations as $translation){
					echo '<li class="direct translation correctness'.$translation['correctness'].'">';
					echo $this->Html->link(
						$translation['text'],
						array(
							"controller" => "sentences",
							"action" => "translate",
							$translation['id']
						),
						array("class" => $translation['lang'])
					);
					echo '</li>';
					
					$translationsIds[] = $translation['id'];
					if(isset($translation['IndirectTranslation'])){
						foreach($translation['IndirectTranslation'] as $indirectTranslation){
							$indirectTranslations[] = $indirectTranslation;
						}
					}
				}
				
				// indirect translations
				$this->displayIndirectTranslations($indirectTranslations, $translationsIds, $sentence['lang']);
				echo '</ul>';
				
				
			}
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
	function displayMenu($id, $specialOptions, $score = null){		
		echo '<ul class="menu">';
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
			
			// translate link => everyone can see
			echo '<li class="'.$this->optionClass('translate').'">';
			echo $this->Html->link(
				__('Translate',true),
				array(
					"controller" => "sentences",
					"action" => "translate",
					$id
				));
			echo '</li>';
			
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
			
			// edit link => modify or suggest correction
			if(isset($specialOptions['canEdit']) AND $specialOptions['canEdit'] == true){
				echo '<li class="'.$this->optionClass('edit').'">';
				echo $this->Html->link(
					__('Edit',true),
					array(
						"controller" => "sentences",
						"action" => "edit",
						$id
					));
				echo '</li>';
			}
			
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