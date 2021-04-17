<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2020 Tatoeba Project
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

$this->set('title_for_layout', $this->Pages->formatTitle(__d('beta', 'Tags categories management')));
echo $this->Html->css('autocompletion.css');
$this->AssetCompress->script('sentences-block-for-members.js', ['block' => 'scriptBottom']);
$this->Html->script('autocompletion.js', ['block' => 'scriptBottom']);
$this->Html->script('categoriesTree.js', ['block' => 'scriptBottom']);

$messages = [
    'createCategory' => __d('beta', 'This tags category could not be created (category names should be unique and cycles are forbidden).'),
    'removeCategory' => __d('beta', 'This tags category could not be removed (a category should be empty to be deleted).'),
    'attachTagToCategory' => __d('beta', 'This tag could not be attached to this category.'),
    'detachTagFromCategory' => __d('beta', 'This tag could not be detached from its category.')
];
?>

<div id="main_content">
    <?php
        if (isset($this->request['?'])) {
            $action = array_keys($this->request['?'])[0];
            $error = ($this->request['?'][$action] == '0');
            if (array_key_exists($action, $messages) && $error) {
    ?>
        <div class="warning">
            <?= $messages[$action] ?>
        </div>
    <?php
            }
        }
    ?>

    <section class="md-whiteframe-1dp">
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2><?= __d('beta', 'Create or edit a tags category') ?></h2>
            </div>
        </md-toolbar>
        
        <div class="section md-whiteframe-1dp">
            <?php
                echo $this->Form->create(
                    'CreateOrEditCategory', 
                    [
                        'url' => [
                            'controller' => 'categoriesTree',
                            'action' => 'createOrEditCategory'
                        ],
                    ]
                );
            ?>

            <md-input-container layout="column">
                <?php
                    echo $this->Form->input(
                        'name', 
                        [
                            'label' => __d('beta', 'Name (required)')
                        ]
                    );
                ?>
            </md-input-container>
                    
            <md-input-container layout="column">
                <?php
                    echo $this->Form->input(
                        'description', 
                        [
                            'label' => __d('beta', 'Description (optional)')
                        ]
                    );
                ?>
            </md-input-container>

            <md-input-container layout="column">
                <?php
                    echo $this->Form->input(
                        'parentName', 
                        [
                            'id' => 'parentName',
                            'label' => __d('beta', 'Parent (tags category)')
                        ]
                    );
                ?>
                <div id="autocompletionParent"></div>
            </md-input-container>

            <md-input-container layout="column">
                <md-button type="submit" class="md-raised md-default">
                    <?= __d('beta', 'Create or edit') ?>
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
                <h2><?= __d('beta', 'Attach a tag to a category') ?></h2>
            </div>
        </md-toolbar>
        
        <div class="section md-whiteframe-1dp">
            <?php
                echo $this->Form->create(
                    'AttachTag', 
                    [
                        'url' => [
                            'controller' => 'categories_tree',
                            'action' => 'attachTagToCategory'
                        ],
                    ]
                );
            ?>

            <md-input-container layout="column">
                <?php
                    echo $this->Form->input(
                        'tagName',
                        [
                            'id' => 'tagName',
                            'label' => __d('beta', 'Tag')
                        ]
                    );
                ?>
                <div id="autocompletionTag"></div>
            </md-input-container>

            <md-input-container layout="column">
                <?php
                    echo $this->Form->input(
                        'categoryName', 
                        [
                            'id' => 'categoryName',
                            'label' => __d('beta', 'Tags category')
                        ]
                    );
                ?>
                <div id="autocompletionCategory"></div>
            </md-input-container>

            <md-input-container layout="column">
                <md-button type="submit" class="md-raised md-default">
                    <?= __d('beta', 'Add') ?>
                </md-button>
            </md-input-container>
            <?php
                echo $this->Form->end();
            ?>
        </div>
    </section>

    <div ng-app="app" ng-controller="CategoriesTreeController as vm">
        <?php
            foreach ($tree as $category) {
                echo $this->element('categories_tree', [
                    'root' => -1,
                    'depth' => 0,
                    'category' => $category
                ]);
            }
        ?>
    </div>
</div>
