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
        'Html',
        'Form',
        'Javascript',
        'SentenceButtons',
        'Languages',
        'Session',
        'Pinyin'
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
        // Language flag
            $this->SentenceButtons->displayLanguageFlag(
                $sentence['id'], $sentence['lang'], false
            );
            
        // Sentence
        echo '<span class="correctness'.$sentence['correctness'].' '
            .$sentence['lang'].'">';
        echo $this->Html->link(
            $sentence['text'], 
            array(
                "controller" => "sentences",
                "action" => "show",
                $sentence['id']
            ),
            array(
                'id' => '_'.$sentence['id']
            )
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
            $romanization = 'hophop hophpo hophopoh'; //$sentence['romanization'];
            
            if ($sentence['lang'] == 'jpn') {
                
                $title = 'ROMAJI: '.$sentence['romaji']."\n\n ";
                $title .= __(
                    'WARNING : this is automatically generated '.
                    'and is not always reliable. Click to learn more.', true
                );
                echo $this->Html->link(
                    $romanization,
                    'http://blog.tatoeba.org/2010/04/japanese-romanization-in-tatoeba-now.html',
                    array(
                        'class' => 'romanization',
                        'title' => $title
                    )
                );
                
            } else {
            
                echo '<div class="romanization">';
                if ($sentence['lang'] === 'cmn') {
                    echo $this->Pinyin->numeric2diacritic(
                        $romanization             
                    );
                } else {
                    echo $romanization;
                }
                echo '</div>';
                
            }
        }
    }

    /**
     * display alternate script (traditional / simplfied)
     * for chinese sentence
     *
     * @param array $sentence Sentence for which to display alternate script 
     *
     * @return void
     */
    private function _displayAlternateScript($sentence)
    {
        if (isset($sentence['alternateScript'])) {
            ?>
            <div class="romanization">
            <?php echo $sentence['alternateScript']; ?>
            </div>
        <?php
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
        ?>
        <div class="original sentence mine">

            <?php
            // info icon
            $this->SentenceButtons->displayInfoButton($sentence['id']);
            
            // Language flag
            $this->SentenceButtons->displayLanguageFlag(
                $sentence['id'], $sentence['lang'], true
            );
            
            // Sentence
            echo '<div id="'.$sentence['lang'].'_'.$sentence['id'].'" 
                class="editable editableSentence">';
            echo Sanitize::html($sentence['text']);
            echo '</div> ';
            
            $this->_displayRomanization($sentence);
            $this->_displayAlternateScript($sentence);
            ?>    
        </div>
        <?php
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
        $this->_displayAlternateScript($sentence);
        
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
     * @param array $translations         Translations of the sentence.
     * @param array $user                 Owner of the sentence.
     * @param array $indirectTranslations Indirect translations of the sentence.
     *
     * @return void
     */
    public function displayGroup(
        $sentence,
        $translations,
        $user = null,
        $indirectTranslations = array()
    ) {
        echo '<div class="toto">';
        
        $editable = '';
        $editableFlag = false;
        $tooltip = __(
            'This sentence does not belong to anyone. '.
            'If you would like to edit it, you have to adopt it first.', true
        );
        
        if ($user != null) {
            if (isset($user['canEdit']) && $user['canEdit']) {
                $editable = 'editable editableSentence';
                $editableFlag = true;
            }
            if (isset($user['username']) && $user['username'] != '') {
                $tooltip = __('This sentence belongs to :', true).' '
                    .$user['username'];
            }
        }
        
        
        $this->_displaySentenceWithButtons($sentence, $user['username']);
        
        
        $id = $sentence['id'];
        
        // Loading animation
        echo $this->Html->image(
            'loading.gif',
            array(
                "id" => "_".$id."_loading",
                "class" => "loading"
            )
        );
        echo "\n";
        
        // add a new translation
        $this->_displayNewTranslationForm($id);
        
        // Translations
        echo '<ul id="_'.$sentence['id'].'_translations" class="translations">';
           echo '<li></li>';
        if (count($translations) > 0) {
            // direct translations
            $this->_displayTranslations(
                $translations, $sentence['id']
            );
            
            // indirect translations
            $this->_displayIndirectTranslations(
                $indirectTranslations, $sentence['id']
            );
        }
        echo '</ul>';
        
        echo '</div>';

        echo "\n";
    }
     
    /**
     * Display direct translations.
     *
     * @param array $translations Translations to display.
     * @param int   $originalId   Id of the original sentence.
     *
     * @return void
     */
    private function _displayTranslations($translations, $originalId)
    {
        
        foreach ($translations as $translation) {
            $canUnlink = CurrentUser::canLinkAndUnlink($originalId);
            
            echo '<li class="direct translation">';
            // unlink button
            if ($canUnlink) {
                $this->SentenceButtons->unlinkButton(
                    $originalId, $translation['id']
                );
            }
            
            // goto button
            $this->SentenceButtons->translationShowButton(
                $translation['id'], 'direct'
            );
            
            
            // audio
            $this->SentenceButtons->audioButton(
                $translation['id'], $translation['lang']
            );
            
            // language flag
            $this->SentenceButtons->displayLanguageFlag(
                $translation['id'], $translation['lang']
            );
            
            //translation and romanization
            // translation text
            echo '<div >';
            echo $this->Html->link(
                $translation['text'],
                array(
                    "controller" => "sentences",
                    "action" => "show",
                    $translation['id']
                )
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
     * @param int   $originalId           Id of the original sentence.
     *
     * @return void
     */
    private function _displayIndirectTranslations(
        $indirectTranslations, $originalId
    ) {
        if (count($indirectTranslations) > 0) {
            
            foreach ($indirectTranslations as $translation) {
                $canLink = CurrentUser::canLinkAndUnlink($originalId);
                
                echo '<li class="indirect translation">';
                // unlink button
                if ($canLink) {
                    $this->SentenceButtons->linkButton(
                        $originalId, $translation['id']
                    );
                }
                
                // goto button
                $this->SentenceButtons->translationShowButton(
                    $translation['id'], 'indirect'
                );
                
                // audio
                $this->SentenceButtons->audioButton(
                    $translation['id'], $translation['lang']
                );
                
                // language flag
                $this->SentenceButtons->displayLanguageFlag(
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
                    )
                );
                echo '</div>';
                
                $this->_displayRomanization($translation);
                
                echo '</li>';
            }
        }
    }
    

    /**
     * display the form to translate a sentence
     * appear when clicking on the translate icon
     *
     * @param int $id The id of the sentence to translate.
     *
     * @return void
     */

    private function _displayNewTranslationForm($id)
    {
        
        $langArray = $this->Languages->translationsArray();
        $preSelectedLang = $this->Session->read('contribute_lang');
        if (empty($preSelectedLang)) {
            $preSelectedLang = 'auto';
        }

        // To add new translations
        echo '<ul id="translation_for_'.$id.'" class="addTranslations">';
        echo '<li class="direct">';
        echo '<input '.
            'id="_'.$id.'_text" '.
            'class="addTranslationsTextInput" '.
            'type="text" value="" '.
        '/>';
        // language select
        echo $this->Form->select(
            'translationLang_'.$id,
            $langArray,
            $preSelectedLang,
            array("class"=>"translationLang"),
            false
        );
        echo '<input id="_'.$id.'_submit" type="button"' 
            .   ' value="'.__('Submit translation', true).'" />';
        echo  '<input id="_'.$id.'_cancel" type="button"'
            .   ' value="'.__('Cancel', true).'"/>';
        echo '</li>';
        echo '<li class="important">'
            . __(
                'Important! You are about to add a translation to the sentence '
                . 'above. If you do not understand this sentence, click on '
                . '"Cancel" to display everything again, and then click on '
                . 'the sentence that you understand and want to translate from.',
                true
            );
        echo '</li>';
        echo '</ul>';
    }
    
    /**
     * ====================================================================
     */
    
    /**
     * Displays the main sentence. The main sentence is composed of a sentence and a 
     * menu of action that can be applied on this sentence. This is the sentence at 
     * the top.
     *
     * @param array  $sentence  Sentence data.
     * @param string $ownerName Name of the owner of the sentence.
     *
     * @return void
     */
    public function displayMainSentence($sentence, $ownerName){
    }
    
    
    /**
     * Displays the generic version of a sentence. This is used to display direct and
     * indirect translations as well (by definition, a translation is a sentence).
     * The generic sentence contains :
     *  - the sentence content (text, romanization, alternate Chinese script)
     *  - the navigation button (clicking on it leads to the "Browse" section)
     *  - the language flag
     *  - the audio button
     *  - the link/unlink buttons
     *
     * @param array  $sentence  Sentence data.
     * @param string $ownerName Name of the owner of the sentence.
     * @param int    $parentId  Id of the parent sentence, if type is 'translation'.
     *
     * @return void
     */
    public function displayGenericSentence($sentence, $ownerName, $type, $parentId = null) {
        // TODO Perhaps display this on the "Adopt" button...
        $tooltip = __(
            'This sentence does not belong to anyone. '.
            'If you would like to edit it, you have to adopt it first.', true
        );
        if (!empty($ownerName)) {
            $tooltip = sprintf(__('This sentence belongs to %s', true), $ownerName);
        }
        
        $sentenceId = $sentence['id'];
        $sentenceLang = $sentence['lang'];
        $isEditable = (CurrentUser::get('username') == $ownerName);
        ?>
        
        <div class="sentence <?php echo $type; ?>">
        <?php
        // audio
        $this->SentenceButtons->audioButton($sentenceId, $sentenceLang);
        
        // language flag
        $this->SentenceButtons->displayLanguageFlag(
            $sentenceId, $sentenceLang, $isEditable
        );
        
        // Navigation icon, so we can browse to the sentence + link/unlink
        if ($type == 'original') {
            $this->SentenceButtons->displayInfoButton($sentenceId);
        } else if ($type == 'directTranslation') {
            $this->SentenceButtons->translationShowButton($sentenceId, 'direct');
        } else if ($type == 'indirectTranslation') {
            $this->SentenceButtons->translationShowButton($sentenceId, 'indirect');
        }
        
        // Sentence and romanization
        $this->_displayBasicSentence($sentence, $isEditable);
        ?>
        </div>
        
        <?php
    }
    
    
    public function _displayBasicSentence($sentence, $isEditable) {
        $sentenceId = $sentence['id'];
        $sentenceLang = $sentence['lang'];
        $sentenceText = $sentence['text'];
        ?>
        
        <div class="basicSentence">
        <?php
        // text
        $this->_displaySentenceText($sentenceId, $sentenceText, $isEditable);
        
        // romanization
        $this->_displayRomanization($sentence);
        
        // traditional or simplified Chinese
        $this->_displayAlternateScript($sentence);
        ?>
        </div>
        
        <?php
    }
    
    
    public function _displaySentenceText($sentenceId, $sentenceText, $isEditable)
    {
        if ($isEditable) {
            
            $this->Javascript->link('jquery.jeditable.js', false);
            $this->Javascript->link('sentences.edit_in_place.js', false);
            
            // TODO: HACK SPOTTED id is used in edit_in_place
            // NOTE: I didn't find an easy way to difficult to find another way to 
            // pass the sentenceId to jEditable using jQuery.data...
            echo '<div id="_'.$sentenceId.'" class="text editableSentence">';
            echo Sanitize::html($sentenceText);
            echo '</div>';
            // NOTE: I'm echo-ing this because we don't want to have extra spaces
            // before or after the sentence text.
            
        } else {
            echo $this->Html->link(
                $sentenceText,
                array(
                    'controller' => 'sentences',
                    'action' => 'show',
                    $sentenceId
                ),
                array(
                    'class' => 'text'
                )
            );
        }
    }
}
?>
