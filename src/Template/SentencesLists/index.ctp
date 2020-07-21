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

$total = $this->Paginator->counter('{{count}}');
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

    if ($this->request->getSession()->read('Auth.User.id')) {
        $this->Lists->displayCreateListForm();
    }
    ?>
</div>

<div id="main_content">
    <section class="md-whiteframe-1dp">
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2 flex>
                    <?= $this->safeForAngular($title) ?>
                </h2>
                <md-menu md-offset="5 50" md-position-mode="target-right target">
                    <md-button ng-click="$mdOpenMenu($event)">
                        <md-icon>sort</md-icon> Sort by
                    </md-button>
                    <md-menu-content>

                        <?php echo $this->element('list_sort_button', array(
                                'param' => 'name',
                                'direction' => 'asc',
                                'label' => __('list name')
                        ));?>

                        <?php echo $this->element('list_sort_button', array(
                                'param' => 'name',
                                'direction' => 'desc',
                                'label' => __('list name')
                        ));?>

                        <?php echo $this->element('list_sort_button', array(
                                'param' => 'created',
                                'direction' => 'asc',
                                'label' => __('date created')
                        ));?>

                        <?php echo $this->element('list_sort_button', array(
                                'param' => 'created',
                                'direction' => 'desc',
                                'label' => __('date created')
                        ));?>
                        
                        <?php echo $this->element('list_sort_button', array(
                                'param' => 'numberOfSentences',
                                'direction' => 'asc',
                                'label' => __('number of sentences')
                        ));?>

                        <?php echo $this->element('list_sort_button', array(
                                'param' => 'numberOfSentences',
                                'direction' => 'desc',
                                'label' => __('number of sentences')
                        ));?>

                        <?php echo $this->element('list_sort_button_defaults', array(
                                'param' => 'modified',
                                'direction' => 'desc',
                                'label' => __('last updated')
                        ));?>

                        <?php echo $this->element('list_sort_button_defaults', array(
                                'param' => 'modified',
                                'direction' => 'asc',
                                'label' => __('last updated')
                        ));?>
                        
                    </md-menu-content>
                </md-menu>

            </div>
        </md-toolbar>

        <div layout-padding>
        <?php

        $this->Pagination->display();
        $this->Lists->displayListTable($allLists);
        $this->Pagination->display();
        ?>
        </div>
    </section>
</div>
