<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2015  Gilles Bedel
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
 */

$layout = isset($isSidebar) && $isSidebar ? 'column' : 'row';

$this->Html->script('sentences/search.ctrl.js', ['block' => 'scriptBottom']);

echo $this->Form->create('AdvancedSearch', [
    'id' => 'advanced-search',
    'url' => false,
    'name' => 'form',
    'ng-controller' => 'SearchController as vm',
    'ng-submit' => "vm.submit(form, filters, 'search')",
]);
?>

<div layout="column" ng-app="app" ng-cloak>
    <?php if (!(isset($isSidebar) && $isSidebar) && $usesTemplate): ?>
        <div>
            <i><?= __("This form is pre-filled.")?></i>
            <md-button class="md-primary"
                        href="<?= $this->Url->build([
                            "controller" => "Sentences",
                            "action" => "advanced_search",
                            "?" => [],
                        ]);?>">
                <?= /* @translators: button appearing on advanced search page
                       after clicking "Create a search template" (verb) */
                    __('Clear form')
                ?>
            </md-button>
        </div>
        <md-divider></md-divider>
    <?php endif; ?>

    <div layout="column" layout-gt-sm="<?= $layout ?>"
         layout-align="start center" layout-align-gt-sm="center start">
        <div class="column-1" layout="column" flex>
        <?php /* @translators: section title in advanced search form */ ?>
        <md-subheader><?= __('Sentences'); ?></md-subheader>

            
        <div layout="column">
            <md-input-container class="md-button-right">
                <?php
                echo $this->Form->input('query', array(
                    'label' => __('Words:'),
                    'lang' => '',
                    'dir' => 'auto',
                    'id' => 'WordSearch',
                    'ng-model' => 'filters.query',
                    'ng-model-init' => $this->safeForAngular($query),
                ));
                ?>
                <md-button class="md-icon-button" reset-button target="WordSearch">
                    <md-icon>clear</md-icon>
                </md-button>
                <div class="hint"><?= __('Enter a word or a phrase') ?></div>
            </md-input-container>

            <div class="param" layout="<?= $layout ?>" layout-align="center">
                <label for="from" flex><?= __('Language:') ?></label>
                <?php
                echo $this->Search->selectLang('from', $from, [
                    'selectedLanguage' => 'filters.from',
                ]);
                ?>
            </div>

            <div class="param" layout="<?= $layout ?>" layout-align="center">
                <label for="to" flex><?= __('Show translations in:') ?></label>
                <?php
                echo $this->Search->selectLang('to', $to, [
                    'languages' => $this->Languages->languagesArrayShowTranslationsIn(false),
                    /* @translators: option used in language selection dropdown for
                                     "Show translations in" in advanced search form */
                    'placeholder' => __x('show-translations-in', 'All languages'),
                    'selectedLanguage' => 'filters.to',
                ]);
                ?>
            </div>

            <div class="param word-count">
                <div layout="row" layout-align="center">
                      <?php /* @translators: sentence length filter in the search form. */ ?>
                      <label flex><?= __('Length:') ?></label>
                      <div layout="column" layout-align="end">
                          <?php
                              $fields = [
                                  /* @translators: text inside the filter "Length" in the search form.
                                     You may move the numericField placeholder around. */
                                  'word_count_min' => __('At least {numericField}'),
                                  /* @translators: text inside the filter "Length" in the search form.
                                     You may move the numericField placeholder around. */
                                  'word_count_max' => __('At most {numericField}'),
                              ];
                              $mins = [
                                  'word_count_min' => '0',
                                  'word_count_max' => '{{filters.word_count_min}}',
                              ];
                              $maxs = [
                                  'word_count_min' => '{{filters.word_count_max}}',
                                  'word_count_max' => '',
                              ];
                              foreach ($fields as $field => $label) {
                                  $numericField = $this->Form->number($field, [
                                      'id' => $field,
                                      'min' => $mins[$field],
                                      'max' => $maxs[$field],
                                      'string-to-number' => '',
                                      'ng-model' => "filters.$field",
                                      'ng-model-init' => $this->get($field),
                                  ]);
                                  ?>
                                  <div layout="row" layout-align="end">
                                      <label for="<?= $field ?>">
                                          <?= format($label, compact('numericField')) ?>
                                      </label>
                                  </div>
                              <?php
                              }
                          ?>
                      </div>
                      <div layout="column" class="infobox">
                          <md-icon>help</md-icon>
                          <md-tooltip class="multiline" md-direction="top">
                              <?= __('For languages with word boundaries, the number of words is used. For other languages, the number of characters is used.') ?>
                          </md-tooltip>
                      </div>
                </div>
            </div>

            <md-input-container class="md-button-right">
                <?php
                echo $this->Form->input('user', array(
                    'label' => __('Owner:'),
                    'id' => 'OwnerSearch',
                    'ng-model' => 'filters.user',
                    'ng-model-init' => $user,
                ));
                ?>
                <md-button class="md-icon-button" reset-button target="OwnerSearch">
                    <md-icon>clear</md-icon>
                </md-button>
                <div class="hint"><?= __('Enter a username') ?></div>
            </md-input-container>

            <div class="param" layout="row">
                <md-checkbox
                    ng-false-value="''"
                    ng-true-value="'yes'"
                    ng-model="filters.original"
                    ng-model-init="<?= h($original) ?>"
                    class="md-primary">
                    <?= __('Is original') ?>
                </md-checkbox>
                <div class="infobox">
                    <md-icon>help</md-icon>
                    <md-tooltip class="multiline" md-direction="top">
                        <?= __('Original sentences are ones that were not added as translations of other sentences.') ?>
                    </md-tooltip>
                </div>
            </div>

            <div class="param">
                <div layout="row" layout-align="center">
                    <label for="orphans" flex><?= __('Is orphan:') ?></label>
                    <?php
                    echo $this->Form->input('orphans', [
                        'label' => '',
                        'options' => [
                            /* @translators: dropdown option of "Is orphan" field in search form */
                            'any' => __x('orphan', 'Any'),
                            /* @translators: part of Any/No/Yes dropdown options in search form */
                            'no' => __('No'),
                            /* @translators: part of Any/No/Yes dropdown options in search form */
                            'yes' => __('Yes'),
                        ],
                        'ng-model' => 'filters.orphans',
                        'ng-model-init' => $orphans,
                    ]);
                    ?>
                </div>
                <div class="hint">
                    <?= __('Orphan sentences are likely to be incorrect.') ?>
                </div>
            </div>

            <div class="param">
                <div layout="row" layout-align="center">
                    <label for="unapproved" flex><?= __('Is unapproved:') ?></label>
                    <?php
                    echo $this->Form->input('unapproved', array(
                        'label' => '',
                        'options' => array(
                            /* @translators: dropdown option of "Is unapproved" field in search form */
                            'any' => __x('unapproved', 'Any'),
                            'no' => __('No'),
                            'yes' => __('Yes'),
                        ),
                        'ng-model' => 'filters.unapproved',
                        'ng-model-init' => $unapproved,
                    ));
                    ?>
                </div>
                <div class="hint">
                    <?= __('Unapproved sentences are likely to be incorrect.') ?>
                </div>
            </div>

            <div class="param" layout="row" layout-align="center">
                <label for="has-audio" flex><?= __('Has audio:') ?></label>
                <?php
                echo $this->Form->input('has_audio', array(
                    'label' => '',
                    'options' => array(
                        /* @translators: dropdown option of "Has audio" field in search form */
                        '' => __x('audio', 'Any'),
                        'no' => __('No'),
                        'yes' => __('Yes'),
                    ),
                    'ng-model' => 'filters.has_audio',
                    'ng-model-init' => $has_audio,
                ));
                ?>
            </div>

            <md-input-container class="md-button-right">
            <?php
            echo $this->Form->input('tags', array(
                'label' => __('Tags:'),
                'id' => 'TagSearch',
                'ng-model' => 'filters.tags',
                'ng-model-init' => $this->safeForAngular($tags),
            ));
            ?>
            <md-button class="md-icon-button" reset-button target="TagSearch">
                <md-icon>clear</md-icon>
            </md-button>
            <div class="hint">
                <?= __('Separate tags with commas.') ?>
            </div>
            </md-input-container>

            <div class="param" layout="<?= $layout ?>" layout-align="center">
                <label for="list" flex><?= __('Belongs to list:') ?></label>
                <div flex>
                <?php
                $listOptions = $this->Lists->listsAsSelectable($searchableLists->toList());
                echo $this->Form->input('list', [
                    'class' => 'list-select',
                    'label' => '',
                    'options' => $this->safeForAngular($listOptions),
                    'ng-model' => 'filters.list',
                    'ng-model-init' => $list,
                ]);
                ?>
                </div>
            </div>

            <div class="param" layout="row">
                <md-checkbox
                    ng-false-value="''"
                    ng-true-value="'yes'"
                    ng-model="filters.native"
                    ng-model-init="<?= h($native) ?>"
                    class="md-primary">
                    <?= __('Owned by a self-identified native') ?>
                </md-checkbox>
            </div>
        </div>
        </div>

        <md-divider></md-divider>

        <div class="column-2" <?= isset($isSidebar) && $isSidebar ? '' : 'flex' ?>>
            <?php /* @translators: section title in advanced search form */ ?>
            <md-subheader><?php echo __('Translations'); ?></md-subheader>

            <div layout="column">
                <div class="param">
                <?php
                $filterOption = $this->Form->select(
                    'trans_filter',
                    array(
                        /* @translators This is inserted into another sentence
                                        that begins with {action} */
                        'limit' => __('Limit to'),
                        /* @translators This is inserted into another sentence
                                        that begins with {action} */
                        'exclude' => __('Exclude'),
                    ),
                    array(
                        'empty' => false,
                        'ng-model' => 'filters.trans_filter',
                        'ng-model-init' => $trans_filter,
                    )
                );
                $label = format(
                    __('{action} sentences having translations that match'
                    .' all the following criteria.', true),
                    array('action' => $filterOption)
                );
                echo "<label>$label</label>";
                ?>
                </div>

                <div class="param" layout="<?= $layout ?>" layout-align="center">
                    <label for="trans-to" flex><?= __('Language:') ?></label>
                    <?php
                    echo $this->Search->selectLang('trans_to', $trans_to, [
                        'selectedLanguage' => 'filters.trans_to',
                    ]);
                    ?>
                </div>

                <div class="param" layout="row" layout-align="center">
                    <label for="trans-link" flex><?= __('Link:') ?></label>
                    <?php
                    echo $this->Form->input('trans_link', array(
                        'label' => '',
                        'options' => array(
                            /* @translators: dropdown option of "Link" field in search form */
                            '' => __x('link', 'Any'),
                            /* @translators: dropdown option of "Link" field in search form (noun) */
                            'direct' => __('Direct'),
                            /* @translators: dropdown option of "Link" field in search form (noun) */
                            'indirect' => __('Indirect'),
                        ),
                        'ng-model' => 'filters.trans_link',
                        'ng-model-init' => $trans_link,
                    ));
                    ?>
                </div>

                <md-input-container class="md-button-right">
                    <?php
                    echo $this->Form->input('trans_user', array(
                        'label' => __('Owner:'),
                        'id' => 'TranslatorSearch',
                        'ng-model' => 'filters.trans_user',
                        'ng-model-init' => $trans_user,
                    ));
                    ?>
                    <md-button class="md-icon-button" reset-button target="TranslatorSearch">
                        <md-icon>clear</md-icon>
                    </md-button>
                    <div class="hint"><?= __('Enter a username') ?></div>
                </md-input-container>

                <div class="param">
                    <div layout="row" layout-align="center">
                        <label for="trans-orphan" flex><?= __('Is orphan:') ?></label>
                        <?php
                        echo $this->Form->input('trans_orphan', array(
                            'label' => '',
                            'options' => array(
                                '' => __x('orphan', 'Any'),
                                'no' => __('No'),
                                'yes' => __('Yes'),
                            ),
                            'ng-model' => 'filters.trans_orphan',
                            'ng-model-init' => $trans_orphan,
                        ));
                        ?>
                    </div>
                    <div class="hint">
                        <?= __('Orphan sentences are likely to be incorrect.') ?>
                    </div>
                </div>

                <div class="param">
                    <div layout="row" layout-align="center">
                        <label for="trans-unapproved" flex><?= __('Is unapproved:') ?></label>
                        <?php
                        echo $this->Form->input('trans_unapproved', array(
                            'label' => '',
                            'options' => array(
                                '' => __x('unapproved', 'Any'),
                                'no' => __('No'),
                                'yes' => __('Yes'),
                            ),
                            'ng-model' => 'filters.trans_unapproved',
                            'ng-model-init' => $trans_unapproved,
                        ));
                        ?>
                    </div>
                    <div class="hint">
                        <?= __('Unapproved sentences are likely to be incorrect.') ?>
                    </div>
                </div>

                <div class="param" layout="row" layout-align="center">
                    <label for="trans-has-audio" flex><?= __('Has audio:') ?></label>
                    <?php
                    echo $this->Form->input('trans_has_audio', array(
                        'label' => '',
                        'options' => array(
                            '' => __x('audio', 'Any'),
                            'no' => __('No'),
                            'yes' => __('Yes'),
                        ),
                        'ng-model' => 'filters.trans_has_audio',
                        'ng-model-init' => $trans_has_audio,
                    ));
                    ?>
                </div>
            </div>

            <md-divider></md-divider>

            <?php /* @translators: section title in search form (noun) */ ?>
            <md-subheader><?php echo __('Sort'); ?></md-subheader>

            <div layout="column">
                <div class="param" layout="<?= $layout ?>" layout-align="center">
                    <?php /* @translators: field name in search form (noun) */ ?>
                    <label for="sort" flex><?= __('Order:') ?></label>
                    <?php
                    echo $this->Form->input('sort', array(
                        'label' => '',
                        'options' => array(
                            /* @translators: sort order dropdown option in search form */
                            'relevance' => __('Relevance'),
                            'words' => __('Fewest words first'),
                            'created' => __('Last created first'),
                            'modified' => __('Last modified first'),
                            /* @translators: sort order dropdown option in advanced search form (noun) */
                            'random' => __('Random'),
                        ),
                        'ng-model' => 'filters.sort',
                        'ng-model-init' => $sort,
                    ));
                    ?>
                </div>

                <div class="param" layout="row">
                    <md-checkbox
                        ng-false-value="''"
                        ng-true-value="'yes'"
                        ng-model="filters.sort_reverse"
                        ng-model-init="<?= h($sort_reverse) ?>"
                        class="md-primary">
                        <?= __('Reverse order') ?>
                    </md-checkbox>
                </div>
            </div>
        </div>
    </div>

    <md-divider></md-divider>

    <div class="buttons" layout="column" layout-gt-sm="<?= $layout ?>">
        <md-button type="submit" class="md-primary md-raised">
            <?php /* @translators: search form submit button (verb) */ ?>
            <?= __x('button', 'Search') ?>
        </md-button>

        <md-button class="md-primary" target="_blank"
                   href="<?= h($this->Pages->getWikiLink('text-search')) ?>">
            <?= __('More search options') ?>
        </md-button>
        <?php if (!(isset($isSidebar) && $isSidebar)): ?>
            <md-button class="md-primary" ng-click="vm.submit(form, filters, 'advanced_search')">
                <?= __('Create a search template') ?>
            </md-button>
            <span>
                <md-icon>help</md-icon>
                <md-tooltip class="multiline" md-direction="top">
                    <?= __('Use this button to use the currently selected criteria as a base for other searches. '
                          .'You can also bookmark/share the search template with someone else.') ?>
                </md-tooltip>
            </span>
        <?php endif; ?>
    </div>
</div>

<?php
echo $this->Form->end();
