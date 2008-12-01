<?php
class SentencesHelper extends AppHelper {

	var $helpers = array('Html', 'Form');
	
	/**
	 * $sentence : array("id" => int, "lang" => string, "text" => string)
	 * $translations : array ( $sentence )
	 * $canDelete : boolean
	 * $canComment : boolean
	 * $isReturned : boolean
	 */
	function displayGroup($sentence, $translations, $canComment = false, $canDelete = false) {
		echo '<div class="sentences_set">';
			
			// Menu
			echo '<ul class="menu">';
				echo '<li class="id">';
					echo '<strong>' . $sentence['id'] . '</strong> <em>' . $sentence['lang'] . ' ('. $sentence['correctness'] .')</em>';
				echo '</li>';
				
				$this->displayMenu($sentence);
			echo '</ul>';
			
			
			echo '<ul class="sentence translations">';
				// Sentence
				echo '<li class="original">'.$sentence['text'].'</li>';
				
				if(count($translations) > 0){
					// Translations
					foreach($translations as $translation){
						echo '<li class="direct translation">';
							echo '<em>'.$translation['lang'].'</em>';
							echo $translation['text'];
						echo '</li>';
					}
				}else{
					echo '<li>';
					echo '<em>';
					__('There are no translations for now.');
					echo '</em> ';
					echo $this->Html->link(
						__('Add a translation',true),
						array(
							"controller" => "sentences",
							"action" => "translate",
							$sentence['id']
						));
					echo '</li>';
				}
			echo '</ul>';
			
		echo '</div>';
    }
	
	function displayForTranslation($sentence, $translations, $canComment = false, $canDelete = false){
		echo '<div class="sentences_set">';
			
			// Menu
			echo '<ul class="menu">';
				echo '<li class="id">';
					echo '<strong>' . $sentence['id'] . '</strong> <em>' . $sentence['lang'] . ' ('. $sentence['correctness'] .')</em>';
				echo '</li>';
				
				$this->displayMenu($sentence);
			echo '</ul>';
			
			
			echo '<ul class="sentence translations">';
				// Sentence
				echo '<li class="original">'.$sentence['text'].'</li>';				
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
						echo '<li class="direct translation">';
							echo '<em>'.$translation['lang'].'</em>';
							echo $translation['text'];
						echo '</li>';
					}
				}
			echo '</ul>';
		echo '</div>';
	}
	
	
	function displayMenu($sentence, $canComment = false, $canDelete = false){
		// edit link
		echo '<li class="option">';
		echo $this->Html->link(
			__('Edit',true),
			array(
				"controller" => "sentences",
				"action" => "edit",
				$sentence['id']
			));
		echo '</li>';
		
		// translate link
		echo '<li class="option">';
		echo $this->Html->link(
			__('Translate',true),
			array(
				"controller" => "sentences",
				"action" => "translate",
				$sentence['id']
			));
		echo '</li>';

		// suggest correction link
		echo '<li class="option">';
		echo $this->Html->link(
			__('Correct',true),
			array(
				"controller" => "suggested_modifications",
				"action" => "add",
				$sentence['id']
			));
		echo '</li>';
		
		if($canComment){
			// discuss link
			echo '<li class="option">';
			echo $this->Html->link(
				__('Comment',true),
				array(
					"controller" => "sentence_comments",
					"action" => "add",
					$sentence['id']
				));
			echo '</li>';
		}
		
		if($canDelete){
			// delete link
			echo '<li class="option">';
			echo $this->Html->link(
				__('Delete',true), 
				array(
					"controller" => "sentences",
					"action" => "delete",
					$sentence['id']
				), 
				null, 
				'Are you sure?');
			echo '</li>';
		}
	}
	
	function displayNavigation($currentId){
		echo '<div class="navigation">';
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