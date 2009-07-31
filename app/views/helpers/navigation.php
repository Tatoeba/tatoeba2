<?php
class NavigationHelper extends AppHelper{
	var $helpers = array('Html', 'Form', 'Languages', 'Javascript', 'Session');
	
	function displaySentenceNavigation($currentId = null){
		$controller = $this->params['controller'];
		$action = $this->params['action'];
		$input = $this->params['pass'][0];
		if($currentId == null){
			$currentId = intval($this->params['pass'][0]);
		}
		
		echo '<div class="navigation">';
			// go to form
			echo $this->Form->create('Sentence', array("action" => "goTo", "type" => "get"));
			echo $this->Form->input('sentence_id', array("label" => __('Show sentence nº : ', true), "value" => $input));
			echo $this->Form->end(__('OK',true));
			echo '<ul>';
			
			// previous
			echo '<li class="option">';
			echo $this->Html->link(
				'<< '.__('previous',true), 
				array(
					"controller" => $controller,
					"action" => $action,
					$currentId-1
				)
			);
			echo '</li>';
			
			// next
			echo '<li class="option">';
			echo $this->Html->link(
				__('next',true).' >>', 
				array(
					"controller" => $controller,
					"action" => $action,
					$currentId+1
				)
			);
			echo '</li>';
			
			// random
			$this->Javascript->link('sentences.random.js', false);
			$langArray = $this->Languages->languagesArray();
			asort($langArray);
			$selectedLanguage = $this->Session->read('random_lang_selected');
			array_unshift($langArray, array('any' => __('any', true)));
			
			echo '<li class="option random">';
			echo $this->Html->link(
				__('random', true), 
				array(
					"controller" => "sentences",
					"action" => "show",
					$selectedLanguage
				),
				array("id" => "randomLink", "lang" => $this->params['lang'])
			);
			echo $this->Form->select("randomLangChoiceInBrowse", $langArray, $selectedLanguage, null, false);
			echo '</li>';
			
			
			echo '</ul>';
			
		echo '</div>';
	}
	
	function displayUsersNavigation($currentId, $username = null){
		echo '<div class="navigation">';
			if($username == null) $username = '';
			echo $this->Form->create('User', array("action" => "search"));
			echo $this->Form->input('username', array("label" => __('Enter a username : ',true), "value" => $username));
			echo $this->Form->end(__('show user',true));
			
			echo '<ul>';
			
			// random
			echo '<li class="option">';
			echo $this->Html->link(
				__('random',true), 
				array(
					"controller" => "users",
					"action" => "show",
					"random"
				)
			);
			echo '</li>';
			
			// next
			echo '<li class="option">';
			echo $this->Html->link(
				__('all',true), 
				array(
					"controller" => "users",
					"action" => "all"
				)
			);
			echo '</li>';
			
			echo '</ul>';
			
		echo '</div>';
	}
}
?>