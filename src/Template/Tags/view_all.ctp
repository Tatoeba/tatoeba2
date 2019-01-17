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
 * @link     http://tatoeba.org
 */

$this->set('title_for_layout', $this->Pages->formatTitle(__('All existing tags')));
?>

<div id="annexe_content">
    <div class="module">
        <?php
        echo $this->Html->tag('h2', __('Search tags'));
        echo $this->Form->create('Tags', [
            'url' => array('action' => 'search'),
        ]);
        echo $this->Form->input(
            'search',
            array(
                'value' => $filter,
                'label' => false
            )
        );
        echo $this->Form->submit(__('Search'));
        echo $this->Form->end();

        echo '<p>';
        echo $this->Html->link(
            __('Show all tags'),
            array(
                'controller' => 'tags',
                'action' => 'view_all'
            )
        );
        echo '</p>';
        ?>
    </div>
</div>



<div id="main_content">
    <div class="module">
        <?php
        if (empty($filter)) {
            $title = $this->Paginator->counter(
                array('format' => __('All tags (total {{count}})'))
            );
        } else {
            $n = $this->Paginator->param('count');
            $title = format(
                __('Tags containing: {search} (total {count})'),
                array('search' => $filter, 'count' => $n)
            );
        }
        echo $this->Html->tag('h2', $title, array('escape' => true));
        ?>

        <div class="sortBy">
            <strong><?php echo __("Sort by:") ?> </strong>
            <?php
            echo $this->Paginator->sort('nbrOfSentences', __("count"));
            echo " | ";
            echo $this->Paginator->sort('name', __("name"));
            ?>
        </div>

        <?php
            $this->Pagination->display();
        ?>

        <div>
            <?php
            foreach( $allTags as $tag) {
                ?>
                <span class="tag">
                    <?php
                    $tagName = $tag->name;
                    $tagId = $tag->id;
                    $count = $tag->nbrOfSentences;
                    $this->Tags->displayTagInCloud($tagName, $tagId, $count);
                    ?>
                </span>
            <?php
            }
            ?>
        </div>

        <div>
        <?php
            $this->Pagination->display();
        ?>
        </div>
    </div>
</div>
