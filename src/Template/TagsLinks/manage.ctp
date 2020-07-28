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
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

$this->set('title_for_layout', $this->Pages->formatTitle(__('Structure tags')));
echo $this->Html->css('autocompletion.css');
$this->AssetCompress->script('sentences-block-for-members.js', ['block' => 'scriptBottom']);
$this->Html->script('autocompletion.js', ['block' => 'scriptBottom']);
$this->Html->script('tagslinks.js', ['block' => 'scriptBottom']);

?>

<div id="main_content">
    <section class="md-whiteframe-1dp">
    <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2><?php echo __('Add new link'); ?></h2>
            </div>
        </md-toolbar>
        
        <?php
            echo $this->Form->create('TagsLinks', [
                'url' => array('action' => 'add'),
            ]);
        ?>

        <md-input-container layout="column">
            <?php
                echo $this->Form->input('parentTag');
            ?>
            <div id="autocompletionParent"></div>
        </md-input-container>
        <md-input-container layout="column">
            <?php
                echo $this->Form->input('childTag');
            ?>
            <div id="autocompletionChild"></div>
        </md-input-container>
        <md-input-container layout="column">
            <md-button type="submit" class="md-raised md-default">
                <?= __('LINK') ?>
            </md-button>
        </md-input-container>
        <?php
            echo $this->Form->end();
        ?>
    </section>

    <div ng-app="app" ng-controller="TagsLinksController as vm">
    <?php
    foreach ($tree as $parent => $children) {
        echo $this->element('tagslink', [
            'root' => 0,
            'parent' => $parent,
            'children' => $children,
            'labels' => $all_tags,
            'depth' => 0
        ]);
    }
    ?>
    </div>
</div>