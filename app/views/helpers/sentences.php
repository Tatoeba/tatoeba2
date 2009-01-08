<?php
class SentencesHelper extends AppHelper {

	var $helpers = array('Html', 'Form');
	
	/**
	 * $sentence : array("id" => int, "lang" => string, "text" => string)
	 * $translations : array ( $sentence )
	 * $options = array( can
	 */
	function displayGroup($sentence, $translations) {
		echo '<div class="sentence">';
		
		// Sentence
		echo '<span class="original correctness'.$sentence['correctness'].' '.$sentence['lang'].'">'.$sentence['text'].'</span>';
		
		// Translations
		if(count($translations) > 0){	
			echo '<ul class="translations">';
			foreach($translations as $translation){
				echo '<li class="direct translation correctness'.$translation['correctness'].' '.$translation['lang'].'">';
				echo $this->Html->link(
					$translation['text'],
					array(
						"controller" => "sentences",
						"action" => "show",
						$translation['id']
					)
				);
				echo '</li>';
			}
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
			echo '<span class="original correctness'.$sentence['correctness'].' '.$sentence['lang'].'">'.$sentence['text'].'</span>';
			
			// Translations
			echo '<ul class="translations">';
				echo '<li class="form">';
					echo $this->Form->create('Sentence', array("action" => "save_translation"));
					echo $this->Form->input('text', array("label" => __('Translation : ', true)));
					echo $this->Form->input('id', array("type" => "hidden", "value" => $sentence['id']));
					echo $this->Form->input('sentence_lang', array("type" => "hidden", "value" => $sentence['lang'])); // for logs
					echo $this->Form->end(__('OK',true));
				echo '<li>';
				
				if(count($translations) > 0){
					foreach($translations as $translation){
						echo '<li class="direct translation correctness'.$translation['correctness'].' '.$translation['lang'].'">';
						echo $this->Html->link(
							$translation['text'],
							array(
								"controller" => "sentences",
								"action" => "show",
								$translation['id']
							)
						);
						echo '</li>';
					}
				}
			echo '</ul>';
		
		echo '</div>';
	}
	
	/**
	 * Display group of sentence with a text input to suggest a correction.
	 */
	function displayForCorrection($sentence){
		echo '<div class="sentence">';
		
			// Sentence
			echo '<span class="original correctness'.$sentence['correctness'].' '.$sentence['lang'].'">'.$sentence['text'].'</span>';
			
			echo '<ul>';
				echo '<li class="form">';
					echo $this->Form->create('SuggestedModification', array("action" => "save_suggestion"));
					echo $this->Form->input('sentence_id', array("type" => "hidden", "value" => $sentence['id']));
					echo $this->Form->input('sentence_lang', array("type" => "hidden", "value" => $sentence['lang']));
					echo $this->Form->input('correction_text', array("label" => __('Correction : ',true), "value" => $sentence['text']));
					echo $this->Form->end(__('OK',true));
				echo '<li>';
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
			
			// suggest correction link
			echo '<li class="'.$this->optionClass('correct').'">';
			echo $this->Html->link(
				__('Correct',true),
				array(
					"controller" => "suggested_modifications",
					"action" => "add",
					$id
				));
			echo '</li>';
			
			
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
			'show' => array("controller" => "sentences", "action" => "show"),
			'translate' => array("controller" => "sentences", "action" => "translate"),
			'edit' => array("controller" => "sentences", "action" => "edit"),
			'correct' => array("controller" => "suggested_modifications", "action" => "add"),
			'comments' => array("controller" => "sentence_comments", "action" => "show"),
			'logs' => array("controller" => "contributions", "action" => "show")
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