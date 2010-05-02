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
            $romanization = $sentence['romanization'];
            
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
     * @param bool  $inBrowseMode         ???
     *
     * @return void
     */
    public function displayGroup(
        $sentence,
        $translations,
        $user = null, 
        $indirectTranslations = array(),
        $inBrowseMode = false
    ) {
        echo '<div class="sentence">';
        // Sentence
        $this->Javascript->link('jquery.jeditable.js', false);
        $this->Javascript->link('sentences.edit_in_place.js', false);
        
        $editable = '';
        $editableSentence = '';
        $editableFlag = false;
        $tooltip = __(
            'This sentence does not belong to anyone. '.
            'If you would like to edit it, you have to adopt it first.', true
        );
        $linkStyle = '';
        $divStyle = 'display:none;';
        
        if ($user != null) {
            if (isset($user['canEdit']) && $user['canEdit']) {
                $editable = 'editable';
                $editableSentence = 'editableSentence';
                $linkStyle = 'display:none;';
                $divStyle = '';
                $editableFlag = true;
            }
            if (isset($user['username']) && $user['username'] != '') {
                $tooltip = __('This sentence belongs to :', true).' '
                    .$user['username'];
            }
        }
        
        // Original sentence
        echo '<div id="_'.$sentence['id'].'_original" class="original">';
        
        // audio
        $this->SentenceButtons->audioButton($sentence['id'], $sentence['lang']);
        
        // language flag
        $this->SentenceButtons->displayLanguageFlag(
            $sentence['id'], $sentence['lang'], $editableFlag
        );
        
        // sentence text
        $toggle = "toggleOriginalSentence";
        if ($inBrowseMode) {
            $divStyle = '';
            $toggle = '';
        } else {
            echo $this->Html->link(
                $sentence['text'],
                array(
                    "controller"=>"sentences",
                    "action"=>"show",
                    $sentence['id']
                ),
                array(
                    "style" => $linkStyle,
                    "class" => "toggleOriginalSentence"
                )
            );
        }
        // TODO : HACK SPOTTED id is made of lang + id
        // and then is used in edit_in_place 
        echo '<div id="'.$sentence['lang'].'_'.$sentence['id'].'" 
            class="'.$toggle.' '.$editable.' '.$editableSentence.'" 
            title="'.$tooltip.'" style="'.$divStyle.'">';

        echo Sanitize::html($sentence['text']);

        echo '</div> ';
            
        // romanization
        $this->_displayRomanization($sentence);
        $this->_displayAlternateScript($sentence);
        
        echo '</div>';
        echo "\n";
        
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
     * @param bool  $canUnlink    'true' if user can unlink the translation.
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
     * @param bool  $canLink              'true' if user can link the translation.
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
}
?>