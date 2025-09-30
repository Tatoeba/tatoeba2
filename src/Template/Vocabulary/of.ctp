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
$this->Html->script('/js/vocabulary/of.ctrl.js', ['block' => 'scriptBottom']);
$this->Html->script('/js/services/vocabulary.srv.js', ['block' => 'scriptBottom']);

$count = $this->Paginator->param('count');
$title = format(
    __("{username}'s vocabulary items ({number})", $count),
    array('username' => $username, 'number' => $this->Number->format($count))
);

$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>

<div ng-cloak id="annexe_content">
    <?php echo $this->element('vocabulary/menu'); ?>

    <?php $this->CommonModules->createFilterByLangMod(2); ?>
</div>

<div id="main_content" ng-controller="VocabularyOfController as ctrl">
    <section class="md-whiteframe-1dp">
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2><?= $title ?></h2>
            </div>
        </md-toolbar>
    
        <md-content>
        <?php
        $this->Pagination->display();
        ?>

        <md-list flex>
            <?php
            foreach($vocabulary as $item) {
                $item = $item->vocabulary;
                $divId = $item->id;
                ?>
                <md-list-item id="vocabulary_<?= $divId ?>">
                    <?= $this->Vocabulary->vocabulary($item); ?>
                    <?php if ($canDelete || $item->canEdit) { ?>
                        <md-button ng-cloak ng-click="ctrl.edit(<?= h(json_encode($item)) ?>, <?= json_encode($item->canEdit) ?>)"
                                   class="md-icon-button">
                            <md-icon aria-label="Edit">edit</md-icon>
                        </md-button>
                    <?php } ?>
                </md-list-item>
                <?php
            }
            ?>
        </md-list>

        <?php
        $this->Pagination->display();
        ?>
        </md-content>
    </div>

</div>
