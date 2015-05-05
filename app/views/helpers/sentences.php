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
        'Menu',
        'Images'
    );


    /**
     * Diplays a sentence and its translations.
     *
     * @param array $sentence             Sentence to display.
     * @param array $translations         Translations of the sentence.
     * @param array $user                 Owner of the sentence.
     * @param array $indirectTranslations Indirect translations of the sentence.
     * @param bool  $options              Array of options
                                          withAudio: set it to false to hide audio icon
     *                                    langFilter: the language $indirectTranslations are filtered in, if any.
     *
     * @return void
     */
    public function displaySentencesGroup(
        $sentence,
        $translations,
        $user = null,
        $indirectTranslations = array(),
        $options = array()
    ) {
        $options = array_merge(
            array(
                'withAudio' => true,
                'langFilter' => 'und'
            ),
            $options
        );
        extract($options);

        $id = $sentence['id'];

        ?>
        <div class="sentences_set" id="sentences_group_<?php echo $id; ?>">
        <?php

        $ownerName = null;
        if (isset($user['username'])) {
            $ownerName = $user['username'];
        }
        $this->displayMainSentence($sentence, $ownerName, $withAudio, $langFilter);


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

        $this->displayTranslations($id, $translations, $indirectTranslations, $withAudio, $langFilter);

        ?>
        </div>
        <?php
    }

    public function displayTranslations($id, $translations, $indirectTranslations, $withAudio = true, $langFilter = 'und') {
        ?>
        <div id="_<?php echo $id; ?>_translations" class="translations">
            
            <?php
            $this->Javascript->link('sentences.collapse.js', false);

            $totalDirectTranslations = count(array_keys($translations));
            $totalIndirectTranslations = count(array_keys($indirectTranslations));

            //merge direct and indirect translations into single array
            $allTranslations = array_merge($translations, $indirectTranslations);

            $totalTranslations = count($allTranslations);
            $showButton = true;

            //if only 1 hidden sentence then show all
            $collapsibleTranslationsEnabled = $this->Session->read('collapsible_translations_enabled') || !CurrentUser::isMember();
            if ($totalTranslations <= 6 || !$collapsibleTranslationsEnabled) {
                $initiallyDisplayedTranslations = $totalTranslations;
                $showButton = false;
            } else {
                $initiallyDisplayedTranslations = 5;
                $displayed = $totalTranslations - $initiallyDisplayedTranslations;
            }
            
            //Split 'allTranslations' array into two, visible & hidden sets of sentences        
            $visibleTranslations = array_slice($allTranslations, 0, $initiallyDisplayedTranslations);
            $hiddenTranslations = array_slice($allTranslations, $initiallyDisplayedTranslations);
            
            $sentenceCount = 0;  

            //visible list of translations
            foreach ($visibleTranslations as $translation) {

                if ($sentenceCount < $totalDirectTranslations)
                    $type = 'directTranslation';
                else 
                    $type = 'indirectTranslation';

                $this->displayGenericSentence(
                    $translation['Translation'],
                    $type,
                    $withAudio,
                    $id,
                    false,
                    $langFilter
                );

                $sentenceCount++;
            }

            if($showButton){
                echo $this->Html->tag('div',
                    ' ▼ ' . format(__n(
                        'Show 1 more translation',
                        'Show {number} more translations',
                        $displayed,
                        true
                    ), array('number' => $displayed)),
                    array('class' => 'showLink')
                );
            }

            //expanded list of translations
            echo $this->Html->tag('div', null, array('class' => 'more'));
            
            foreach ($hiddenTranslations as $translation) {

                if ($sentenceCount < $totalDirectTranslations)
                    $type = 'directTranslation';
                else 
                    $type = 'indirectTranslation';

                $this->displayGenericSentence(
                    $translation['Translation'],
                    $type,
                    $withAudio,
                    $id,
                    false,
                    $langFilter
                );

                $sentenceCount++;
            }

            echo $this->Html->tag('div',
                    ' ▲ ' . __('Less translations', true),
                    array('class' => 'hideLink')
                );
            ?>
          </div>  
        </div>
        <?php
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
        $langArray = $this->Languages->profileLanguagesArray(true, false, false);

        ?>
        <div id="translation_for_<?php echo $id; ?>" class="addTranslations">
        <?php
        $currentUserLanguages = CurrentUser::getProfileLanguages();
        if (empty($currentUserLanguages)) {

            $this->Languages->displayAddLanguageMessage(false);

        } else {

            $this->_translationForm($id, $withAudio, $langArray);

        }
        ?>
        </div>
        <?php
    }

    private function _translationForm($id, $withAudio, $langArray)
    {
        $preSelectedLang = $this->Session->read('contribute_lang');
        if (!array_key_exists($preSelectedLang, $langArray)) {
            $preSelectedLang = key($langArray);
        }

        echo $this->Images->svgIcon(
            'translation',
            array(
                'class' => 'navigationIcon'
            )
        );

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
        <?php

        echo '<div class="form">';

        echo $this->Html->tag(
            'label',
            __('Translation:', true),
            array(
                'for'=>'_'.$id.'_text'
            )
        );

        // Input field
        echo $this->Form->textarea(
            'translation',
            array(
                'id' => '_'.$id.'_text',
                'class' => 'addTranslationsTextInput',
                'dir' => 'auto',
            )
        );

        // language select
        echo '<div class="languageSection">';
        echo $this->Html->tag(
            'label',
            __('Language:', true),
            array(
                'for'=>'translationLang_'.$id
            )
        );

        echo $this->Form->select(
            'translationLang_'.$id,
            $langArray,
            $preSelectedLang,
            array(
                "class" => "translationLang language-selector",
                "empty" => false
            ),
            false
        );

        $display = null;
        if ($preSelectedLang == 'auto') {
            $display = 'display: none;';
        }
        echo $this->Languages->icon(
            $preSelectedLang,
            array(
                'class' => 'flag translationLang_flag',
                'width' => '30',
                'height' => '20',
                'style' => $display
            )
        );
        echo '</div>';

        // Buttons
        echo '<div class="addTranslation_buttons">';
        // OK
        echo $this->Form->button(
            __('Submit translation', true),
            array(
                'id' => '_'.$id.'_submit',
                'class' => 'submit button'
            )
        );

        // Cancel
        echo $this->Form->button(
            __('Cancel', true),
            array(
                'id' => '_'.$id.'_cancel',
                'type' => 'reset',
                'class'=>'cancel button'
            )
        );
        echo '</div>';

        echo '</div>';

    }


    /**
     * Displays the main sentence. The main sentence is composed of a sentence and a
     * menu of action that can be applied on this sentence. This is the sentence at
     * the top.
     *
     * @param array  $sentence  Sentence data.
     * @param string $ownerName Name of the owner of the sentence.
     * @param string $langFilter The language translations are filtered in, if any.
     *
     * @return void
     */
    public function displayMainSentence($sentence, $ownerName, $withAudio, $langFilter = 'und') {
        $sentenceId = $sentence['id'];
        $canTranslate = $sentence['correctness'] >= 0;
        $hasAudio = $sentence['hasaudio'] == 'shtooka';
        $this->Menu->displayMenu(
            $sentenceId, $ownerName, $sentence['script'], $canTranslate, $langFilter, $hasAudio
        );

        $isEditable = CurrentUser::canEditSentenceOfUser($ownerName);
        $this->displayGenericSentence(
            $sentence,
            'mainSentence',
            $withAudio,
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
     * @param string $type            Type of sentence. Can be 'mainSentence',
     *                                'directTranslation' or 'indirectTranslation'.
     * @param bool   $withAudio       Set to 'true' if audio icon is displayed.
     * @param int    $parentId        Id of the parent sentence (i.e. main sentence).
     * @param bool   $isEditable      Whether the sentence can be edited in place.
     * @param string $langFilter      The language the list of sentences $sentence is from is being filtered in, if any.
     *
     * @return void
     */
    public function displayGenericSentence(
        $sentence,
        $type,
        $withAudio = true,
        $parentId = null,
        $isEditable = false,
        $langFilter = 'und'
    ) {
        $sentenceId = $sentence['id'];
        $sentenceLang = $sentence['lang'];
        $sentenceAudio = 'no';
        $correctnessLabel = $this->getCorrectnessLabel($sentence['correctness']);
        if (isset($sentence['hasaudio'])) {
            $sentenceAudio = $sentence['hasaudio'];
        }
        $elementId = '';
        if ($type != 'mainSentence') {
            $elementId = 'id="translation_'.$sentenceId.'_'.$parentId.'"';
        }
        $classes = array('sentence', $type, $correctnessLabel);
        if ($isEditable && $type == 'directTranslation') {
            $classes[] = 'editableTranslation';
        }
        $class = join(' ', $classes);
        ?>
        
        <div class="<?php echo $class; ?>" <?php echo $elementId; ?>>
        <?php
        // Navigation button (info or arrow icon)
        echo '<div class="nav column">';
        $this->SentenceButtons->displayNavigationButton($sentenceId, $type);
        echo '</div>';

        // language flag
        // TODO For Chinese sentences, it is better to display the
        // traditional/simplified icon here, instead of in the menu.
        echo '<div class="lang column">';
        $this->SentenceButtons->displayLanguageFlag(
            $sentenceId, $sentenceLang, $isEditable
        );
        echo '</div>';

        echo '<div class="content column">';
        // Link/unlink button
        if (CurrentUser::isTrusted()) {

            $this->_displayLinkOrUnlinkButton(
                $parentId, $sentenceId, $type, $langFilter
            );
        }

        // Sentence and romanization
        $canEdit = $isEditable && $sentenceAudio == 'no';
        $this->displaySentenceContent($sentence, $canEdit);
        echo '</div>';

        // audio
        if ($withAudio) {
            echo '<div class="audio column">';
            $this->SentenceButtons->audioButton(
                $sentenceId, $sentenceLang, $sentenceAudio
            );
            echo '</div>';
        }
        ?>
        </div>

        <?php
    }
    
    
    /**
     * Returns the label for the correctness of a sentence.
     * 
     * @param int $correctness Correctness of the sentence.
     *
     * @return String
     */
    private function getCorrectnessLabel($correctness)
    {
        $result = 'correctness';
        
        if ($correctness < 0) {
            $result .= 'Negative'.abs($correctness);
        } else if ($correctness == 0) {
            $result .= 'Zero';
        } else {
            $result .= 'Positive'.$correctness;
        }
        
        return $result;
    }

     
    /**
     * Display the link or unlink button.
     *
     * @param array  $parentId    Id of the parent (or grand-parent) sentence.
     * @param string $sentenceId  Name of the owner of the sentence.
     * @param string $type        Type of sentence. Can be 'directTranslation' or
     *                            'indirectTranslation'.
     * @param string $langFilter  The language sentences should be filtered in when redisplaying the list.
     *
     * @return void
     */
    private function _displayLinkOrUnlinkButton($parentId, $sentenceId, $type, $langFilter)
    {
        if ($type == 'directTranslation') {
            $this->SentenceButtons->unlinkButton(
                $parentId, $sentenceId, $langFilter
            );
        }

        if ($type == 'indirectTranslation') {
            $this->SentenceButtons->linkButton(
                $parentId, $sentenceId, $langFilter
            );
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
        ?>

        <div class="sentenceContent">
        <?php
        // text
        $script = null;
        if (isset($sentence['script'])) {
            $script = $sentence['script'];
        }
        $this->displaySentenceText(
            $sentence['id'], $sentence['text'], $isEditable,
            $sentence['lang'], $script
        );

        // romanization
        if (isset($sentence['transcriptions'])) {
            $this->displayTranscriptions(
                $sentence['transcriptions'], $sentence['lang']
            );
        }
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
     * @param bool  $sentenceLang Language of the sentence.
     * @param bool  $sentenceScript ISO 15924 script code.
     *
     * @return void
     */
    public function displaySentenceText(
        $sentenceId, $sentenceText, $isEditable = false,
        $sentenceLang = '', $sentenceScript = ''
    ) {
        if ($isEditable) {

            $this->Javascript->link('jquery.jeditable.js', false);
            $this->Javascript->link('sentences.edit_in_place.js', false);

            // TODO: HACK SPOTTED id is used in edit_in_place
            // NOTE: I didn't find an easy way to pass the sentenceId to jEditable
            // using jQuery.data...
            echo $this->Languages->tagWithLang(
                'div', $sentenceLang, $sentenceText,
                array(
                    'class' => 'text editableSentence',
                    'id' => $sentenceLang.'_'.$sentenceId,
                    'data-submit' => __('OK', true),
                    'data-cancel' => __('Cancel', true),
                ),
                $sentenceScript
            );

        } else {

            echo $this->Languages->tagWithLang(
                'div', $sentenceLang, $sentenceText,
                array('class' => 'text'),
                $sentenceScript
            );

        }
    }

    /**
     * Transforms "[kanji|reading]" to HTML <ruby> tags
     */
    private function _rubify($formatted) {
        return preg_replace(
            '/\[([^|]*)\|([^\]]*)\]/',
            '<ruby><rp>[</rp>$1<rp>|</rp><rt>$2</rt><rp>]</rp></ruby>',
            $formatted);
    }

    /**
     * Display transcriptions.
     *
     * @param array  $transcriptions List of transcriptions.
     * @param string $lang           Language of the transcripted sentence.
     *
     * @return void
     */
    public function displayTranscriptions($transcriptions, $lang)
    {
        $chained = array();
        foreach ($transcriptions as $script => $transcr) {
            if (isset($transcr['parent_id'])) {
                $chained[ $transcr['parent_id'] ] = $transcriptions[$script];
                unset($transcriptions[$script]);
            }
        }

        foreach ($transcriptions as $script => $transcr) {
            if ($transcr['dirty'] && !$isEditable)
                continue;
            if (isset($transcr['id']) && isset($chained[$transcr['id']])) {
                $subTranscr = $chained[$transcr['id']];
            } else {
                $subTranscr = null;
            }
            $this->displayTranscription($transcr, $lang, $subTranscr);
        }
    }

    private function displayTranscription($transcr, $lang, $subTranscr = null) {
        $this->Javascript->link('jquery.jeditable.js', false);
        $this->Javascript->link('transcriptions.edit_in_place.js', false);

        $isEditable = true;
        if (isset($transcr['readonly']) && $transcr['readonly']) {
            $isEditable = false;
        }

        $isGenerated = !isset($transcr['user_id']);
        $class = 'transcription';
        if ($isEditable)
            $class .= ' editable';
        $html = $this->transcriptionAsHTML($transcr);
        $transcriptionDiv = $this->Languages->tagWithLang(
            'div', $lang, $html,
            array(
                'data-sentence-id' => $transcr['sentence_id'],
                'data-script' => $transcr['script'],
                'data-tooltip' => __('Click to edit this transcription', true),
                'class' => $class,
                'escape' => false,
            ),
            $transcr['script']
        );

        $infoDiv = '';
        if ($isGenerated && $isEditable) {
            $warningMessage = __(
                'The following transcription has been automatically generated '.
                'and <strong>may contain errors</strong>. '.
                'If you can, you are welcome to review by clicking it.',
                true
            );
            $infoDiv = $this->Html->tag('div', $warningMessage, array(
                'class' => 'transcriptionWarning',
            ));
        }

        $class = 'transcription subTranscription';
        $subTranscrDiv = '';
        if ($subTranscr) {
            $subTranscrDiv = $this->Languages->tagWithLang(
                'div', $lang, $subTranscr['text'],
                array('class' => $class),
                $subTranscr['script']
            );
        }

        $class = '';
        if ($isGenerated) {
            $class .= 'generatedTranscription';
        }
        echo $this->Html->tag('div', $infoDiv.$transcriptionDiv.$subTranscrDiv, array(
            'escape' => false,
            'class' => $class,
        ));
    }

    /**
     * Format and escape a transcription
     * so that it may be displayed as HTML.
     */
    public function transcriptionAsHTML($transcr) {
        $text = Sanitize::html($transcr['text']);
        if ($transcr['script'] == 'Hrkt')
            $text = $this->_rubify($text);
        return $text;
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
        echo $this->Javascript->link('transcriptions.edit_in_place.js', true);
        echo $this->Javascript->link('sentences.change_language.js', true);
        echo $this->Javascript->link('sentences.link.js', true);
        echo $this->Javascript->link('sentences.collapse.js', true);
        $this->javascriptForAJAXTranslationsGroup();
    }

    public function javascriptForAJAXTranslationsGroup() {
        echo $this->Javascript->link('sentences.play_audio.js', true);
        echo $this->Javascript->link('links.add_and_delete.js', true);
        echo $this->Javascript->link('sentences.logs.js', true);
    }


    /**
     * Diplays a sentence and its translations for 'light' layout.
     *
     * @author CK
     * @author HO Ngoc Phuong Trang <tranglich@gmail.com>
     *
     * @param array $sentence     Sentence to display.
     * @param array $translations Translations of the sentence.
     *
     * @return void
     */
    public function displaySGroup($sentence, $translations)
    {
        $id = $sentence['id'];
        ?>

        <div class="sentences_set" id="sentences_group_<?php echo $id; ?>">

            <?php $this->displayS($sentence, 'mainSentence'); ?>

            <div class="translations">
                <?php
                foreach ($translations as $translation) {
                    $this->displayS($translation['Translation'], 'directTranslation');
                }
                ?>
            </div>

        </div>

        <?php
    }


    /**
     * Displays a sentence (either main sentence or direct translation)
     * and the language flag.
     *
     * @author CK
     * @author HO Ngoc Phuong Trang <tranglich@gmail.com>
     *
     * @param array  $sentence Sentence data.
     * @param string $type     Type of sentence. Can be 'mainSentence',
     *                         or 'directTranslation'.
     *
     * @return void
     */
    public function displayS($sentence, $type) {
        $lang = $sentence['lang'];

        echo $this->Html->div(
            "sentence " . $type,
            null,
            array(
                'lang' => LanguagesLib::languageTag($lang),
                'dir'  => LanguagesLib::getLanguageDirection($lang),
            )
        );

        $this->SentenceButtons->displayLanguageFlag(
            $sentence['id'], $sentence['lang'], false
        );

        if ($type == 'mainSentence') {
            echo $this->Html->link(
                $sentence['text'],
                array(
                    'controller' => 'sentences',
                    'action' => 'show',
                    $sentence['id']
                )
            );
        } else {
            echo $this->Html->div(null, $sentence['text']);
        }

        echo $this->Html->tag('/div');
    }

}
?>
