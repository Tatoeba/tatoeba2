<?php
if($sentence != null){
	$this->pageTitle = __('Example sentence : ',true) . $sentence['Sentence']['text'];

	// navigation (previous, random, next)
	$navigation->displaySentenceNavigation();
	
	echo '<div class="sentences_set">';
		// sentence menu (translate, edit, comment, etc)
		$specialOptions['belongsTo'] = $sentence['User']['username']; // TODO set up a better mechanism
		$sentences->displayMenu($sentence['Sentence']['id'], $sentence['Sentence']['lang'], $specialOptions);

		// sentence and translations
		$t = (isset($sentence['Translation'])) ? $sentence['Translation'] : array();
		$sentence['User']['canEdit'] = $specialOptions['canEdit']; // TODO set up a better mechanism
		$sentences->displayGroup($sentence['Sentence'], $t, $sentence['User']);
	echo '</div>';
	
	//$tooltip->displayAdoptTooltip(); 
	
	echo '<script type="text/javascript">
	$(document).ready(function(){
		$(".translations").html("<div class=\"loading\">'.addslashes($html->image('loading.gif')).'</div>");
		$(".translations").load("http://" + self.location.hostname + "/sentences/get_translations/'.$sentence['Sentence']['id'].'");
	});
	</script>';
	
}else{
	$this->pageTitle = __('Sentence does not exist : ', true) . $this->params['pass'][0];
	
	// navigation (previous, random, next)
	$navigation->displaySentenceNavigation('random');
	
	echo '<div class="error">';
	__('There is no sentence with id ');
	echo $this->params['pass'][0];
	echo '</div>';
}
?>