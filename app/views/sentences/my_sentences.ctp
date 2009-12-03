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

<div id="annexe_content">
	<div class="module">
	<h2><?php __('Tips'); ?></h2>
	<p><?php __('You can edit your sentences by clicking on them.'); ?></p>
	<p><?php __('You can change the language of a sentence by clicking on the flag.'); ?></p>
	</div>
</div>
	
	
<div id="main_content">
	<div class="module">
		<?php
		$javascript->link('jquery.jeditable.js', false);
		$javascript->link('sentences.edit_in_place.js', false);
		$javascript->link('sentences.change_language.js', false);
		
		foreach($user_sentences as $sentence){
			$sentences->displayEditableSentence($sentence['Sentence']);
		}
		?>

	</div>
</div>
