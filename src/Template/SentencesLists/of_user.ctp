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

if ($userExists) {
    $total = $this->Paginator->counter("%count%");
}

if (!$userExists) {
    $title = format(
        __("There's no user called {username}"),
        array('username' => $username));
} else if (empty($filter)) {
    $title = format(
        __("{username}'s lists ({total})"),
        array('username' => $username, 'total' => $total)
    );
} else {
    $title = format(
        __("{username}'s lists containing \"{search}\" ({total})"),
        array('username' => $username, 'search' => $filter, 'total' => $total)
    );
}

$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>

<div id="annexe_content">
    <?php
    $this->Lists->displayListsLinks();
    if($userExists) {
        $this->Lists->displaySearchForm($filter, array('username' => $username));
    }
    if ($this->request->session()->read('Auth.User.id')) {
        $this->Lists->displayCreateListForm();
    }
    ?>
</div>

<div id="main_content">
    <div class="module">
        <?php
        if (!$userExists) {
            $this->CommonModules->displayNoSuchUser($username);
        } else {
            echo $this->Html->tag('h2', $title, array('escape' => true)); ?>

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
            $this->Pagination->display(array($username, $filter));

            $this->Lists->displayListTable($userLists);

            $this->Pagination->display(array($username, $filter));
        }
        ?>
    </div>
</div>
