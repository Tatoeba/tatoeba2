<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
?>


<?php
if (isset($translation_text)) {
    if (isset($sentence_id)) {
        echo $javascript->link('sentences.check_translation.js', true);
        echo '<li class="same_language_warning">';
        echo '<span id="same_language_text">' ;
        echo  __(
            'Are you sure you want to translate this sentence '.
            'into a sentence in the same language?',
            true
        );
        echo '</span>';
        echo '<div id="same_language_ok_cancel">';
        echo    '<input id="are_you_sure_submit" type="button" value="OK" />';
        echo    '<input id="are_you_sure_cancel" type="button" value="Cancel" />';
        echo    '<input id="_'.$sentence_id.'_text" type="hidden" value="'.$translation_text.'" />';
        echo '</div>';
        echo'</li>';
    } else {
        echo $javascript->link('jquery.jeditable.js', true);
        echo $javascript->link('sentences.edit_in_place.js', true);
        echo $javascript->link('sentences.change_language.js', true);
            
        echo '<li id="_'.$translation_id.'" class="direct translation">';
            // translation icon
            echo $html->link(
                $html->image(
                    'direct_translation.png',
                    array(
                        "alt"=>__('Show', true),
                        "title"=>__('Show', true)
                    )
                ),
                array(
                    "controller" => "sentences",
                    "action" => "show",
                    $translation_id
                ),
                array("escape"=>false, "class"=>"info")
            );        
            
            // language flag
            $sentences->displayLanguageFlag(
                $translation_id, 
                $translation_lang, 
                true
            );
            
            // sentence text
            echo '<div id="'.$translation_lang.'_'.$translation_id.'" class="editable editableSentence">';
            echo $translation_text;
            echo '</div> ';
        echo "</li>";
    }
}
?>
