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

if ($userExists == false) {
    $title = format(
        __("There's no user called {username}", true),
        array('username' => $username));
} else if (empty($filter)) {
    $title = format(
        __("{username}'s lists ({total})", true),
        array('username' => $username, 'total' => $total)
    );
} else {
    $title = format(
        __("{username}'s lists containing \"{search}\" ({total})", true),
        array('username' => $username, 'search' => $filter, 'total' => $total)
    );
}

$this->set('title_for_layout', $pages->formatTitle($title));
?>

<div id="annexe_content">
    <?php
    $lists->displayListsLinks();

    $lists->displaySearchForm($filter, array('username' => $username));

    if ($session->read('Auth.User.id')) {
        $lists->displayCreateListForm();
    }
    ?>
</div>

<div id="main_content">
    <div class="module">
        <?php 
        if ($userExists == false) {
            $commonModules->displayNoSuchUser($username, $backLink);
        } else {    
            echo $html->tag('h2', $title, array('escape' => true)); ?>

            <div class="sortBy">
                <strong><?php __("Sort by:") ?> </strong>
                <?php
                echo $this->Paginator->sort(__('list name', true), 'name');
                echo " | ";
                echo $this->Paginator->sort(__('date created', true), 'created');
                echo " | ";
                echo $this->Paginator->sort(
                    __('number of sentences', true),
                    'numberOfSentences'
                );
                ?>
            </div>
            
            <?php
            $pagination->display(array($username, $filter));

            $lists->displayListTable($userLists);

            $pagination->display(array($username, $filter));
        }
        ?>
    </div>
</div>
