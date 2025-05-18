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

/**
 * Page that lists all the members.
 *
 * @category Users
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

$this->set('title_for_layout', $this->Pages->formatTitle(__('Members')));
?>

<div id="annexe_content">
    <div class="section md-whiteframe-1dp">
    <h2><?php echo __('Search user') ?></h2>
    <?php
    $this->Security->enableCSRFProtection();
    echo $this->Form->create('User', ['url' => ['action' => 'search']]);
    ?>
    
    <md-input-container layout="column">
        <?php
        echo $this->Form->input('search_username',[
            'id' => 'usernameInput',
            'label' => '',
        ]);
        ?>
        <md-button type="submit" class="md-raised">
            <?php /* @translators: search button in All members page (verb) */ ?>
            <?= __x('button', 'Search') ?>
        </md-button>
    </md-input-container>

    <?php
    echo $this->Form->end();
    $this->Security->disableCSRFProtection();
    ?>
    </div>

    <?php
    echo $this->element(
        'currently_active_members',
        array(
            'currentContributors' => $currentContributors
        )
    );
    ?>
</div>

<div id="main_content">
    <section class="md-whiteframe-1dp">
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
            <h2>
            <?php
            echo $this->Paginator->counter(
                format(
                    __('Members (total {number})'),
                    ['number' => '{{count}}']
                )
            );
            ?>
            </h2>

            <?php 
                $options = array(
                    /* @translators: sort option in the All members page */
                    array('param' => 'username', 'direction' => 'asc', 'label' => __('Username (alphabetical)')),
                    /* @translators: sort option in the All members page */
                    array('param' => 'username', 'direction' => 'desc', 'label' => __('Username (reverse alphabetical)')),
                    /* @translators: sort option in the All members page */
                    array('param' => 'since', 'direction' => 'desc', 'label' => __x('members', 'Newest first')),
                    /* @translators: sort option in the All members page */
                    array('param' => 'since', 'direction' => 'asc', 'label' => __x('members', 'Oldest first')),
                    /* @translators: sort option in the All members page */
                    array('param' => 'role', 'direction' => 'asc', 'label' => __('Status (admin to contributor)')),
                    /* @translators: sort option in the All members page */
                    array('param' => 'role', 'direction' => 'desc', 'label' => __('Status (contributor to admin)') )
                );
                echo $this->element('sort_menu', array('options' => $options));
            ?>

            </div>
        </md-toolbar>
        
        <md-content>


        <?php $this->Pagination->display(); ?>

        <div class="users">
        <?php
        foreach ($users as $i=>$user):
        $role = $user->role;
        $status = "status_$role";
        ?>
        <div class="user <?php echo $status ?> md-whiteframe-1dp">
            <div class="image">
                <?php echo $this->Members->image($user); ?>
            </div>


            <div class="username">
                <?php
                echo $this->Html->link(
                    $user->username,
                    array(
                        "controller"=>'user',
                        "action"=>'profile',
                        $user->username
                    )
                );
                ?>
            </div>


            <div class="memberSince" title="<?php echo __("Member since"); ?>">
                <span class="date">
                <?php echo $this->Date->ago($user->since); ?>
                </span>
            </div>


            <div class="statusName">
            <?php
            echo $this->Members->groupName($role);
            ?>
            </div>
        </div>
        <?php endforeach; ?>
        </div>

        <?php
        $this->Pagination->display();
        ?>
        </md-content>
    </section>
</div>
