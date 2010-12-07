<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009-2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 * Sentences must follow a strict pattern in order to maintain consistency throughout
 * the whole application. If you have to display sentences somewhere, you have the 
 * choice among one of these:
 *  - SentencesGroup
 *  - MainSentence
 *  - GenericSentence
 *  - SentenceContent
 *  - SentenceText
 *
 * Refer to the description of the function corresponding to one of these patterns
 * for more information. They all start with "display" followed by the name of
 * the pattern.
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
        'Pinyin',
        'Menu'
    );
    
    
    /**
     * Diplays a sentence and its translations.
     *
     * @param array $sentence             Sentence to display.
     * @param array $translations         Translations of the sentence.
     * @param array $user                 Owner of the sentence.
     * @param array $indirectTranslations Indirect translations of the sentence.
     * @param bool  $withAudio            Set to 'true' if audio icon is displayed.
     * @param bool  $withDivWrapper       Set to 'true' to display the div wrapper
     *                                    (id=sentences_group_$id).
     *
     * @return void
     */
    public function displaySentencesGroup(
        $sentence,
        $translations,
        $user = null,
        $indirectTranslations = array(),
        $withAudio = true,
        $withDivWrapper = true
    ) {
        $id = $sentence['id'];
        
        if ($withDivWrapper) {
            ?>
            <div class="sentences_set" id="sentences_group_<?php echo $id; ?>">
        <?php
        }
         
        $ownerName = null;
        if (isset($user['username'])) {
            $ownerName = $user['username'];
        }
        $this->displayMainSentence($sentence, $ownerName, $withAudio);
        
        
        // Loading gif
        echo $this->Html->image(
            IMG_PATH . 'loading.gif',
            array(
                "id" => "_".$id."_loading",
                "class" => "loading",
                "width" => 31,
                "height" => 31
            )
        );
        
        // Form to add a new translation
        $this->_displayNewTranslationForm($id, $withAudio);
        ?>
        <div id="_<?php echo $id; ?>_translations" class="translations">
            <div></div>
            <?php
            // direct translations
            foreach ($translations as $translation) {
                $this->displayGenericSentence(
                    $translation, 
                    null, 
                    'directTranslation', 
                    $withAudio,
                    $id,
                    $ownerName
                );
            }
            
            // indirect translations
            foreach ($indirectTranslations as $translation) {
                $this->displayGenericSentence(
                    $translation, 
                    null, 
                    'indirectTranslation', 
                    $withAudio,
                    $id,
                    $ownerName
                );
            }
        
            ?>
        </div>
        
        <?php
        if ($withDivWrapper) {
        ?>
        </div>
        <?php
        }
    }
    
    
    /**
     * Displays group of sentences with only text, flag and audio button.
     *
     * @param array $sentence             Sentence to display.
     * @param array $translations         Translations of the sentence.
     *
     * @return void
     */
    public function displaySimpleSentencesGroup($sentence, $translations)
    {
        $withAudio = true;
        $id = $sentence['id'];
        ?>
        <div class="sentences_set" id="sentences_group_<?php echo $id; ?>">
        
        <?php
        $this->displayGenericSentence(
            $sentence, 
            null, 
            'mainSentence', 
            $withAudio
        );
        ?>
        
        <div id="_<?php echo $id; ?>_translations" class="translations">
        <?php
        // direct translations
        foreach ($translations as $translation) {
            $this->displayGenericSentence(
                $translation, 
                null, 
                'directTranslation', 
                $withAudio
            );
        }
        ?>
        </div>
        
        </div>
        <?php
    }
    

    /**
     * Displays the form to translate a sentence. Appears when clicking on the
     * translate icon.
     *
     * @param int  $id        The id of the sentence to translate.
     * @param bool $withAudio Set to 'true' to indicate that audio icon is displaye
     *                        in the translation.
     *
     * @return void
     */
    private function _displayNewTranslationForm($id, $withAudio)
    {
        $langArray = $this->Languages->translationsArray();
        $preSelectedLang = $this->Session->read('contribute_lang');
        if (empty($preSelectedLang)) {
            $preSelectedLang = 'auto';
        }
        if (!$withAudio) {
            $withAudio = 0;
        }
        ?>
        <script type='text/javascript'>
        $(document).ready(function() {
            $('#translate_<?php echo $id; ?>').data(
                'withAudio', <?php echo $withAudio; ?>
            );
        });
        </script>
        <div id="translation_for_<?php echo $id; ?>" class="addTranslations">
        
            <?php
            // Input field
            echo $this->Form->textarea(
                'translation',
                array(
                    'id' => '_'.$id.'_text',
                    'class' => 'addTranslationsTextInput',
                    'rows' => 2,
                    'cols' => 90,
                )
            );
            
            // language select
            echo $this->Form->select(
                'translationLang_'.$id,
                $langArray,
                $preSelectedLang,
                array("class"=>"translationLang"),
                false
            );
            
            // OK
            echo $this->Form->button(
                'translation',
                array(
                    'value' => __('Submit translation', true),
                    'id' => '_'.$id.'_submit'
                )
            );
            
            // Cancel
            echo $this->Form->button(
                'translation',
                array(
                    'value' => __('Cancel', true),
                    'id' => '_'.$id.'_cancel'
                )
            );
            
            // Warning
            ?>
            <div class="important">
            <p>
            <?php
            __(
                'Important! You are about to add a translation to the sentence '
                . 'above. If you do not understand this sentence, click on '
                . '"Cancel" to display everything again, and then click on '
                . 'the sentence that you understand and want to translate from.'
            );
            ?>
            </p>
            
            <p>
            <?php
            __(
                'Please do not forget <strong>capital letters</strong> '.
                'and <strong>punctuation</strong>! Thank you.'
            );
            ?>
            </p>
            </div>
        
        </div>
        <?php
    }
    

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
    public function displayMainSentence($sentence, $ownerName, $withAudio){
        $sentenceId = $sentence['id'];
        $chineseScript = null;
        if (isset($sentence['script'])) {
            $chineseScript = $sentence['script'];
        }
        
        $this->Menu->displayMenu(
            $sentenceId, $ownerName, $chineseScript
        );
        
        $isEditable = CurrentUser::canEditSentenceOfUser($ownerName);
        $this->displayGenericSentence(
            $sentence, 
            $ownerName, 
            'mainSentence', 
            $withAudio,
            null,
            null,
            $isEditable
        );
    }
    
    
    /**
     * Displays the generic version of a sentence. This is used to display the
     * the main sentence as well as direct and indirect translations.
     *
     * The generic sentence contains :
     *  - the navigation button (clicking on it leads to the "Browse" section)
     *  - the link/unlink buttons (only if user has permissions to link/unlink)
     *  - the sentence content (text, romanization, alternate Chinese script)
     *  - the language flag
     *  - the audio button
     *
     * @param array  $sentence        Sentence data.
     * @param string $ownerName       Name of the owner of sentence.
     * @param string $type            Type of sentence. Can be 'mainSentence', 
     *                                'directTranslation' or 'indirectTranslation'.
     * @param bool   $withAudio       Set to 'true' if audio icon is displayed.     
     * @param int    $parentId        Id of the parent sentence (i.e. main sentence).
     * @param string $parentOwnerName Name of the owner of the *main* sentence.
     *
     * @return void
     */
    public function displayGenericSentence(
        $sentence, 
        $ownerName, 
        $type, 
        $withAudio = true, 
        $parentId = null,
        $parentOwnerName = null,
        $isEditable = false
    ) {
        $sentenceId = $sentence['id'];
        $sentenceLang = $sentence['lang'];
        $sentenceAudio = 'no';
        if (isset($sentence['hasaudio'])) {
            $sentenceAudio = $sentence['hasaudio'];
        }
        ?>
        
        <div class="sentence <?php echo $type; ?>">
        <?php
        // Navigation button (info or arrow icon)
        if ($type != 'mainSentence' || $isEditable) {
            $this->_displayNavigation($sentenceId, $type);
        }
        
        // Link/unlink button
        if (CurrentUser::canLinkWithSentenceOfUser($parentOwnerName)) {
            $this->_displayLinkOrUnlinkButton($parentId, $sentenceId, $type);
        }
        
        // audio
        if ($withAudio) {
            $this->SentenceButtons->audioButton($sentenceId, $sentenceLang, $sentenceAudio);
        }
        
        // language flag
        // TODO For Chinese sentences, it is better to display the 
        // traditional/simplified icon here, instead of in the menu.
        $this->SentenceButtons->displayLanguageFlag(
            $sentenceId, $sentenceLang, $isEditable
        );
        
        // Sentence and romanization
        $this->displaySentenceContent($sentence, $isEditable);
        ?>
        </div>
        
        <?php
    }
    
    /**
     * Display the link or unlink button.
     *
     * @param array  $parentId    Id of the parent (or grand-parent) sentence.
     * @param string $sentenceId  Name of the owner of the sentence.
     * @param string $type        Type of sentence. Can be 'directTranslation' or 
     *                            'indirectTranslation'.
     *
     * @return void
     */
    private function _displayLinkOrUnlinkButton($parentId, $sentenceId, $type)
    {
        if ($type == 'directTranslation') {
            $this->SentenceButtons->unlinkButton(
                $parentId, $sentenceId
            );
        }
        
        if ($type == 'indirectTranslation') {
            $this->SentenceButtons->linkButton(
                $parentId, $sentenceId
            );
        }
    }
    
    
    /**
     * Displays the navigation button (either info or arrow icon).
     *
     * @param string $sentenceId Name of the owner of the sentence.
     * @param string $type       Type of sentence. Can be 'mainSentence', 
     *                           'directTranslation' or 'indirectTranslation'.
     *
     * @return void
     */
    private function _displayNavigation($sentenceId, $type)
    {
        if ($type == 'mainSentence') {
            $this->SentenceButtons->displayInfoButton($sentenceId);
            
        } else if ($type == 'directTranslation') {
            $this->SentenceButtons->translationShowButton($sentenceId, 'direct');
            
        } else if ($type == 'indirectTranslation') {
            $this->SentenceButtons->translationShowButton($sentenceId, 'indirect');
            
        }
    }
    
    
    /**
     * Displays the text and, if they exists, the romanization and alternate Chinese
     * script of a sentence.
     *
     * @param array $sentence   Sentence data.
     * @param bool  $isEditable Set to 'true' if sentence is editable.
     *
     * @return void
     */
    public function displaySentenceContent($sentence, $isEditable) {
        $sentenceId = $sentence['id'];
        $sentenceLang = $sentence['lang'];
        $sentenceText = $sentence['text'];
        ?>
        
        <div class="sentenceContent">
        <?php
        // text
        $this->displaySentenceText(
            $sentenceId, $sentenceText, $isEditable, $sentenceLang
        );
        
        // romanization
        $this->_displayRomanization($sentence);
        
        // traditional or simplified Chinese
        $this->_displayAlternateScript($sentence);
        ?>
        </div>
        
        <?php
    }
    
    
    /**
     * Displays the text of a sentence. This text can be editable or not.
     *
     * @param array $sentenceId   Id of the sentence.
     * @param array $sentenceText Text of the sentence.
     * @param bool  $isEditable   Set to 'true' if sentence is editable.
     * @param bool  $sentenceLang Used for logs... We need to get rid of it someday.
     *
     * @return void
     */
    public function displaySentenceText(
        $sentenceId, $sentenceText, $isEditable = false, $sentenceLang = ''
    ) {
        $dir = $this->Languages->getLanguageDirection($sentenceLang);

        if ($isEditable) {
            
            $this->Javascript->link('jquery.jeditable.js', false);
            $this->Javascript->link('sentences.edit_in_place.js', false);
            
            // TODO: HACK SPOTTED id is used in edit_in_place
            // NOTE: I didn't find an easy way to pass the sentenceId to jEditable
            // using jQuery.data...
            echo '<div dir="'.$dir.'" id="'.$sentenceLang.'_'.$sentenceId.'" class="text editableSentence">';
            echo Sanitize::html($sentenceText);
            echo '</div>';
            // NOTE: I'm echo-ing this because we don't want to have extra spaces
            // before or after the sentence text when editing in place.
            
        } else {
        
            echo $this->Html->link(
                $sentenceText,
                array(
                    'controller' => 'sentences',
                    'action' => 'show',
                    $sentenceId
                ),
                array(
                    'dir' => $dir,
                    'class' => 'text'
                )
            );
            
        }
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
            $romanization = $sentence['romanization'];
            
            if ($sentence['lang'] == 'jpn') {
                $this->Javascript->link(JS_PATH.'furigana.js', false);
                $title = 'ROMAJI: '.$sentence['romaji'];
                ?>
                
                <div class="romanization furigana" title="<?php echo $title; ?>">
                <?php echo $romanization; ?>
                </div>
                
                <?php
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
     * Inline Javascript for AJAX loaded sentences group.
     *
     * @return void
     */
    public function javascriptForAJAXSentencesGroup() {
        echo $this->Javascript->link('sentences.add_translation.js', true);
        echo $this->Javascript->link('favorites.add.js', true);
        echo $this->Javascript->link('sentences_lists.menu.js', true);
        echo $this->Javascript->link('sentences.adopt.js', true);
        echo $this->Javascript->link('jquery.jeditable.js', true);
        echo $this->Javascript->link('sentences.edit_in_place.js', true);
        echo $this->Javascript->link('sentences.play_audio.js', true);
        echo $this->Javascript->link('sentences.change_language.js', true);
        echo $this->Javascript->link('furigana.js', true);
    }
}
?>
