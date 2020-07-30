<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 Allan SIMON <allan.simon@supinfo.com>
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
 * @author   Alexandre Magueresse <alexandre.magueresse@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

$this->set('title_for_layout', $this->Pages->formatTitle(__('Super tags management')));
echo $this->Html->css('autocompletion.css');
$this->AssetCompress->script('sentences-block-for-members.js', ['block' => 'scriptBottom']);
$this->Html->script('autocompletion.js', ['block' => 'scriptBottom']);
$this->Html->script('tagsSuperTags.js', ['block' => 'scriptBottom']);

?>

<div id="main_content">
<section class="md-whiteframe-1dp">
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2><?= __('Create a super tag') ?></h2>
            </div>
        </md-toolbar>
        
        <div class="section md-whiteframe-1dp">
            <?php
                if (isset($this->request['?']) && isset($this->request['?']['superTagAdded']) && $this->request['?']['superTagAdded'] == '0'){
            ?>
            <div class="warning">
                <?php __('This super tag could not be created (it may already exist).') ?>
            </div>
            <?php
                }
            ?>

            <?php
                echo $this->Form->create(
                    'CreateSuperTag', 
                    [
                        'url' => [
                            'action' => 'createSuperTag'
                        ],
                    ]
                );
            ?>

            <md-input-container layout="column">
                <?php
                    echo $this->Form->input(
                        'name', 
                        [
                            'label' => __('Name (required)')
                        ]
                    );
                ?>
            </md-input-container>
                    
            <md-input-container layout="column">
                <?php
                    echo $this->Form->input(
                        'description', 
                        [
                            'label' => __('Description (optional)')
                        ]
                    );
                ?>
            </md-input-container>

            <md-input-container layout="column">
                <md-button type="submit" class="md-raised md-default">
                    <?= __('Create') ?>
                </md-button>
            </md-input-container>
            <?php
                echo $this->Form->end();
            ?>
        </div>
    </section>

    <section class="md-whiteframe-1dp">
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2><?= __('Add a hierarchy') ?></h2>
            </div>
        </md-toolbar>
        
        <div class="section md-whiteframe-1dp">
            <?php
                if (isset($this->request['?']) && isset($this->request['?']['tagSuperTagAdded']) && $this->request['?']['tagSuperTagAdded'] == '0'){
            ?>
            <div class="warning">
                <?php __('This link could not be added: parent must refer to a super tag, child must referer to the corresponding child type, and cycling hierarchies are forbidden.') ?>
            </div>
            <?php
                }
            ?>

            <?php
                echo $this->Form->create(
                    'CreateTagSuperTag', 
                    [
                        'url' => [
                            'action' => 'createTagSuperTag'
                        ],
                    ]
                );
            ?>

            <md-input-container layout="column">
                <?php
                    echo $this->Form->input(
                        'parent', 
                        [
                            'label' => __('Parent (super tag)')
                        ]
                    );
                ?>
                <div id="autocompletionParent"></div>
            </md-input-container>

            <md-input-container layout="column">
                <?php
                    echo $this->Form->select(
                        'childType',
                        [__('Tag'), __('Super tag')],
                        [
                            'label' => __('Child type'),
                            'id' => 'child_type',
                            'empty' => __('Child type (choose one)'),
                        ]
                    );
                ?>
            </md-input-container>
                    
            <md-input-container layout="column">
                <?php
                    echo $this->Form->input(
                        'child',
                        [
                        'label' => __('Child (tag or super tag)')
                        ]
                    );
                ?>
                <div id="autocompletionChild"></div>
            </md-input-container>

            <md-input-container layout="column">
                <md-button type="submit" class="md-raised md-default">
                    <?= __('Add') ?>
                </md-button>
            </md-input-container>
            <?php
                echo $this->Form->end();
            ?>
        </div>
    </section>

    <div ng-app="app" ng-controller="TagsSuperTagsController as vm">
        <?php
            foreach ($all_super_tags_links as $parent => $children) {
                echo $this->element('superTag', [
                    'root' => -1,
                    'depth' => 0,
                    'parent' => $parent,
                    'children' => $children,
                ]);
            }
        ?>
    </div>
</div>
