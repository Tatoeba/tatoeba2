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

$this->set('title_for_layout', $pages->formatTitle(__('Add sentences', true)));

$sentences->javascriptForAJAXSentencesGroup(false);
$javascript->link(JS_PATH . 'sentences.contribute.js', false);

$vocabularyUrl = $html->url(array(
    'controller' => 'vocabulary',
    'action' => 'add_sentences'
));
?>

<div id="annexe_content">
    <div class="section" md-whiteframe="1">
    <h2><?php __('Important'); ?></h2>
    <p>
    <?php
    __(
        'Please do not forget <strong>capital letters</strong> '.
        'and <strong>punctuation</strong>! Thank you.'
    );
    ?>
    </p>

    <p>
    <?php
    echo format(
        __(
            'Do not copy-paste sentences from elsewhere, '.
            'except if the content is CC-BY compatible. '.
            '<a href="{}">Learn more...</a>', true
        ),
        'http://blog.tatoeba.org/2011/01/legally-valid-content.html'
    );
    ?>
    </p>
    </div>
    
    <div class="section" md-whiteframe="1">
    <h2><?php __('Tips'); ?></h2>
    <p>
        <?php
        __(
            "You can add sentences that you do not know how to translate. ".
            "Perhaps someone else will know!"
        );
        ?>
    </p>
    </div>
</div>

<div id="main_content">
    
    <div class="section" md-whiteframe="1">
        <h2><?php __('Add new sentences'); ?></h2>

        <?php
        $langArray = $this->Languages->profileLanguagesArray(true, false);
        $currentUserLanguages = CurrentUser::getProfileLanguages();
        if (empty($currentUserLanguages)) {

            $this->Languages->displayAddLanguageMessage(true);

        } else {
            $preSelectedLang = $session->read('contribute_lang');

            if (!array_key_exists($preSelectedLang, $langArray)) {
                $preSelectedLang = key($langArray);
            }
            ?>

            <div layout="column">
                <div class="language-select" layout="row" layout-align="start center">
                    <label><? __('Language'); ?></label>
                    <?php
                    echo $form->select(
                        'contributionLang',
                        $langArray,
                        $preSelectedLang,
                        array(
                            "class" => "language-selector",
                            "empty" => false
                        ),
                        false
                    );
                    ?>
                </div>

                <md-input-container flex>
                    <label><? __('Sentence'); ?></label>
                    <input id="SentenceText" type="text" ng-model="ctrl.data.text"
                           autocomplete="off"
                           ng-disabled="ctrl.isAdding">
                </md-input-container>


                <div layout="row" layout-align="center center">
                    <md-button id="submitNewSentence" class="md-raised md-primary">
                        <? __('OK') ?>
                    </md-button>
                </div>
            </div>
            <?php
        }
        ?>

    </div>
    
    <div class="section" md-whiteframe="1">
        <h2><?php __('Sentences added'); ?></h2>
        
        <div class="sentencesAddedloading" style="display:none">
            <md-progress-circular md-mode="indeterminate" class="block-loader">
            </md-progress-circular>
        </div>
        
        <div id="sentencesAdded">
        <?php
        if (isset($sentence)) {
            $translation = array();            
            $sentences->displaySentencesGroup(
                $sentence['Sentence'],
                $sentence['Transcription'],
                $translation,
                $sentence['User']
            );
        }
        ?>
        </div>
    </div>

    <div class="section" md-whiteframe="1">
        <div layout="column" layout-align="center center">
            <? __('Check out the vocabulary for which we need sentences'); ?>
            <md-button class="md-primary" href="<?= $vocabularyUrl ?>">
                <? __('Sentences wanted') ?>
                <md-icon>keyboard_arrow_right</md-icon>
            </md-button>
        </div>
    </div>
</div>
