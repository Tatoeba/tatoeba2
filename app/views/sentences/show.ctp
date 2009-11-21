<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

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


// navigation (previous, random, next)
$navigation->displaySentenceNavigation();
?>

<div id="annexe_content">
	
	<div class="module">
		<?php
		echo '<h2>';
		__('Logs');
		echo '</h2>';
		
		$contributions = $sentence['Contribution'];
		if(count($contributions) > 0){
			echo '<div id="logs">';
			foreach($contributions as $contribution){
				$logs->annexeEntry($contribution, $contribution['User']);
			}
			echo '</div>';
		}else{
			echo '<em>'. __('There is no log for this sentence', true) .'</em>';
		}
		?>
	</div>	
	
</div>

<div id="main_content">
	<div class="module">
		<?php
		if($sentence != null){
			echo '<h2>' . __('Sentence nº', true) . $sentence['Sentence']['id'] . '</h2>';
			$this->pageTitle = __('Example sentence : ',true) . $sentence['Sentence']['text'];

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
			
			echo '<h2>' . __('Sentence nº', true) . $this->params['pass'][0]. '</h2>';

			echo '<div class="error">';
			__('There is no sentence with id ');
			echo $this->params['pass'][0];
			echo '</div>';
		}
		?>
	</div>

	<div class="module">
		<?php
		echo '<div class="addComment">';
		echo $html->link(
			__('Add a comment',true),
			array("controller" => "sentence_comments", "action" => "show", $sentence['Sentence']['id'].'#add_comment')
		);
		echo '</div>';			
		
		echo '<h2>';
		__('Comments');
		echo '</h2>';

		if(count($sentence['SentenceComment']) > 0){
			echo '<ol class="comments">';
			for($i = 0; $i < 3 AND $i < count($sentence['SentenceComment']); $i++){
				$comment = $sentence['SentenceComment'][$i];
				$comments->displaySentenceComment($comment);
			}
			echo '</div>';
			
			if(count($sentence['SentenceComment']) > 3){
				?>
				<p class="more_link">
				<?=$html->link(
					__('See all comments',true),
					array(
						"controller" => "sentence_comments",
						"action" => "show",
						$sentence['Sentence']['id']
					)); 
				?>
				</p>
				<?php
			}
		}else{
			echo '<em>' . __('There are no comments for now.', true) .'</em>';
		}
		echo '</div>';
		?>
		
		
	</div>
	
</div>

