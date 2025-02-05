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
 * @link     https://tatoeba.org
 */
?>
<?php
$this->Html->script('/js/vocabulary/add.ctrl.js', ['block' => 'scriptBottom']);

$title = __('Add vocabulary items');

$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>

<div ng-cloak id="annexe_content">
    <?php echo $this->element('vocabulary/menu'); ?>

    <div class="section md-whiteframe-1dp" layout="column">
        <?php /* @translators: title of the help text on the Add vocabulary page */ ?>
        <h2><?= __('Tips'); ?></h2>
        <p><?= __(
            'Add vocabulary that you are learning. If your vocabulary does not '.
            'exist yet in Tatoeba, other contributors can add sentences for it.'
        );
        ?></p>
    </div>
</div>

<div ng-controller="VocabularyAddController as ctrl" id="main_content">

    <section class="md-whiteframe-1dp">
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2><?= $title ?></h2>
            </div>
        </md-toolbar>
    
        <?= $this->Form->create('Vocabulary', [
            'ng-cloak' => true,
            'id' => 'add-vocabulary-form',
            'ng-submit' => 'ctrl.add()',
            'url' => ['action' => 'save'],
            'onsubmit' => 'return false',
            'layout-padding'
        ]) ?>
            <div layout="row">
                <div class="language" layout="column">
                    <?php /* @translators: language field label in new vocabulary request form */ ?>
                    <label for="lang-select"><?= __('Language'); ?></label>
                    <?php
                    $langArray = $this->Languages->profileLanguagesArray();
                    $selectedLang = key($langArray);
                    echo $this->Form->select(
                        'lang',
                        $langArray,
                        array(
                            'id' => 'lang-select',
                            'ng-model' => 'ctrl.data.lang',
                            'ng-init' => "ctrl.data.lang = '$selectedLang'",
                            'empty' => false
                        ),
                        false
                    );
                    ?>
                </div>

                <md-input-container flex>
                    <?= $this->Form->input('text', [
                        'label' => __('Vocabulary item'),
                        'ng-model' => 'ctrl.data.text',
                        'ng-disabled' => 'ctrl.isAdding',
                        'autocomplete' => 'off',
                        'focus-input' => 'focusInput'
                    ]);
                    ?>
                </md-input-container>
            </div>

            <div layout="row" layout-align="center center">
                <md-button type="submit" class="md-raised md-primary"
                           ng-disabled="ctrl.isAdding || !ctrl.data.text || !ctrl.data.lang">
                    <?php /* @translators: button to add a vocabulary request */ ?>
                    <?= __('Add'); ?>
                </md-button>
            </div>
        <?= $this->Form->end() ?>

    </section>

    <section class="md-whiteframe-1dp">
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2 flex><?= __('Vocabulary items added'); ?></h2>
                <md-progress-circular md-mode="indeterminate"
                                      md-diameter="32"
                                      ng-show="ctrl.isAdding">
                </md-progress-circular>
            </div>
        </md-toolbar>

        <md-list flex ng-show="ctrl.vocabularyAdded.length > 0">
            <md-list-item id="vocabulary_{{item.id}}"
                          ng-repeat="item in ctrl.vocabularyAdded">
                <img class="vocabulary-lang language-icon" width="30" height="20"
                     ng-src="/img/flags/{{item.lang}}.svg"/>
                <div class="vocabulary-text" flex>{{item.text}}</div>
                <md-icon ng-show="item.duplicate">warning</md-icon>
                <md-tooltip md-direction="top" ng-show="item.duplicate">
                    <?= __('You have already added this vocabulary item.') ?>
                </md-tooltip>
                <md-button class="md-primary" href="{{item.url}}" ng-disabled="!item.url">
                    {{item.numSentencesLabel}}
                </md-button>
                <md-button ng-click="ctrl.remove(item.id)" class="md-icon-button">
                    <md-icon aria-label="Remove">delete</md-icon>
                </md-button>
            </md-list-item>
        </md-list>
    </div>

</div>
