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


App::import('Core', 'Sanitize');

if (isset($this->params['lang'])) {
	Configure::write('Config.language',  $this->params['lang']);
}
?>
<div class="search_bar_container">
<div class="search_bar">

<?php

$languages = $languages->getSearchableLanguagesArray();

$selectedLanguageFrom = $session->read('search_from');
$selectedLanguageTo = $session->read('search_to');
$searchQuery = $session->read('search_query');
Sanitize::html($searchQuery);
$searchQuery = htmlentities($searchQuery);


// TODO HACK SPOTTED : in fact the array should be sort in the languagesArray method and "all languages" set as first
// and 
if($selectedLanguageFrom == null){
    $selectedLanguageFrom = 'und' ;
}

if($selectedLanguageTo == null){
    $selectedLanguageTo = 'und' ;
}
echo $form->create('Sentence', array("action" => "search", "type" => "get"));

echo '<fieldset class="select">';
echo '<label>' . __('From',true) . '</label>';
echo $form->select('from', $languages, $selectedLanguageFrom,null ,false);
echo '</fieldset>';

echo '<fieldset class="into">';
echo '<span id="into">&raquo;</span>';
echo '</fieldset>';
	
echo '<fieldset class="select">';
echo '<label>' . __('To',true) . '</label>';
echo $form->select('to', $languages, $selectedLanguageTo,null,false);
echo '</fieldset>';

echo '<fieldset class="input text">';
echo '<label for="SentenceQuery">'. __('Example sentences with the words :',true) .'</label>';
echo '<input id="SentenceQuery" type="text" value="'.$searchQuery .'" name="query"/>';
echo '</fieldset>';

echo '<fieldset class="submit">';
echo '<input type="submit" value="'.__('search',true).'"/>';
echo '</fieldset>';

echo '<fieldset class="help">';
echo $html->link('[?]', array("controller" => "sentences", "action" => "search"));
echo '</fieldset>';

echo $form->end();

?>
</div>

<div class="search_bar_left"></div>
<div class="search_bar_right"></div>
</div>
