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
