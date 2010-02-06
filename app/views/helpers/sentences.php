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

/**
 * Helper to display sentences.
 *
 * @category Sentences
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class SentencesHelper extends AppHelper
{
    public $helpers = array(
        'Html', 'Form', 'Kakasi', 'Javascript', 'Menu', 'Languages'
    );
    
    /**
     * Display a single sentence.
     *
     * @param array $sentence Sentence to display.
     *
     * @return void
     */
    public function displaySentence($sentence)
    {
        echo '<div class="original sentence">';
        // Sentence
        echo '<span class="correctness'.$sentence['correctness'].' '
            .$sentence['lang'].'">';
        echo $this->Html->link(
            $sentence['text'], 
            array("controller" => "sentences", "action" => "show", $sentence['id'])
        );
        echo '</span> ';
        
        $this->_displayRomanization($sentence);
        
        echo '</div>';
    }
        
    /**
     * Display romanization.
     *
     * @param array $sentence Sentence for which to display romanization.
     *
     * @return void
     */
    private function _displayRomanization($sentence)
    {
        if (isset($sentence['romanization'])) {
            echo '<div class="romanization">';
                echo $sentence['romanization'];
            echo '</div>';
        }
    }
    
    /**
     * Display a single sentence for edit in place.
     *
     * @param array $sentence Sentence to display.
     *
     * @return void
     */
    public function displayEditableSentence($sentence)
    {
        echo '<div class="original sentence mine">';
            // info icon
            echo $this->Html->link(
                $this->Html->image('info.png'),
                array(
                    "controller"=>"sentences"
                    , "action"=>"show"
                    , $sentence['id']
                ),
                array("escape"=>false, "class"=>"infoIcon")
            );
            
            // Language flag
            $this->displayLanguageFlag($sentence['id'], $sentence['lang'], true);
            
            // Sentence
            echo '<div id="_'.$sentence['id'].'" 
                class="editable editableSentence'.$sentence['correctness'].'">';
            echo Sanitize::html($sentence['text']);
            echo '</div> ';
            
            $this->_displayRomanization($sentence);
            
        echo '</div>';
    }
    
    /**
     * Display sentence in list.
     *
     * @param array  $sentence         Sentence to display.
     * @param string $translationsLang Language of translation.
     *
     * @return void
     */
    public function displaySentenceInList($sentence, $translationsLang = null)
    {
        // Sentence
        echo '<span id="'.$sentence['lang'].$sentence['id'].'" 
            class="sentenceInList '.$sentence['lang'].'">';
        echo $this->Html->link(
            $sentence['text'], 
            array(
                "controller" => "sentences", 
                "action" => "show", 
                $sentence['id']
            )
        );
        echo '</span> ';
        $this->_displayRomanization($sentence);
        
        // Translations
        if ($translationsLang != null) {
            foreach ($sentence['Translation'] as $translation) {            
                echo '<span id="'.$translationsLang.$translation['id'].'" 
                    class="translationInList '.$translationsLang.'">';
                echo $this->Html->link(
                    $translation['text'], 
                    array(
                        "controller" => "sentences", 
                        "action" => "show", 
                        $translation['id']
                    )
                );
                echo '</span> ';
            }
        }
    }
    
    /**
     * Diplay a sentence and its translations.
     *
     * @param array $sentence             Sentence to display.
     * @param array $translations         Language of translation.
     * @param array $user                 Language of translation.
     * @param array $indirectTranslations Language of translation.
     * @param bool  $inBrowseMode         Language of translation.
     *
     * @return void
     */
    public function displayGroup($sentence, $translations, $user = null, 
        $indirectTranslations = array(), $inBrowseMode = false
    ) {
        echo '<div class="sentence">';
        // Sentence
        $this->Javascript->link('jquery.jeditable.js', false);
        $this->Javascript->link('sentences.edit_in_place.js', false);
        
        $editable = '';
        $editableSentence = '';
        $editableFlag = false;
        $tooltip = __(
            'This sentence does not belong to anyone. 
            If you would like to edit it, you have to adopt it first.', true
        );
       
        if ($user != null) {
            if (isset($user['canEdit']) AND $user['canEdit']) {
                $editable = 'editable';
                $editableSentence = 'editableSentence';
                $editableFlag = true;
            }
            if (isset($user['username']) AND $user['username'] != '') {
                $tooltip = __('This sentence belongs to :', true).' '
                    .$user['username'];
            }
        }
        
        // Original sentence
        echo '<div id="_'.$sentence['id'].'_original" class="original">';
        
        // language flag
        $this->displayLanguageFlag(
            $sentence['id'], $sentence['lang'], $editableFlag
        );
        
        // sentence text
        if ($inBrowseMode) {
            // TODO : HACK SPOTTED id is made of lang + id
            // and then is used in edit_in_place 
            echo '<div id="'.$sentence['lang'].'_'.$sentence['id'].'" 
                class="'.$editable.' '.$editableSentence.'" 
                title="'.$tooltip.'">'
                .Sanitize::html($sentence['text']).'</div> ';
        } else {
            echo $this->Html->link(
                $sentence['text'],
                array(
                    "controller"=>"sentences",
                    "action"=>"show",
                    $sentence['id']
                )
            );
        }
        
        // romanization
        $this->_displayRomanization($sentence);
        
        echo '</div>';

        echo "\n";
        
        // To add new translations
        echo '<ul id="translation_for_'.$sentence['id'].'" 
            class="addTranslations"><li></li></ul>';
        
        // Translations
        echo '<ul id="_'.$sentence['id'].'_translations" class="translations">';
           echo '<li></li>';
        if (count($translations) > 0) {
            // direct translations
            $this->_displayTranslations($translations);
            
            // indirect translations
            $this->_displayIndirectTranslations($indirectTranslations);
        }
        echo '</ul>';
        
        echo '</div>';

        echo "\n";
    }
     
    /**
     * Display direct translations.
     *
     * @param array $translations Translations to display.
     *
     * @return void
     */
    private function _displayTranslations($translations)
    {
        foreach ($translations as $translation) {
        
            echo '<li class="direct translation">';
            // translation icon
            echo $this->Html->link(
                $this->Html->image(
                    'direct_translation.png',
                    array(
                        "alt"=>__('Show', true),
                        "title"=>__('Show', true)
                    )
                ),
                array(
                    "controller" => "sentences",
                    "action" => "show",
                    $translation['id']
                ),
                array("escape"=>false, "class"=>"info")
            );
            
            // language flag
            $this->displayLanguageFlag($translation['id'], $translation['lang']);
            
            //translation and romanization
            // translation text
            echo '<div >';
            echo $this->Html->link(
                $translation['text'],
                array(
                    "controller" => "sentences",
                    "action" => "show",
                    $translation['id']
                ),
                array("escape"=>false)
            );
            echo '</div>';

            $this->_displayRomanization($translation);
            
            echo '</li>';
        }
    }
    
    /**
     * Display indirect translations, that is to say translations of translations.
     *
     * @param array $indirectTranslations Indirect translations to display.
     *
     * @return void
     */
    private function _displayIndirectTranslations($indirectTranslations)
    {
        if (count($indirectTranslations) > 0) {
            foreach ($indirectTranslations as $translation) {
                echo '<li class="indirect translation">';
                
                // translation icon
                echo $this->Html->link(
                    $this->Html->image(
                        'indirect_translation.png',
                        array(
                            "alt"=>__('Show', true),
                            "title"=>__('Show', true)
                        )
                    ),
                    array(
                        "controller" => "sentences",
                        "action" => "show",
                        $translation['id']
                    ),
                    array("escape"=>false, "class"=>"info")
                );
                
                // language flag
                $this->displayLanguageFlag(
                    $translation['id'], $translation['lang']
                );
                
                // translation text
                echo '<div title="'.__('indirect translation', true).'">';
                echo $this->Html->link(
                    $translation['text'],
                    array(
                        "controller" => "sentences",
                        "action" => "show",
                        $translation['id']
                    ),
                    array("escape"=>false)
                );
                echo '</div>';
                
                echo '</li>';
            }
        }
    }
    
    /**
     * Sentences options (translate, edit, correct, comments, logs, edit, etc).
     *
     * @param int    $id             Id of the sentence. 
     * @param string $lang           Language of the sentence.
     * @param array  $specialOptions Options for the sentence.
     * @param int    $score          Score of the sentence. Used for search results.
     *
     * @return void
     */
    public function displayMenu($id, $lang, $specialOptions, $score = null)
    {
        if ($lang == '') {
            $lang = 'und';
        }
        echo '<ul class="menu" id="_'. $id .'" lang="'.$lang.'">';
        // score
        if ($score != null) {
            echo '<li class="score">';
            echo intval($score * 100);
            echo '%';
            echo '</li>';
        }
        
        // owner
        if (isset($specialOptions['belongsTo'])) {
            echo '<li class="belongsTo">';
            $user = $this->Html->link(
                $specialOptions['belongsTo'],
                array(
                    "controller" => "user",
                    "action" => "profile",
                    $specialOptions['belongsTo']
                )
            );
            echo sprintf(__('belongs to %s', true), $user);
            echo '</li>';
        }
        
        // translate
        if ($specialOptions['canTranslate']) {
            $this->Javascript->link('sentences.add_translation.js', false);
            $this->Menu->translateButton();
        }
        
        // adopt
        if (isset($specialOptions['canAdopt']) 
            AND $specialOptions['canAdopt'] == true
        ) {
            $this->Menu->adoptButton($id);
            echo "\n";
        }
        
        // let go
        if (isset($specialOptions['canLetGo']) 
            AND $specialOptions['canLetGo'] == true
        ) {
            $this->Menu->letGoButton($id);
            echo "\n";
        }
        
        // favorite
        if (isset($specialOptions['canFavorite']) 
            AND $specialOptions['canFavorite'] == true
        ) {
            $this->Javascript->link('favorites.add.js', false);
            $this->Menu->favoriteButton($id);
            echo "\n";
        }
        
        // unfavorite
        if (isset($specialOptions['canUnFavorite']) 
            AND $specialOptions['canUnFavorite'] == true
        ) {
            $this->Javascript->link('favorites.add.js', false);
            $this->Menu->unfavoriteButton($id);
            echo "\n";
        }
        
        // add to list
        if (isset($specialOptions['canAddToList']) 
            AND $specialOptions['canAddToList'] == true
        ) {
            $this->Javascript->link('sentences_lists.menu.js', false);
            $this->Javascript->link('jquery.impromptu.js', false);
            $lists = $this->requestAction('/sentences_lists/choices'); 
            // TODO Remove requestAction someday
            
            $this->Menu->addToListButton();
            
            echo '<li style="display:none" class="addToList'.$id.'">';
                
            // select list
            echo '<select class="listOfLists" id="listSelection'.$id.'">';
            echo '<option value="-1">';
            __('Add to new list...');
            echo '</option>';
            
            echo '<option value="-2">';
            __('Manage lists...');
            echo '</option>';
            
            echo '<option value="0">--------------</option>';
            
            // user's lists
            foreach ($lists as $list) {
                $belongsToList = !in_array(
                    $list['SentencesList']['id'],
                    $specialOptions['belongsToLists']
                );
                if ($belongsToList AND !$list['SentencesList']['is_public']) {
                    echo '<option value="'.$list['SentencesList']['id'].'">';
                    echo $list['SentencesList']['name'];
                    echo '</option>';
                }
            }

            echo '<option value="0">--------------</option>';
            
            // public lists
            foreach ($lists as $list) {
                $belongsToList = !in_array(
                    $list['SentencesList']['id'],
                    $specialOptions['belongsToLists']
                );
                if ($belongsToList AND $list['SentencesList']['is_public']) {
                    echo '<option value="'.$list['SentencesList']['id'].'">';
                    echo $list['SentencesList']['name'];
                    echo '</option>';
                }
            }
            
            echo '</select>';
            
            // ok button
            echo '<input type="button" value="ok" class="addToListButton" />';
            
            echo '</li>';
            echo "\n";
        }
        
        // delete
        if (isset($specialOptions['canDelete']) 
            AND $specialOptions['canDelete'] == true
        ) {
            $this->Menu->deleteButton($id);
        }
        
        echo "<li>";
        echo $this->Html->image(
            'loading-small.gif', 
            array("id"=>"_".$id."_in_process", "style"=>"display:none")
        );
        echo $this->Html->image(
            'valid_16x16.png', 
            array("id"=>"_".$id."_valid", "style" =>"display:none")
        );
        echo "</li>";
            
        echo '</ul>';
    }
    
    /**
     * Language flag.
     *
     * @param int    $id       Id of the sentence.
     * @param string $lang     Language of the sentence.
     * @param bool   $editable Set to true of flag can be changed.
     *
     * @return void
     */
    public function displayLanguageFlag($id, $lang, $editable = false)
    {
        if ($lang == '') {
            $lang = 'unknown_lang';
        }
        
        $class = '';
        if ($editable) {
            $this->Javascript->link('sentences.change_language.js', false);
            $class = 'editableFlag';
            
            // language select
            $langArray = $this->Languages->languagesArray();
            asort($langArray);
            echo $this->Form->select(
                'selectLang_'.$id,
                $langArray,
                'und',
                array("class"=>"selectLang"),
                false
            );
        }
        
        echo $this->Html->image(
            'flags/'.$lang.'.png',
            array("class" => "languageFlag ".$class)
        );
        
    }
}
?>
