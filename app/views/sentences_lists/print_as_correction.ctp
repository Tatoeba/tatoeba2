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

if (isset($list)) {

    $this->pageTitle = __('Tatoeba list :', true) . ' ' . 
        $list['SentencesList']['name'];
     
    echo '<h1>';
    echo $list['SentencesList']['name'];
    echo '</h1>';
    
} else {
    
    echo '<h1>';
    __('Choose language of translations');
    echo '</h1>';
    
}

if (isset($translationsLang)) {
    ?>
    <ol id="listAsCorrection">
    <?php
    $displayRomanization = ($romanization == 'display_romanization');
    
    foreach ($list['Sentence'] as $sentence) {
        echo '<li>';
        echo '<div class="original">';
            echo '<span class="sentence">'.$sentence['text'].'</span>';
            if ($displayRomanization AND isset($sentence['romanization'])) {
                echo '<div class="romanization">';
                echo $sentence['romanization'];
                echo '</div>';
            }
        echo '</div>';
        
        foreach ($sentence['Translation'] as $translation) {            
            echo '<div class="translation">';
                echo '<div class="text">'.$translation['text'].'</div>';
                if ($displayRomanization AND isset($translation['romanization'])) {
                    echo '<div class="romanization">';
                    echo $translation['romanization'];
                    echo '</div>';
                }
            echo '</div>';
        }
        
        echo '</li>';
    }
    ?>
    </ol>
<?php
} else {

    $javascript->link('jquery.js', false);    
    
    echo '<p>';
    __('You have to specify the language of the translations :'); echo ' ';
    
    //2* TODO => see edit.ctp , javascript hidden inside the html +
    //  hackish use of the "languagesArray"

    $path  = '/' . Configure::read('Config.language') . 
        '/sentences_lists/print_as_correction/' . $listId . '/';
    echo $form->select(
        "translationLangChoice",
        $languages->languagesArray(),
        null,
        array(
            "onchange" => "$(location).attr('href', '".
                $path."' + this.value + '/".$romanization."');"
        ),
        false
    ); 
    echo '</p>';
    
    echo '<p>';
    __(
        'This version enables you to have a printable version of the list with '.
        'translations in the language of your choice.'
    );
    echo '</p>';
    
    echo '<p>';
    echo sprintf(
        __(
            'It is practical, for instance, in the case you want to practice '.
            '<a href="%s">translating on paper</a> and would like also to have '.
            'the correction printed somewhere on paper, to check if you have '.
            'translated properly.', true
        ), 
        $html->url(
            array(
                "controller"=>"sentences_lists",
                "action"=>"print_as_exercise",
                $listId
            )
        )
    );
    echo '</p>';    
    
    echo '<p>';
    __(
        'WARNING : It is possible that the sentences do not have translations in '.
        'the language you will specify.'
    );
    echo '</p>';
    
}
?>
