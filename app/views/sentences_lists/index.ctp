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

$total = $paginator->counter("%count%");
$title = format(
    __n('1 list of sentences', '{total} lists of sentences', $total, true),
    array('total' => $total)
);
$this->set('title_for_layout', $pages->formatTitle($title));
?>

<div id="annexe_content">
    <div class="module">
        <?php
        echo $html->tag('h2', __('Search lists', true));
        echo $form->create(array('action' => 'search'));
        echo $form->input(
            'search',
            array(
                'value' => $search,
                'label' => false
            )
        );
        echo $form->submit(__('Search', true));
        echo $form->end();

        echo '<p>';
        echo $html->link(
            __('Show all lists', true),
            array(
                'controller' => 'sentences_lists',
                'action' => 'index'
            )
        );
        echo '</p>';
        ?>
    </div>
    
    <?php
    if ($session->read('Auth.User.id')) {
        $lists->displayCreateListForm();
    }
    ?>
</div>

<div id="main_content">

<?php
if (count($allLists) > 0) {
    echo '<div class="module">';

    echo $html->tag('h2', $title);

    ?>
    <div class="sortBy">
        <strong><?php __("Sort by:") ?> </strong>
        <?php
        echo $this->Paginator->sort(__('name', true), 'name');
        echo " | ";
        echo $this->Paginator->sort(__('date', true), 'created');
        echo " | ";
        echo $this->Paginator->sort(__('number of sentences', true), 'numberOfSentences');
        ?>
    </div>
    <?php

    $pagination->display();
    $lists->displayListTable($allLists);
    $pagination->display();
    
    echo '</div>';
}
?>

</div>
