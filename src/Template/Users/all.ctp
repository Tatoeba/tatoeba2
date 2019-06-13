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

/**
 * Page that lists all the members.
 *
 * @category Users
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

$this->set('title_for_layout', $this->Pages->formatTitle(__('Members')));
?>

<div id="annexe_content">
    <div class="module">
    <h2><?php echo __('Search user') ?></h2>
    <?php
    $this->Security->enableCSRFProtection();
    echo $this->Form->create('User', array(
        "url" => array("action" => "search")
    ));
    echo $this->Form->input(
        'username',
        array(
            "id" => "usernameInput",
            "label" => "",
        )
    );
    echo $this->Form->submit(__('search'));
    echo $this->Form->end();
    $this->Security->disableCSRFProtection();
    ?>
    </div>

    <div class="module">
    <?php
    echo $this->element(
        'currently_active_members',
        array(
            'currentContributors' => $currentContributors
        )
    );
    ?>
    </div>
</div>

<div id="main_content">
    <div class="module">
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

        <div class="sortBy">
            <strong><?php echo __('Sort by:'); ?></strong>
            <?php
            echo $this->Paginator->sort('username', __('Username'));
            echo ' | ';
            echo $this->Paginator->sort('since', __('Member since'));
            echo ' | ';
            echo $this->Paginator->sort('role', __('Member status'));
            ?>
        </div>


        <?php $this->Pagination->display(); ?>

        <div class="users">
        <?php
        foreach ($users as $i=>$user):
        $role = $user->role;
        $status = "status_$role";
        $username = $user->username;
        $userImage = null;
        if (isset($user->image)) {
            $userImage = $user->image;
        };
        ?>
        <div class="user <?php echo $status ?>">
            <div class="image">
                <?php echo $this->Members->image($username, $userImage); ?>
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
    </div>
</div>
