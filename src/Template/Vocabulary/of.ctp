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
$this->Html->script('/js/vocabulary/of.ctrl.js', ['block' => 'scriptBottom']);

$count = $this->Paginator->param('count');
$title = format(
    __("{username}'s vocabulary items ({number})", $count),
    array('username' => $username, 'number' => $count)
);

$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>

<div ng-cloak id="annexe_content">
    <?php echo $this->element('vocabulary/menu'); ?>

    <?php $this->CommonModules->createFilterByLangMod(2); ?>
</div>

<div id="main_content" ng-controller="VocabularyOfController as ctrl">
    <div class="section" md-whiteframe="1">
        <h2><?= $title ?></h2>

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
                    <? if ($canEdit) { ?>
                        <md-button ng-click="ctrl.remove('<?= $divId ?>')"
                                   class="md-icon-button">
                            <md-icon aria-label="Remove">delete</md-icon>
                        </md-button>
                    <? } ?>
                </md-list-item>
                <?php
            }
            ?>
        </md-list>

        <?php
        $this->Pagination->display();
        ?>
    </div>

</div>
