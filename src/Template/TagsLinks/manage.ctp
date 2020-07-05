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
$this->Html->script('tagslinks.js', ['block' => 'scriptBottom']);
$this->Html->script('tagslinksautocompletion.js', ['block' => 'scriptBottom']);
echo $this->Html->css('tagslinks/manage.css');

function displayTree($tree, $tags, $before, $depth, $t) {
    echo "<ol ui-tree-nodes>";
    foreach ($tree as $parent => $children) {
        echo "<li ui-tree-node>";
        echo "<div ui-tree-handle>";
        echo "<span class='tag'>".$tags[$parent][0]."</span>";
        if (!count($children) && $depth > 0)
        echo $t->Html->link(
            'X',
            array(
                "controller" => "tagsLinks",
                "action" => "remove",
                $before,
                $parent
            ),
            array(
                "class" => "removeTagFromSentenceButton",
                "id" => 'deleteButton'.$before.$parent,
                "title" => "remove",
                "escape" => false
            ),
            null
        );
        echo "</div>";
        foreach ($children as $head => $sub){
            $subtree = [];
            $subtree[$head] = $sub;
            displayTree($subtree, $tags, $parent, $depth+1, $t);
        }
        echo "</li>";
    }
    echo "</ol>";
}

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
            <div id="autocompletionParent" class="autocompletionDiv"></div>
        </md-input-container>
        <md-input-container layout="column">
            <?php
                echo $this->Form->input('childTag');
            ?>
            <div id="autocompletionChild" class="autocompletionDiv"></div>
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

    <section class="md-whiteframe-1dp">
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2><?= __('Tags in tree') ?></h2>
            </div>
        </md-toolbar>

        <div layout-padding>
            <div ui-tree>
            <?php displayTree($tree, $all_tags, -1, 0, $this); ?>
            </div>
        </div>
    </section>

    <!-- <script type="text/ng-template" id="nodes_renderer.html">
        <div ui-tree-handle>
            {{node.title}}
        </div>
        <ol ui-tree-nodes="" ng-model="node.nodes">
            <li ng-repeat="node in node.nodes" ui-tree-node ng-include="'nodes_renderer.html'">
            </li>
        </ol>
    </script>
    <div ui-tree>
    <ol ui-tree-nodes="" ng-model="nodes" id="tree-root">
        <li ng-repeat="node in nodes" ui-tree-node ng-include="'nodes_renderer.html'"></li>
    </ol>
    </div> -->
</div>