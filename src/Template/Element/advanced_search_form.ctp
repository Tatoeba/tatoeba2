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

echo $this->Form->create('AdvancedSearch', [
    'id' => 'advanced-search',
    'type' => 'get',
    'url' => [
        'controller' => 'sentences',
        'action' => 'search',
    ]
]);
?>

<div layout="column" ng-app="app" ng-cloak>
    <div layout="<?= $layout ?>">
        <div class="column-1" layout="column" flex>
            <h3><?= __('Sentences'); ?></h3>

            <md-input-container>
                <?php
                echo $this->Form->input('query', array(
                    'label' => __('Words:'),
                    'value' => $query,
                    'lang' => '',
                    'dir' => 'auto',
                ));
                ?>
                <div class="hint"><?= __('Enter a word or a phrase') ?></div>
            </md-input-container>

            <div class="param" layout="<?= $layout ?>" layout-align="center">
                <label for="from" flex><?= __('Language:') ?></label>
                <?php
                echo $this->Search->selectLang('from', $from, ['label' => '']);
                ?>
            </div>

            <div class="param" layout="<?= $layout ?>" layout-align="center">
                <label for="to" flex><?= __('Show translations in:') ?></label>
                <?php
                echo $this->Search->selectLang('to', $to, [
                    'label' => '',
                    'options' => $this->Languages->languagesArrayForPositiveLists(),
                ]);
                ?>
            </div>

            <md-input-container>
                <?php
                echo $this->Form->input('user', array(
                    'label' => __('Owner:'),
                    'value' => $user,
                ));
                ?>
                <div class="hint"><?= __('Enter a username') ?></div>
            </md-input-container>

            <div class="param">
                <div layout="row" layout-align="center">
                    <label for="orphans" flex><?= __('Is orphan:') ?></label>
                    <?php
                    echo $this->Form->input('orphans', [
                        'label' => '',
                        'options' => [
                            '' => __x('orphan', 'Any'),
                            'no' => __('No'),
                            'yes' => __('Yes'),
                        ],
                        'value' => $orphans,
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
                            '' => __x('unapproved', 'Any'),
                            'no' => __('No'),
                            'yes' => __('Yes'),
                        ),
                        'value' => $unapproved,
                    ));
                    ?>
                </div>
                <div class="hint">
                    <?= __('Unapproved sentences are likely to be incorrect.') ?>
                </div>
            </div>
            
            <div class="param" layout="row" layout-align="center">
                <label for="has_audio" flex><?= __('Has audio:') ?></label>
                <?php
                echo $this->Form->input('has-audio', array(
                    'label' => '',
                    'options' => array(
                        '' => __x('audio', 'Any'),
                        'no' => __('No'),
                        'yes' => __('Yes'),
                    ),
                    'value' => $has_audio,
                ));
                ?>
            </div>

            <md-input-container>
            <?php
            echo $this->Form->input('tags', array(
                'label' => __('Tags:'),
                'value' => $tags
            ));
            ?>
            <div class="hint">
                <?= __('Separate tags with commas.') ?>
            </div>
            </md-input-container>

            <div class="param" layout="<?= $layout ?>" layout-align="center">
                <label for="list" flex><?= __('Belongs to list:') ?></label>
                <div flex>
                <?php
                $listOptions = $this->Lists->listsAsSelectable($searchableLists);
                echo $this->Form->input('list', [
                    'class' => 'list-select',
                    'label' => '',
                    'value' => $list,
                    'options' => $listOptions,
                ]);
                ?>
                </div>
            </div>

            <div class="param" layout="row" layout-align="center">
                <md-checkbox
                    ng-false-value="0"
                    ng-true-value="1"
                    ng-model="native"
                    ng-init="native = <?= isset($native) && $native == 'yes' ? 1 : 0 ?>"
                    class="md-primary">
                </md-checkbox>
                <div ng-hide="true">
                    <?php
                    echo $this->Form->input('native', [
                        'value' => '{{native ? "yes" : ""}}',
                    ]);
                    ?>
                </div>
                <label for="native" flex><?= __('Owned by a self-identified native') ?></label>
            </div>
        </div>

        <div class="column-2" <?= isset($isSidebar) && $isSidebar ? '' : 'flex' ?>>
            <div layout="column">
                <h3><?php echo __('Translations'); ?></h3>

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
                        'value' => $trans_filter,
                        'empty' => false
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
                    echo $this->Search->selectLang('trans_to', $trans_to, array(
                        'label' => '',
                        'options' => $this->Languages->getSearchableLanguagesArray(),
                    ));
                    ?>
                </div>

                <div class="param" layout="row" layout-align="center">
                    <label for="trans-link" flex><?= __('Link:') ?></label>
                    <?php
                    echo $this->Form->input('trans_link', array(
                        'label' => '',
                        'options' => array(
                            '' => __x('link', 'Any'),
                            'direct' => __('Direct'),
                            'indirect' => __('Indirect'),
                        ),
                        'value' => $trans_link,
                    ));
                    ?>
                </div>

                <md-input-container>
                    <?php
                    echo $this->Form->input('trans_user', array(
                        'label' => __('Owner:'),
                        'value' => $trans_user,
                    ));
                    ?>
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
                            'value' => $trans_orphan,
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
                            'value' => $trans_unapproved,
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
                        'value' => $trans_has_audio,
                    ));
                    ?>
                </div>
            </div>

            <div layout="column">
                <h3><?php echo __('Sort'); ?></h3>

                <div class="param" layout="<?= $layout ?>" layout-align="center">
                    <label for="sort" flex><?= __('Order:') ?></label>
                    <?php
                    echo $this->Form->input('sort', array(
                        'label' => '',
                        'options' => array(
                            'relevance' => __('Relevance'),
                            'words' => __('Fewest words first'),
                            'created' => __('Last created first'),
                            'modified' => __('Last modified first'),
                            'random' => __('Random'),
                        ),
                        'value' => $sort,
                    ));
                    ?>
                </div>

                <div class="param" layout="row" layout-align="center">
                    <md-checkbox
                        ng-false-value="0"
                        ng-true-value="1"
                        ng-model="sortReverse"
                        ng-init="sortReverse = <?= $sort_reverse == 'yes' ? 1 : 0 ?>"
                        class="md-primary">
                    </md-checkbox>
                    <div ng-hide="true">
                        <?php
                        echo $this->Form->input('sort_reverse', [
                            'value' => '{{sortReverse ? "yes" : ""}}',
                        ]);
                        ?>
                    </div>
                    <label for="sort-reverse" flex><?= __('Reverse order') ?></label>
                </div>
            </div>
        </div>
    </div>

    <div class="buttons" layout="<?= $layout ?>">
        <md-button type="submit" class="md-primary md-raised">
            <?= __x('button', 'Search') ?>
        </md-button>

        <md-button class="md-primary" target="_blank"
                   href="http://en.wiki.tatoeba.org/articles/show/text-search">
            <?= __('More search options') ?>
        </md-button>
    </div>
</div>

<?php
echo $this->Form->end();
