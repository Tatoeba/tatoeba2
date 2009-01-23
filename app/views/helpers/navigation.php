<?php
class NavigationHelper extends AppHelper{
	var $helpers = array('Html', 'Form');
	
	function displaySentenceNavigation($currentId){
		$action = ($this->params['action'] == 'translate') ? 'translate' : 'show';
		
		echo '<div class="navigation">';
			$input = $this->params['pass'][0];
			echo $this->Form->create('Sentence', array("action" => "goTo", "type" => "get"));
			echo $this->Form->input('sentence_id', array("label" => __('Show sentence nº : ', true), "value" => $input));
			echo $this->Form->end(__('OK',true));
			echo '<ul>';
			
			// previous
			echo '<li class="option">';
			echo $this->Html->link(
				'<< '.__('previous',true), 
				array(
					"controller" => "sentences",
					"action" => $action,
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
					"action" => $action,
					"random"
				)
			);
			echo '</li>';
			
			// next
			echo '<li class="option">';
			echo $this->Html->link(
				__('next',true).' >>', 
				array(
					"controller" => "sentences",
					"action" => $action,
					$currentId+1
				)
			);
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
			
			// previous
			echo '<li class="option">';
			echo $this->Html->link(
				'<< '.__('previous',true), 
				array(
					"controller" => "users",
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
					"controller" => "users",
					"action" => "show",
					"random"
				)
			);
			echo '</li>';
			
			// next
			echo '<li class="option">';
			echo $this->Html->link(
				__('next',true).' >>', 
				array(
					"controller" => "users",
					"action" => "show",
					$currentId+1
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