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

 $total = $this->Paginator->counter("%count%");
if (empty($filter)) {
    if (!empty($isCollaborative)) {
        $title = __('Collaborative lists ({total})');
    } else {
        $title = __('All public lists ({total})');
    }
    $title = format($title, array('total' => $total));
} else {
    if (!empty($isCollaborative)) {
        $title = __('Collaborative lists containing "{search}" ({total})');
    } else {
        $title = __('All public lists containing "{search}" ({total})');
    }
    $title = format($title, array('total' => $total, 'search' => $filter));
}

$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>

<div id="annexe_content">
    <?php
    $this->Lists->displayListsLinks();

    $this->Lists->displaySearchForm($filter);

    if ($this->request->session()->read('Auth.User.id')) {
        $this->Lists->displayCreateListForm();
    }
    ?>
</div>

<div id="main_content">
    <div class="module">
        <?php echo $this->Html->tag('h2', $title, array('escape' => true)); ?>

        <div class="sortBy">
            <strong><?php echo __("Sort by:") ?> </strong>
            <?php
            echo $this->Paginator->sort('name', __('list name'));
            echo " | ";
            echo $this->Paginator->sort('created', __('date created'));
            echo " | ";
            echo $this->Paginator->sort(
              'numberOfSentences',
                __('number of sentences')
            );
            echo " | ";
            $options = array('defaultOrders' => array('modified' => 'desc'));
            echo $this->Pagination->sortDefaultOrder(__('last updated'), 'modified', $options);
            ?>
        </div>
        <?php

        $this->Pagination->display();
        $this->Lists->displayListTable($allLists);
        $this->Pagination->display();
        ?>
    </div>
</div>
