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

        $this->displayTranslations($id, $translations, $indirectTranslations, $withAudio);

        if ($withDivWrapper) {
        ?>
        </div>
        <?php
        }
    }

    public function displayTranslations($id, $translations, $indirectTranslations, $withAudio = true) {
        ?>
        <div id="_<?php echo $id; ?>_translations" class="translations">
            <div></div>
            <?php
            // direct translations
            foreach ($translations as $translation) {
                $this->displayGenericSentence(
                    $translation['Translation'],
                    null,
                    'directTranslation',
                    $withAudio,
                    $id
                );
            }

            // indirect translations
            foreach ($indirectTranslations as $translation) {
                $this->displayGenericSentence(
                    $translation['Translation'],
                    null,
                    'indirectTranslation',
                    $withAudio,
                    $id
                );
            }

            ?>
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
                array(
                    "class" => "translationLang language-selector",
                    "empty" => false
                ),
                false
            );
            
            // OK
            echo $this->Form->button(
                __('Submit translation', true),
                array(
                    'id' => '_'.$id.'_submit'
                )
            );

            // Cancel
            echo $this->Form->button(
                __('Cancel', true),
                array(
                    'id' => '_'.$id.'_cancel',
                    'type' => 'reset',
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

        $canTranslate = $sentence['correctness'] >= 0;
        $this->Menu->displayMenu(
            $sentenceId, $ownerName, $chineseScript, $canTranslate
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
        $sentenceCorrectness = $sentence['correctness'];
        $correctnessLabel = $this->getCorrectnessLabel($sentence['correctness']);
        if (isset($sentence['hasaudio'])) {
            $sentenceAudio = $sentence['hasaudio'];
        }
        $elementId = '';
        if ($type != 'mainSentence') {
            $elementId = 'id="translation_'.$sentenceId.'_'.$parentId.'"';
        }
        $class = 'sentence '.$type.' '.$correctnessLabel;
        ?>
        
        <div class="<?php echo $class; ?>" <?php echo $elementId; ?>>
        <?php
        // Navigation button (info or arrow icon)
        $this->_displayNavigation($sentenceId, $type);
        

        // Link/unlink button
        if (CurrentUser::isTrusted()) {
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
        if (isset($sentence['transcriptions'])) {
            $this->_displayTranscriptions(
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
     * @param bool  $sentenceLang Used for logs... We need to get rid of it someday.
     *
     * @return void
     */
    public function displaySentenceText(
        $sentenceId, $sentenceText, $isEditable = false, $sentenceLang = ''
    ) {
        $dir = $this->Languages->getLanguageDirection($sentenceLang);
        $sentenceText = Sanitize::html($sentenceText);

        if ($isEditable) {

            $this->Javascript->link('jquery.jeditable.js', false);
            $this->Javascript->link('sentences.edit_in_place.js', false);

            // TODO: HACK SPOTTED id is used in edit_in_place
            // NOTE: I didn't find an easy way to pass the sentenceId to jEditable
            // using jQuery.data...
            echo '<div dir="'.$dir.'" id="'.$sentenceLang.'_'.$sentenceId.'" class="text editableSentence">';
            echo $sentenceText;
            echo '</div>';

        } else {

            // To check if we're on the sentence's page or not
            $currentSentenceId = null;
            if (isset($this->params['pass'][0])) {
                $currentSentenceId = $this->params['pass'][0];
            }
            $currentURL = array(
                'controller' => $this->params['controller'],
                'action' => $this->params['action'],
                $currentSentenceId
            );

            echo '<div class="text" dir="'.$dir.'">';
            echo $sentenceText;
            echo '</div>';
            

        }
    }


    /**
     * Display transcriptions.
     *
     * @todo Rename CSS class: 'romanization' -> 'transcription'.
     *
     * @param array  $transcriptions List of transcriptions.
     * @param string $lang           Language of the sentence transcripted.
     *
     * @return void
     */
    private function _displayTranscriptions($transcriptions, $lang)
    {
        if ($lang == 'jpn') {

            $this->Javascript->link(JS_PATH.'furigana.js', false);
            $furigana = $transcriptions[0];
            $romaji = $transcriptions[1];
            echo '<div class="romanization furigana" title="'.$romaji.'">';
            echo $furigana;
            echo '</div>';

        } else if ($lang === 'cmn') {

            $otherScript = $transcriptions[1];
            echo '<div class="romanization">';
            echo $otherScript;
            echo '</div>';

            $pinyin = $this->Pinyin->numeric2diacritic($transcriptions[0]);
            echo '<div class="romanization">';
            echo $pinyin;
            echo '</div>';

        } else {

            foreach ($transcriptions as $transcription) {
                echo '<div class="romanization">';
                echo $transcription;
                echo '</div>';
            }

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
        echo $this->Javascript->link('sentences.change_language.js', true);
        echo $this->Javascript->link('sentences.link.js', true);
        $this->javascriptForAJAXTranslationsGroup();
    }

    public function javascriptForAJAXTranslationsGroup() {
        echo $this->Javascript->link('sentences.play_audio.js', true);
        echo $this->Javascript->link('links.add_and_delete.js', true);
        echo $this->Javascript->link('furigana.js', true);
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
        $sentenceId = $sentence['id'];
        $sentenceLang = $sentence['lang'];
        $sentenceText = $sentence['text'];
        ?>

        <div class="sentence <?php echo $type; ?>">
            <?php
            $this->SentenceButtons->displayLanguageFlag(
                $sentenceId, $sentenceLang, false
            );

            $this->displaySentenceText(
                $sentenceId, $sentenceText, false, $sentenceLang
            );
            ?>
        </div>

        <?php
    }

}
?>
