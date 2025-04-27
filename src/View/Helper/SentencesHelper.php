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
 * @link     https://tatoeba.org
 */
namespace App\View\Helper;

use App\Lib\LanguagesLib;
use App\Model\CurrentUser;
use App\View\Helper\AppHelper;
use Cake\Utility\Hash;
use App\Model\Table\SentencesTable;


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
 * @link     https://tatoeba.org
 */
class SentencesHelper extends AppHelper
{
    public $helpers = array(
        'AssetCompress.AssetCompress',
        'Html',
        'Form',
        'SentenceButtons',
        'Languages',
        'Menu',
        'Images',
        'Transcriptions',
        'Search',
        'Number',
        'SentenceLicense',
        'ClickableLinks',
    );


    /**
     * Diplays a sentence and its translations.
     *
     * @param array $sentence   Sentence, transcriptions, translations,
                                owner and audio recordings.
     * @param bool  $options              Array of options
                                          withAudio: set it to false to hide audio icon
     *                                    langFilter: the language $indirectTranslations are filtered in, if any.
     *
     * @return void
     */
    public function displaySentencesGroup(
        $sentence,
        $options = array(),
        $duplicate = false
    ) {
        if (CurrentUser::isMember()) {
            // defined in config/asset_compress.ini
            $this->AssetCompress->script('sentences-block-for-members.js', ['block' => 'scriptBottom']);
        }

        $options = array_merge(
            array(
                'withAudio' => true,
                'langFilter' => 'und'
            ),
            $options
        );
        extract($options);

        $id = $sentence->id;

        ?>
        <div class="sentences_set" id="sentences_group_<?php echo $id; ?>">
        <?php

        if ($duplicate) {
            $image = $this->Images->svgIcon(
                'warning-small',
                ['class' => 'sentences_duplicate_icon']
            );

            $message = $image.__(
                'Your sentence was not added because the following already exists.',
                true
            );

            echo $this->Html->div('sentences_duplicate', $message);
        }

        $this->displayMainSentence(
            $sentence,
            $withAudio,
            $langFilter
        );


        // Loading icon
        echo $this->Html->div('translation-loader loader', '', array(
            'id' => '_'.$id.'_loading',
            'style' => 'display:none',
        ));

        // Form to add a new translation
        $this->_displayNewTranslationForm($id);

        if ($sentence->translations) {
            $translations = $sentence->translations;
            $this->displayTranslations($id, $translations, $withAudio, $langFilter);
        }
        ?>
        </div>
        <?php
    }

    public function displayTranslations($id, $translations, $withAudio = true, $langFilter = 'und') {
        list($translations, $indirectTranslations) = $translations;
        ?>
        <div id="_<?php echo $id; ?>_translations" class="translations">

            <?php
            $totalDirectTranslations = count(array_keys($translations));
            $totalIndirectTranslations = count(array_keys($indirectTranslations));

            //merge direct and indirect translations into single array
            $allTranslations = array_merge($translations, $indirectTranslations);

            $totalTranslations = count($allTranslations);
            $showButton = true;

            //if only 1 hidden sentence then show all
            $collapsibleTranslationsEnabled = !CurrentUser::isMember() || CurrentUser::get('settings.collapsible_translations');
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
                    $translation,
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
                    $translation,
                    $type,
                    $withAudio,
                    $id,
                    false,
                    $langFilter
                );

                $sentenceCount++;
            }

            echo $this->Html->tag('div',
                    ' ▲ ' . __('Fewer translations'),
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
     * @param array $sentence  Sentence, transcriptions, translations, audios, owner.
     *
     * @return void
     */
    public function displaySimpleSentencesGroup($sentence) {
        $withAudio = true;
        $id = $sentence->id;
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
        foreach ($sentence->translations as $translation) {
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
    private function _displayNewTranslationForm($id)
    {
        $langArray = $this->Languages->profileLanguagesArray(true);

        ?>
        <div id="translation_for_<?php echo $id; ?>" class="addTranslations">
        <?php
        $currentUserLanguages = CurrentUser::getProfileLanguages();
        if (empty($currentUserLanguages)) {

            $this->Languages->displayAddLanguageMessage(false);

        } else {

            $this->_translationForm($id, $langArray);

        }
        ?>
        </div>
        <?php
    }

    private function _translationForm($id, $langArray)
    {
        $preSelectedLang = $this->getView()->get('contribute_lang');

        echo $this->Images->svgIcon(
            'translation',
            array(
                'class' => 'navigationIcon'
            )
        );

        echo $this->Form->create('Sentence', [
            'id' => 'translation-form',
            'url' => [
                'controller' => 'sentences', 
                'action' => 'save_translation'
            ],
            'class' => 'form',
            'onsubmit' => 'return false'
        ]);
        
        echo $this->Form->hidden('id', ['value' => $id]);

        echo $this->Html->tag(
            'label',
            __('Translation:'),
            array(
                'for'=>'_'.$id.'_text'
            )
        );

        // Input field
        echo $this->Form->textarea(
            'value',
            array(
                'id' => '_'.$id.'_text',
                'class' => 'addTranslationsTextInput',
                'dir' => 'auto',
                'value' => ''
            )
        );

        // language select
        echo '<div class="languageSection">';
        echo $this->Html->tag(
            'label',
            __('Language:'),
            array(
                'for'=>'translationLang_'.$id
            )
        );

        echo $this->Form->select(
            'selectLang',
            $langArray,
            array(
                "id" => 'translationLang_'.$id,
                "value" => $preSelectedLang,
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
                'style' => $display
            )
        );
        echo '</div>';

        // Buttons
        echo '<div class="addTranslation_buttons">';
        // OK
        echo $this->Form->button(
            __('Submit translation'),
            array(
                'id' => '_'.$id.'_submit',
                'class' => 'submit button'
            )
        );

        // Cancel
        echo $this->Form->button(
            /* @translators: cancel button of "add translation" form on a sentence (verb) */
            __('Cancel'),
            array(
                'id' => '_'.$id.'_cancel',
                'type' => 'button',
                'class'=>'cancel button'
            )
        );
        echo $this->Form->end();

        echo '</div>';

    }


    /**
     * Displays the main sentence. The main sentence is composed of a sentence and a
     * menu of action that can be applied on this sentence. This is the sentence at
     * the top.
     *
     * @param array  $sentence   Sentence, transcriptions, owner, audios.
     * @param string $langFilter The language translations are filtered in, if any.
     *
     * @return void
     */
    public function displayMainSentence(
        $sentence,
        $withAudio,
        $langFilter = 'und'
    ) {
        $user = $sentence->user;
        $sentenceId = $sentence->id;
        $canTranslate = $sentence->correctness >= 0 && $sentence->license != '';
        $hasAudio = isset($sentence->audios) && count($sentence->audios);
        $script = $sentence->script;
        $isFavorited = isset($sentence->favorites_users)
                       && count($sentence->favorites_users);
        $this->Menu->displayMenu(
            $sentenceId, $user, $script, $canTranslate, $langFilter, $hasAudio, $isFavorited
        );

        $ownerName = $user ? $user->username : null;
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
     * @param array  $sentence        Sentence, transcriptions, owner, audios.
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
        $this->AssetCompress->script('single-sentence.js', ['block' => 'scriptBottom']);

        $sentenceId = $sentence->id;
        $sentenceLang = $sentence->lang;
        $isFavoritePage = ($this->request->params['controller'] == 'Favorites' && $this->request->params['action'] == 'of_user');
        $classes = array('sentence', $type);
        if ($isEditable && $type == 'directTranslation') {
            $classes[] = 'editableTranslation';
        }
        $class = join(' ', $classes);

        $attributes = array(
            'id' => 'translation_'.$sentenceId.'_'.$parentId,
            'data-sentence-id' => $sentenceId
        );
        echo $this->Html->div($class, null, $attributes);

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

        if($isFavoritePage){
            echo '<div class="content column remove">';
        } else {
            echo '<div class="content column">';
        }

        // Link/unlink button
        if (CurrentUser::isTrusted()) {
            $this->_displayLinkOrUnlinkButton(
                $parentId, $sentenceId, $type, $langFilter
            );
        }

        // Sentence
        $hasAudio = isset($sentence->audios) && count($sentence->audios);
        $canEdit = $isEditable && !$hasAudio;
        $this->displaySentenceContent($sentence, $canEdit);
        echo '</div>';

        // Copy
        echo '<div class="copy column">';
        $this->SentenceButtons->displayCopyButton($sentence->text);
        echo '</div>';

        // Audio
        if ($withAudio && isset($sentence->audios)) {
            echo '<div class="audio column">';
            $this->SentenceButtons->audioButton(
                $sentenceId, $sentenceLang, $sentence->audios
            );
            echo '</div>';
        }

        if( $isFavoritePage && $this->request->params['pass'][0] == CurrentUser::get('username') ){
            echo '<div class="favorite-page column">';
            $this->Menu->favoriteButton($sentenceId, true, true, true);
            echo '</div>';
        }

        // Transcriptions
        if (isset($sentence->transcriptions)
            && count($sentence->transcriptions)) {
            echo $this->Html->div('transcriptions', null, array(
               'data-sentence-id' => $sentence->id,
            ));
            $this->Transcriptions->displayTranscriptions(
                $sentence->transcriptions,
                $sentence->lang,
                $sentence->user_id
            );
            echo $this->Html->tag('/div');
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
    public function displaySentenceContent(
        $sentence,
        $isEditable
    ) {
        echo $this->Html->div('sentenceContent', null, array(
            'data-sentence-id' => $sentence->id,
        ));

        $script = $sentence->script;
        $highlight = null;
        if (isset($sentence->highlight)) {
            $highlight = $sentence->highlight;
        }
        $this->displaySentenceText(
            $sentence->id, $sentence->text, $isEditable,
            $sentence->lang, $script, $sentence->correctness,
            $highlight
        );

        echo $this->Html->tag('/div');
    }

    /**
     * Displays the text of a sentence. This text can be editable or not.
     *
     * @param array $sentenceId   Id of the sentence.
     * @param array $sentenceText Text of the sentence.
     * @param bool  $isEditable   Set to 'true' if sentence is editable.
     * @param bool  $sentenceLang Language of the sentence.
     * @param bool  $sentenceScript ISO 15924 script code.
     * @param bool  $correctness  Sentence correctness level.
     * @param array $highlight    Highlighting markers for search results.
     *
     * @return void
     */
    public function displaySentenceText(
        $sentenceId, $sentenceText, $isEditable = false,
        $sentenceLang = '', $sentenceScript = '', $correctness,
        $highlight
    ) {
        $classes = array(
            'text',
            $this->getCorrectnessLabel($correctness),
        );
        $sentenceEscaped = false;
        if ($highlight) {
            $sentenceText = h($sentenceText);
            $sentenceEscaped = true;
            $sentenceText = $this->Search->highlightMatches($highlight, $sentenceText);
        }

        if ($isEditable) {
            $classes[] = 'editableSentence';
            
            echo $this->Languages->tagWithLang(
                'div', $sentenceLang, $sentenceText,
                array(
                    'class' => join(' ', $classes),
                    /* @translators: submit button of sentence edition form */
                    'data-submit' => __('OK'),
                    /* @translators: cancel button of sentence edition form (verb) */
                    'data-cancel' => __('Cancel'),
                    'escape' => !$sentenceEscaped,
                ),
                $sentenceScript
            );

        } else {

            echo $this->Languages->tagWithLang(
                'div', $sentenceLang, $sentenceText,
                array(
                    'class' => join(' ', $classes),
                    'escape' => !$sentenceEscaped,
                ),
                $sentenceScript
            );

        }
    }

    /**
     * Javascript for AJAX loaded sentences group.
     *
     * @return void
     */
    public function javascriptForAJAXSentencesGroup() {
        $options = ['block' => 'scriptBottom'];

        // defined in config/asset_compress.ini
        $this->AssetCompress->script('sentences-block-for-members.js', $options);
        $this->AssetCompress->script('single-sentence.js', $options);
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
        $id = $sentence->id;
        ?>

        <div class="sentences_set" id="sentences_group_<?php echo $id; ?>">

            <?php $this->displayS($sentence, 'mainSentence'); ?>

            <div class="translations">
                <?php
                foreach ($translations as $translation) {
                    $this->displayS($translation, 'directTranslation');
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
        $lang = $sentence->lang;

        echo $this->Html->div(
            "sentence " . $type,
            null,
            array(
                'lang' => LanguagesLib::languageTag($lang),
                'dir'  => LanguagesLib::getLanguageDirection($lang),
            )
        );

        $this->SentenceButtons->displayLanguageFlag(
            $sentence->id, $sentence->lang, false
        );

        if ($type == 'mainSentence') {
            echo $this->Html->link(
                $sentence->text,
                array(
                    'controller' => 'sentences',
                    'action' => 'show',
                    $sentence->id
                ),
                ['target' => '_blank']
            );
        } else {
            echo $this->Html->div(null, $sentence->text);
        }

        echo $this->Html->tag('/div');
    }

    public function originText($sentence) {
        $baseId = $sentence->based_on_id;
        if (!is_null($baseId) && $baseId == 0) {
            $msg = __('This sentence is original and '
                     .'was not derived from translation.');
        } elseif ($baseId > 0) {
            $baseText = $sentence->base ? $sentence->base->text : null;
            $msg = format(
                __('This sentence was initially added as a '
                  .'translation of sentence {sentenceIdWithIdSign}.'),
                array('sentenceIdWithIdSign' => $this->ClickableLinks->buildSentenceLink($baseId, $baseText))
            );
        } else {
            $msg = __('We cannot determine yet whether this sentence was '
                     .'initially derived from translation or not.');
        }
        return $this->Html->tag('p', $msg, array('class' => 'derivation'));
    }

    public function sentenceForAngular($sentence) {
        if (isset($sentence->highlight)) {
            $sentenceText = h($sentence->text);
            $highlight = $sentence->highlight;
            $sentence['highlightedText'] = $this->Search->highlightMatches($highlight, $sentenceText);
        }
        if (isset($sentence->translations)) {
            $sentence->expandLabel = $this->getExpandLabel($sentence);
        }

        return h(json_encode($sentence));
    }

    public function translationsForAngular($translations) {
        return h(json_encode($translations));
    }

    public function getExpandLabel($sentence)
    {
        $extraTranslationsCount = $this->getNumberOfExtraTranslations($sentence);
        if ($extraTranslationsCount > 0) {
            return format(__n(
                'Show 1 more translation',
                'Show {number} more translations',
                $extraTranslationsCount
            ), ['number' => $this->Number->format($extraTranslationsCount)]);
        } else {
            return null;
        }
    }

    private function getNumberOfExtraTranslations($sentence)
    {
        $translations = $sentence->translations;
        $total = count($translations[0]) + count($translations[1]);
        return $total - $sentence->max_visible_translations;
    }
}
?>
