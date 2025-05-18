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
$this->Html->script('/js/vocabulary/add-sentences.ctrl.js', ['block' => 'scriptBottom']);
if (empty($langFilter)) {
    $title = __('Vocabulary that needs sentences');
} else {
    $title = format(
        __('Vocabulary that needs sentences in {language}'),
        array('language' => $this->Languages->codeToNameToFormat($langFilter))
    );
}
$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>

<div ng-cloak id="annexe_content">
    <?php echo $this->element('vocabulary/menu'); ?>

    <?php $this->CommonModules->createFilterByLangMod(); ?>
</div>

<div id="main_content" ng-controller="VocabularyAddSentencesController as ctrl">
    <section class="md-whiteframe-1dp">
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2><?= $title ?></h2>
            </div>
        </md-toolbar>

        <md-content>
        <div layout-padding>
            <?= __(
                'Only vocabulary items that match fewer than 10 sentences are '.
                'displayed here.'
            )
            ?>
        </div>
        <?php
            if ($vocabulary->count() == 0) {
                echo $this->Html->div(
                    'empty-info-text',
                    __('There are no requests.')
                );
            }
        ?>

        <?php
        $this->Pagination->display();
        ?>

        <md-list flex>
            <?php
            foreach($vocabulary as $item) {
                $id = $item->id;
                $lang = $item->lang;
                ?>
                <md-list-item id="vocabulary_<?= $id ?>">
                    <?= $this->Vocabulary->vocabulary($item); ?>
                    <md-button ng-cloak ng-click="ctrl.showForm('<?= $id ?>')"
                               class="md-icon-button">
                        <md-icon aria-label="Add">add</md-icon>
                    </md-button>
                </md-list-item>
                <div ng-cloak id="sentences_<?= $id ?>" class="new-sentences"
                     ng-show="ctrl.sentencesAdded['<?= $id ?>']">
                    <div ng-repeat="sentence in ctrl.sentencesAdded['<?= $id ?>']"
                         class="new-sentence"
                         layout="row" layout-align="start center">
                        <md-button class="md-icon-button"
                                   ng-href="{{sentence.url}}">
                            <md-icon ng-hide="sentence.duplicate">forward</md-icon>
                            <md-icon ng-show="sentence.duplicate">warning</md-icon>
                            <md-tooltip md-direction="top"
                                        ng-show="sentence.duplicate">
                                <?= __('This sentence already exists.') ?>
                            </md-tooltip>
                        </md-button>
                        <div class="text" flex>{{sentence.text}}</div>
                    </div>
                </div>

                <div ng-cloak id="loader_<?= $id ?>" flex ng-show="false">
                    <md-progress-linear></md-progress-linear>
                </div>

                <?= $this->Form->create('Vocabulary', [
                    'id' => 'form_'.$id,
                    'url' => ['controller' => 'vocabulary', 'action' => 'save_sentence', $id],
                    'class' => 'sentence-form',
                    'layout' => 'column',
                    'flex' => '',
                    'ng-show' => 'false',
                    'ng-cloak',
                    'ng-submit' => "ctrl.saveSentence($id, '$lang')",
                    'onsubmit' => 'return false'
                ]); ?>
                    <?= $this->Form->hidden('lang', ['value' => $lang]) ?>
                    <md-input-container flex>
                        <?= $this->Form->input('text', [
                            'id' => 'form_'.$id.'_input',
                            /* @translators: sentence text field label of sentence addition form on vocabulary page */
                            'label' => __('Sentence'),
                            'ng-model' => "ctrl.sentence['$id']"
                        ]); ?>
                    </md-input-container>
                    <div layout="row" layout-align="end center">
                        <md-button class="md-raised"
                                   ng-disabled="ctrl.isAdding"
                                   ng-click="ctrl.hideForm('<?= $id ?>')">
                            <?php /* @translators: cancel button of sentence addition form on wanted vocabulary requests page (verb) */ ?>
                            <?= __('Cancel') ?>
                        </md-button>
                        <md-button type="submit" class="md-raised md-primary"
                                   ng-disabled="ctrl.isAdding">
                            <?php /* @translators: button to submit new sentence from wanted vocabulary requests page (verb) */ ?>
                            <?= __('Submit') ?>
                        </md-button>
                    </div>
                <?= $this->Form->end() ?>
                <?php
            }
            ?>
        </md-list>

        <?php
        $this->Pagination->display();
        ?>
        </md-content>
    </section>

</div>
