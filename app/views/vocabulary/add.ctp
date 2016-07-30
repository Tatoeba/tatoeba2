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
?>
<?php
$javascript->link('/js/vocabulary/add.ctrl.js', false);

$title = __('Add vocabulary items', true);

$this->set('title_for_layout', $pages->formatTitle($title));
?>

<div id="annexe_content">
    <?php echo $this->element('vocabulary/menu'); ?>

    <div class="section" layout="column" md-whiteframe="1">
        <h2><? __('Tips'); ?></h2>
        <p><?
        __(
            'Add vocabulary that you are learning. If your vocabulary does not '.
            'exist yet in Tatoeba, other contributors can add sentences for it.'
        );
        ?></p>
    </div>
</div>

<div ng-controller="VocabularyAddController as ctrl" id="main_content">

    <div class="section" layout="column" md-whiteframe="1">
        <h2><?= $title ?></h2>
        <form ng-submit="ctrl.add()">
            <div layout="row">
                <div class="language" layout="column">
                    <label for="lang-select"><? __('Language'); ?></label>
                    <?php
                    $langArray = $this->Languages->profileLanguagesArray(
                        false, false
                    );
                    $selectedLang = key($langArray);
                    echo $form->select(
                        null,
                        $langArray,
                        null,
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
                    <label><? __('Vocabulary item'); ?></label>
                    <input type="text" ng-model="ctrl.data.text"
                           autocomplete="off"
                           ng-disabled="ctrl.isAdding">
                </md-input-container>
            </div>

            <div layout="row" layout-align="center center">
                <md-button type="submit" class="md-raised md-primary"
                           ng-disabled="ctrl.isAdding || !ctrl.data.text || !ctrl.data.lang">
                    <? __('Add'); ?>
                </md-button>
            </div>
        </form>

    </div>

    <div class="section" md-whiteframe="1">
        <div layout="row">
            <h2 flex><? __('Vocabulary items added'); ?></h2>
            <md-progress-circular md-mode="indeterminate"
                                  md-diameter="32"
                                  ng-show="ctrl.isAdding">
            </md-progress-circular>
        </div>

        <md-list flex ng-show="ctrl.vocabularyAdded.length > 0">
            <md-list-item id="vocabulary_{{item.id}}"
                          ng-repeat="item in ctrl.vocabularyAdded">
                <img class="vocabulary-lang" ng-src="/img/flags/{{item.lang}}.png"/>
                <div class="vocabulary-text" flex>{{item.text}}</div>
                <md-button class="md-primary" href="{{item.url}}">
                    {{item.numSentencesLabel}}
                </md-button>
                <md-button ng-click="ctrl.remove(item.id)" class="md-icon-button">
                    <md-icon aria-label="Remove">delete</md-icon>
                </md-button>
            </md-list-item>
        </md-list>
    </div>

</div>
