<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
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

<div id="annexe_content">
	
	<div class="module">
	<h2><?php __('Languages'); ?></h2>
	<p><?php __('Comments are grouped by languages.'); ?></p>
	<ul>
		<?php
		foreach($sentenceComments as $lang => $commentsInLang){
			if($lang != 'unknown'){
				$item = $languages->codeToName($lang);
			}else{
				$item = __('Other languages', true);
			}
			echo '<li>' . $html->link($item, '#'.$lang) . '</li>';
		}
		?>
	</ul>
	<p><?php __('NOTE : Since the language of the comments is auto-detected, you may find certain comments in the wrong category.') ?></p>
	</div>
</div>

<div id="main_content">
	<div class="module">
		<?php
		foreach($sentenceComments as $lang => $commentsInLang){
			echo '<a name="'.$lang.'"></a>';
			if($lang != 'unknown'){
				echo '<h2>'.$languages->codeToName($lang).'</h2>';
			}else{
				echo '<h2>'.__('Other languages', true).'</h2>';
			}

			if(count($commentsInLang) > 0){
				echo '<ol class="comments">';
				foreach($commentsInLang as $comment){
					$comments->displaySentenceComment($comment, true);
				}
				echo '</ol>';
			}else{
				__('There are no comments in this language');
			}
		}
		?>
	</div>
</div>


