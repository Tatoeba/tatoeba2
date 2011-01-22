<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  BEN YAALA Salem <salem.benyaala@gmail.com>
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
 * @author   BEN YAALA Salem <salem.benyaala@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * profile view for Users.
 *
 * @category Users
 * @package  View
 * @author   BEN YAALA Salem <salem.benyaala@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

$userId = $user['id'];

$realName = Sanitize::html($user['name']);
$userName = Sanitize::html($user['username']);
$userDescription = Sanitize::html($user['description']);
$homepage = $user['homepage']; // no need to sanitize, used in $html->link()

$birthday = $user['birthday'];
$userSince = $user['since'];
$lastTimeActive = $user['last_time_active'];



$userImage = 'tatoeba_user.png';
if (!empty($user['image'])) {
    $userImage = Sanitize::html($user['image']);
}

if (!empty($realName)) {
    $this->pageTitle = "$realName ($userName) - Tatoeba";
} else {
    $this->pageTitle = "$userName - Tatoeba"; 
}
?>

<div id="annexe_content">

    <div id="pcontact" class="module">
        <h2><?php __('Contact information'); ?></h2>
        <dl>
            
            <dt><?php __('Private message'); ?></dt>
            <dd>
                <?php
                echo $html->link(
                    sprintf(__('Contact %s', true), $userName),
                    array(
                        'controller' => 'private_messages',
                        'action' => 'write',
                        $userName
                    )
                );
                ?>
            </dd>

            <dt><?php __('Others'); ?></dt>
            <dd>
                <?php
                echo $html->link(
                    sprintf(__("See this user's contributions", true)),
                    array(
                        'controller' => 'users',
                        'action' => 'show',
                        $userId
                    )
                );
                ?>
            </dd>

            <?php
            if (!empty($homepage)) {
                ?>
                <dt><?php __('Homepage'); ?></dt>
                <dd><?php echo $html->link($homepage); ?></dd>
            <?php
            }
            ?>
        </dl>
    </div>

    <div class="module">
        <h2><?php __('Activity information'); ?></h2>
        <dl>
            <dt><?php __('Member since'); ?></dt>
            <dd><?php echo date('F j, Y', strtotime($userSince)); ?></dd>
            <dt><?php __('Status'); ?></dt>
            <dd><?php echo $userStatus; ?></dd>
            <dt><?php __('Last login'); ?></dt>
            <dd><?php echo date('F j, Y \\a\\t G:i', $lastTimeActive); ?></dd>
            <dt><?php __('Comments posted'); ?></dt>
            <dd><?php echo $userStats['numberOfComments']; ?></dd>
            <dt><?php __('Sentences owned'); ?></dt>
            <dd><?php echo $userStats['numberOfSentences']; ?></dd>
            <dt><?php __('Sentences favorited'); ?></dt>
            <dd><?php echo $userStats['numberOfFavorites']; ?></dd>
        </dl>
    </div>
</div>

<!-- Main Content -->

<div id="main_content">
    <div class="module profile_master_content">
        <h2>
            <?php
            if (!empty($realName)) {
                echo $realName . ' aka. ' . $userName;
            } else {
                echo $userName;
            }
            ?>    
        </h2>
        <!-- self image -->
        <div id="pimg">
            <?php
            echo $html->image(
                'profiles_128/'.$userImage,
                array(
                    'alt' => $userName
                )
            );
            ?>
        </div>
    </div>
    
    <!-- self description  -->
    
    <?php
    if (!empty($userDescription)) {
        ?>
        <div id="pdescription" class="module">
            <h2><?php __('Something about you'); ?></h2>
            <div id="profile_description">
                <?php 
                $userDescription = $clickableLinks->clickableURL($userDescription);
                echo nl2br($userDescription); 
                ?>
            </div>
        </div>
    <?php
    }
    ?>

    <!-- self basic info -->
    <div id="pbasic" class="module">
        <h2><?php __('Basic Information'); ?></h2>
        <dl>
            <?php
            if (!empty($realName)) {
                ?>
                <dt><?php __('Name'); ?></dt>
                <dd><?php echo $realName; ?></dd>
            <?php
            }


            // TODO change this, no birthday should be stored as null value
            if ($birthday !== "0000-00-00 00:00:00") {
                $birthdayDate = new DateTime ($birthday);
                ?>
                <dt><?php __('Birthday'); ?></dt>
                <dd><?php echo $birthdayDate->format('F j, Y'); ?></dd>
            <?php
            }

            if (!empty($userCountry['name'])) {
                ?>
                <dt><?php __('Country'); ?></dt>
                <dd><?php echo $userCountry['name']; ?></dd>
            <?php
            }
            ?>
        </dl>
    </div>
</div>
