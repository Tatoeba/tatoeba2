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
    __("{username}'s lists ({total})", true),
    array('username' => $username, 'total' => $total)
);
$this->set('title_for_layout', $pages->formatTitle($title));
?>

<div id="annexe_content">
    <div class="module">
        <?php
        echo $html->tag('h2', __('Search lists', true));
        echo $form->create(array('action' => 'search'));
        echo $form->hidden('username', array('value' => $username));
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

        ?>
        <div class="module">
            <h2><?php __('Create a new list'); ?></h2>
            <?php
            echo $form->create(
                'SentencesList',
                array(
                    "action" => "add",
                    "type" => "post",
                )
            );
            echo $form->input(
                'name',
                array(
                    'type' => 'text',
                    'label' => __p('list', 'Name', true)
                )
            );
            echo $form->end(__('create', true));
            ?>
        </div>
    <?php
    }
    ?>
</div>

<div id="main_content">
    <div class="module">
        <h2><?php echo $title; ?></h2>
        <?php
        $pagination->display(array($username, $search));

        $lists->displayListTable($userLists);

        $pagination->display(array($username, $search));
        ?>
    </div>
</div>
