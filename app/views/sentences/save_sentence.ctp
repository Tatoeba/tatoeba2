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

<?php
// this is for edit in place
if(isset($sentence_text)){
	
	echo rtrim($sentence_text);

}

// this is when adding new sentences
elseif(isset($sentence)){
	
	echo $javascript->link('sentences.add_translation.js', true);
	
	echo '<div class="sentences_set freshlyAddedSentence">';
	// sentence menu (translate, edit, comment, etc)
	$sentences->displayMenu($sentence['Sentence']['id'], $sentence['Sentence']['lang'], $specialOptions);
	
	// sentence and translations
	$sentences->displayGroup($sentence['Sentence'], array(), $session->read('Auth.User'));
	echo '</div>';

}
?>