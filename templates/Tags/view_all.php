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

$this->set('title_for_layout', $this->Pages->formatTitle(__('All existing tags')));
$tagsIndexUrl = $this->Url->build([
    'controller' => 'tags',
    'action' => 'view_all'
]);
?>

<div id="annexe_content">
    <div class="section md-whiteframe-1dp" layout="column">
        <?php
        echo $this->Html->tag('h2', __('Search tags'));
        echo $this->Form->create(null, [
            'url' => array('action' => 'search'),
        ]);
        ?>
        <md-input-container layout="column">
            <?php
                echo $this->Form->control('search', [
                    'value' => $this->safeForAngular($filter),
                    'label' => false,
                ]);
            ?>
            <md-button type="submit" class="md-raised md-default">
                <?php /* @translators: search button in All tags page (verb) */ ?>
                <?= __x('button', 'Search') ?>
            </md-button>
        </md-input-container>

        <?php if ($filter) { ?>
            <md-button class="md-primary" href="<?= $tagsIndexUrl ?>">
                <?= __('Show all tags') ?>
            </md-button>
        <?php } ?>
        <?php
        echo $this->Form->end();
        ?>
    </div>
</div>



<div id="main_content">
    <section class="md-whiteframe-1dp">
        <?php
        if (empty($filter)) {
            $title = $this->Paginator->counter(
                array('format' => __('All tags (total {{count}})'))
            );
        } else {
            $title = format(
                $this->Paginator->counter(__('Tags containing: {search} (total {{count}})')),
                array('search' => $filter)
            );
        }
        ?>

        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2><?= $this->safeForAngular($title) ?></h2>
                
                <?php 
                    $options = array(
                        /* @translators: sort option in the All tags page */
                        array('param' => 'nbrOfSentences', 'direction' => 'desc', 'label' => __('Highest number of sentences')),
                        /* @translators: sort option in the All tags page */
                        array('param' => 'nbrOfSentences', 'direction' => 'asc', 'label' => __('Lowest number of sentences') ),
                        /* @translators: sort option in the All tags page */
                        array('param' => 'name', 'direction' => 'asc', 'label' => __('Tag name (alphabetical)')),
                        /* @translators: sort option in the All tags page */
                        array('param' => 'name', 'direction' => 'desc', 'label' => __('Tag name (reverse alphabetical)'))
                    );
                    echo $this->element('sort_menu', array('options' => $options));
                ?>
                
            </div>
        </md-toolbar>

        <div layout-padding>

        <?php $this->Pagination->display(); ?>

        <md-list>
            <?php foreach( $allTags as $tag) {
                $tagName = $this->safeForAngular($tag->name);
                $tagUrl = $this->Url->build([
                    'controller' => 'tags',
                    'action' => 'show_sentences_with_tag',
                    $tag->id
                ]);
                $n = $tag->nbrOfSentences;
                $tagCount = format(
                    __n('One sentence', '{n}&nbsp;sentences', $n),
                    ['n' => $this->Number->format($n)]
                )
                ?>
                <md-list-item href="<?= $tagUrl ?>" class="secondary-button-padding">
                    <p><span class="tag"><?= $tagName ?></span></p>
                    <span class="number-of-sentences"><?= $tagCount ?></span>
                </md-list-item>
            <?php } ?>
        </md-list>
        
        <?php $this->Pagination->display(); ?>
        
        </div>
    </section>
</div>
