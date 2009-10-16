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
?>

<div id="second_modules">
	<div class="module">
		<h2>Mon espace</h2>
		<?php
			if(!$session->read('Auth.User.id')){
				echo $this->element('login'); 
			} else {
				echo $this->element('space'); 
			}
		?>
	</div>

</div>

<div id="main_modules">
	<div class="module">
	<?php
	// navigation (previous, random, next)
	$navigation->displaySentenceNavigation();
		
	if($sentenceExists){
	
		$this->pageTitle = __('Logs for : ',true) . $sentence['Sentence']['text'];
	
		echo '<div class="sentences_set">';
			// sentence menu (translate, edit, comment, etc)
			$specialOptions['belongsTo'] = $sentence['User']['username']; // TODO set up a better mechanism
			$sentences->displayMenu($sentence['Sentence']['id'], $sentence['Sentence']['lang'], $specialOptions);
	
			// sentence and translations
			$sentence['User']['canEdit'] = $specialOptions['canEdit']; // TODO set up a better mechanism
			$sentences->displayGroup($sentence['Sentence'], $sentence['Translation'], $sentence['User']);
		echo '</div>';	
	
	
		$contributions = $sentence['Contribution'];
		
	}else{
		
		$this->pageTitle = __('Logs for sentence nº',true) . $this->params['pass'][0];
		
		echo '<em>';
		__('The sentence has been deleted');
		echo '</em>';
	}
	
	echo '<h2>';
	__('Logs'); 
	echo ' ';
	$tooltip->displayLogsColors();
	echo '</h2>';
	
	if(count($contributions) > 0){
		echo '<table id="logs">';
		foreach($contributions as $contribution){
			if($sentenceExists){
				$logs->entry($contribution, $contribution['User']);
			}else{
				$logs->entry($contribution['Contribution'], $contribution['User']);
			}
		}
		echo '</table>';
	}else{
		echo '<em>'. __('There is no log for this sentence', true) .'</em>';
	}
	?>
	</div>
</div>
<?php

