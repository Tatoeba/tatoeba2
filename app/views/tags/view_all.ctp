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

$this->set('title_for_layout', $pages->formatTitle(__('All existing tags', true)));
?>

<div id="annexe_content">
    <div class="module">
        <?php
        echo $html->tag('h2', __('Search tags', true));
        echo $form->create(array('action' => 'search'));
        echo $form->input(
            'search',
            array(
                'value' => $filter,
                'label' => false
            )
        );
        echo $form->submit(__('Search', true));
        echo $form->end();

        echo '<p>';
        echo $html->link(
            __('Show all tags', true),
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
            $title = $paginator->counter(
                array('format' => __('All tags (total %count%)', true))
            );
        } else {
            $title = $paginator->counter(
                array(
                    'format' => format(
                        __('Tags containing: {search} (total %count%)', true),
                        array('search' => $filter)
                    )
                )
            );
        }
        echo $html->tag('h2', $title, array('escape' => true));
        ?>
        
        <div class="sortBy">
            <strong><?php __("Sort by:") ?> </strong>
            <?php 
            echo $this->Paginator->sort(__("count",true), 'nbrOfSentences');
            echo " | ";
            echo $this->Paginator->sort(__("name",true), 'name');
            ?>
        </div>
        
        <?php 
            $pagination->display();
        ?>
        
        <div>
            <?php
            foreach( $allTags as $tag) {
                ?>
                <span class="tag">
                    <?php
                    $tagName =  $tag['Tag']['name'];
                    $tagId =  $tag['Tag']['id'];
                    $count = $tag['Tag']['nbrOfSentences'];
                    $tags->displayTagInCloud($tagName, $tagId, $count);
                    ?>
                </span>
            <?php
            }
            ?>
        </div>
        
        <div>
        <?php 
            $pagination->display();
        ?>
        </div>
    </div>
</div>
