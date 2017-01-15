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
$this->Js->link('/js/vocabulary/add-sentences.ctrl.js', false);

$title = __('Vocabulary that needs sentences');

$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>

<div id="annexe_content">
    <?php echo $this->element('vocabulary/menu'); ?>

    <?php $this->CommonModules->createFilterByLangMod(); ?>
</div>

<div id="main_content" ng-controller="VocabularyAddSentencesController as ctrl">
    <div class="section" md-whiteframe="1">
        <h2><?= $title ?></h2>

        <p>
            <?
            __(
                'Only vocabulary items that have less than 10 sentences are '.
                'displayed here.'
            )
            ?>
        </p>
        <?php
        $paginationUrl = array($langFilter);
        $this->Pagination->display($paginationUrl);
        ?>

        <md-list flex>
            <?php
            foreach($vocabulary as $item) {
                $id = $item['Vocabulary']['id'];
                $lang = $item['Vocabulary']['lang'];
                $text = $item['Vocabulary']['text'];
                $numSentences = $item['Vocabulary']['numSentences'];
                $url = $this->Html->url(array(
                    'controller' => 'sentences',
                    'action' => 'search',
                    '?' => array(
                        'query' => '="' . $text . '"',
                        'from' => $lang
                    )
                ));
                ?>
                <md-list-item id="vocabulary_<?= $id ?>">
                    <img class="vocabulary-lang" src="/img/flags/<?= $lang ?>.png"/>
                    <div class="vocabulary-text" flex><?= $text ?></div>
                    <md-button class="md-primary" href="<?= $url ?>">
                        <?= format(
                            __n(
                                '{number} sentence', '{number} sentences',
                                $numSentences,
                                true
                            ),
                            array('number' => $numSentences)
                        ); ?>
                    </md-button>
                    <md-button ng-click="ctrl.showForm('<?= $id ?>')"
                               class="md-icon-button">
                        <md-icon aria-label="Add">add</md-icon>
                    </md-button>
                </md-list-item>
                <div id="sentences_<?= $id ?>" class="new-sentences"
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
                                <? __('This sentence already exists.') ?>
                            </md-tooltip>
                        </md-button>
                        <div class="text" flex>{{sentence.text}}</div>
                    </div>
                </div>

                <div id="loader_<?= $id ?>" flex ng-show="false">
                    <md-progress-linear></md-progress-linear>
                </div>

                <form id="form_<?= $id ?>" class="sentence-form"
                      layout="column" flex ng-show="false"
                      ng-submit="ctrl.saveSentence('<?= $id ?>', '<?= $lang ?>')">
                    <md-input-container flex>
                        <label><? __('Sentence'); ?></label>
                        <input type="text" ng-disabled="ctrl.isAdding"
                               ng-model="ctrl.sentence['<?= $id ?>']">
                    </md-input-container>
                    <div layout="row" layout-align="end center">
                        <md-button class="md-raised"
                                   ng-disabled="ctrl.isAdding"
                                   ng-click="ctrl.hideForm('<?= $id ?>')">
                            <? __('Cancel') ?>
                        </md-button>
                        <md-button type="submit" class="md-raised md-primary"
                                   ng-disabled="ctrl.isAdding">
                            <? __('Submit') ?>
                        </md-button>
                    </div>
                </form>
                <?php
            }
            ?>
        </md-list>

        <?php
        $this->Pagination->display($paginationUrl);
        ?>
    </div>

</div>