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

$this->pageTitle = 'Tatoeba - ' . __('Members', true);
?>

<div id="annexe_content">
    <div class="module">
    <h2><?php __('Search user') ?></h2>
    <?php
    echo $form->create('User', array("action" => "search"));
    echo $form->input(
        'username',
        array(
            "id" => "usernameInput",
            "label" => "", 
        )
    );
    echo $form->end(__('search', true));
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
    
    <div class="module">
    <h2><?php __('Global ranking'); ?></h2>
    <p>
    =&gt; 
    <?php
    echo $html->link(
        __('Show global users ranks',true),
        array(
            "controller" => "contributions",
            "action" => "statistics"
        )
    );
    ?>
    </p>
    </div>
</div>

<div id="main_content">
    <div class="module">
        <h2>
        <?php 
        echo $paginator->counter(
            array('format' => __('Members (total %count%)', true))
        ); 
        ?>
        </h2>
        
        <strong><?php __('Sort by:'); ?></strong>
        <?php
        echo $paginator->sort(__('Username', true), 'username');
        echo ' | ';
        echo $paginator->sort(__('Member since', true),'since');
        echo ' | ';
        echo $paginator->sort(__('Member status', true),'group_id');
        ?>
        
        
        <?php $pagination->display(); ?>
        
        <div class="users">
        <?php
        foreach ($users as $i=>$user):
        $groupId = $user['Group']['id'];
        $status = "status".$groupId;
        $username = $user['User']['username'];
        $userImage = null;
        if (isset($user['User']['image'])) {
            $userImage = $user['User']['image'];
        };
        ?>
        <div class="user <?php echo $status ?>">
            <div class="image">
                <?php echo $members->image($username, $userImage); ?>
            </div>

            
            <div class="username">
                <?php
                echo $html->link(
                    $user['User']['username'],
                    array(
                        "controller"=>'user', 
                        "action"=>'profile', 
                        $user['User']['username']
                    )
                ); 
                ?>
            </div>

            
            <div class="memberSince">
                <?php __("Member since:") ?>
                <span class="date">
                <?php echo $date->ago($user['User']['since']); ?>
                </span>
            </div>

            
            <div class="status">
                <div class="power">
                <?php
                for ($i = 4; $i > $groupId; $i--) {
                    echo $html->image('crown.png');
                }
                ?>
                </div>
                
                <div class="name">
                <?php 
                echo $user['Group']['name']; 
                ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
        
        <?php
        $pagination->display();
        ?>
    </div>
</div>


