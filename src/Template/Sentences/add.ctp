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
use App\Model\CurrentUser;

$this->set('title_for_layout', $this->Pages->formatTitle(__('Add sentences')));

$this->Sentences->javascriptForAJAXSentencesGroup();
$this->Html->script(JS_PATH . 'sentences.contribute.js', ['block' => 'scriptBottom']);

$vocabularyUrl = $this->Url->build(array(
    'controller' => 'vocabulary',
    'action' => 'add_sentences'
));
?>

<div id="annexe_content">
    <div class="section" md-whiteframe="1">
    <h2><?php echo __('Important'); ?></h2>
    <p>
    <?php
    echo __(
        "<strong>We like quality.</strong> Every detail matters. ".
        "Please do not forget punctuation and capital letters."
    );
    ?>
    </p>

    <p>
    <?php
    echo __(
        "<strong>We like diversity.</strong> Unleash your creativity! ".
        "Avoid using the same words, names, topics, or patterns over and over again."
    );
    ?>
    </p>

    <p>
    <?php
    echo __(
        "<strong>We like sharing our data.</strong> Avoid copy-pasting sentences, ".
        "or at least make sure they are not copyrighted and are compatible with the CC BY license. ".
        "Otherwise we cannot use them."
    );
    ?>
    </p>
    </div>
</div>

<div id="main_content">

    <div class="section" md-whiteframe="1">
        <h2><?php echo __('Add new sentences'); ?></h2>

        <?php
        $langArray = $this->Languages->profileLanguagesArray(true, false);
        $currentUserLanguages = CurrentUser::getProfileLanguages();
        if (empty($currentUserLanguages)) {

            $this->Languages->displayAddLanguageMessage(true);

        } else {
            $preSelectedLang = $this->request->getSession()->read('contribute_lang');
            echo $this->Form->create('Sentence', [
                'id' => 'sentence-form',
                'url' => '/sentences/add_an_other_sentence',
                'onsubmit' => 'return false',
                'ng-cloak' => true,
            ]);
            ?>

            <div layout="column">
                <div layout="row">
                    <div class="language-select" layout="row" layout-align="start center" flex>
                        <label><?= __('Language'); ?></label>
                        <?php
                        echo $this->Form->select(
                            'contributionLang',
                            $langArray,
                            array(
                                'id' => 'contributionLang',
                                "value" => $preSelectedLang,
                                "class" => "language-selector",
                                "empty" => false
                            ),
                            false
                        );
                        ?>
                    </div>

                    <?php if (CurrentUser::getSetting('can_switch_license')) : ?>
                    <div class="license-select" layout="row" layout-align="end center" flex>
                        <label><?= __('License'); ?></label>
                        <?php
                        echo $this->Form->select(
                            'sentenceLicense',
                            $this->Sentences->License->getLicenseOptions(),
                            array(
                                'id' => 'sentenceLicense',
                                "value" => CurrentUser::getSetting('default_license'),
                                "class" => "license-selector",
                                "empty" => false
                            ),
                            false
                        );
                        ?>
                    </div>
                    <?php endif; ?>
                </div>

                <md-input-container flex>
                    <label><?= __('Sentence'); ?></label>
                    <input id="SentenceText" type="text" ng-model="ctrl.data.text"
                           autocomplete="off"
                           ng-disabled="ctrl.isAdding">
                </md-input-container>


                <div layout="row" layout-align="center center">
                    <md-button id="submitNewSentence" class="md-raised md-primary">
                        <?= __('OK') ?>
                    </md-button>
                </div>
            </div>
            <?php
            echo $this->Form->end();
        }
        ?>

    </div>

    <div class="section" md-whiteframe="1">
        <h2><?php echo __('Sentences added'); ?></h2>

        <div class="sentencesAddedloading" style="display:none">
            <md-progress-circular md-mode="indeterminate" class="block-loader">
            </md-progress-circular>
        </div>

        <div id="sentencesAdded">
        <?php
        if (isset($sentence)) {
            $sentence['Translation'] = array();
            $this->Sentences->displaySentencesGroup($sentence);
        }
        ?>
        </div>
    </div>

    <div ng-cloak class="section" md-whiteframe="1">
        <div layout="column" layout-align="center center">
            <?= __('Check out the vocabulary for which we need sentences'); ?>
            <md-button class="md-primary" href="<?= $vocabularyUrl ?>">
                <?= __('Sentences wanted') ?>
                <md-icon>keyboard_arrow_right</md-icon>
            </md-button>
        </div>
    </div>
</div>
