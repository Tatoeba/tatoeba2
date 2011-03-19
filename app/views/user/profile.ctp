<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  BEN YAALA Salem <salem.benyaala@gmail.com>
 * Copyright (C) 2011  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
$username = Sanitize::html($user['username']);
$userDescription = Sanitize::html($user['description']);
$homepage = $user['homepage'];
$birthday = $user['birthday'];
$userSince = $user['since'];
$statusClass = 'status'.$groupId;
$currentMember = CurrentUser::get('username');

$userImage = 'unknown-avatar.png';
if (!empty($user['image'])) {
    $userImage = Sanitize::html($user['image']);
}

if (!empty($realName)) {
    $this->pageTitle = "$username ($realName) - Tatoeba";
} else {
    $this->pageTitle = "$username - Tatoeba"; 
}
?>

<div id="annexe_content">
    <?php
        echo $this->element(
        'users_menu', 
        array('username' => $username)
    );
    ?>

    <div class="module">
        <h2><?php __('Stats'); ?></h2>
        <dl>
            <dt><?php __('Comments posted'); ?></dt>
            <dd><?php echo $userStats['numberOfComments']; ?></dd>
            <dt><?php __('Sentences owned'); ?></dt>
            <dd><?php echo $userStats['numberOfSentences']; ?></dd>
            <dt><?php __('Sentences favorited'); ?></dt>
            <dd><?php echo $userStats['numberOfFavorites']; ?></dd>
            <dt><?php __('Contributions'); ?></dt>
            <dd><?php echo $userStats['numberOfContributions']; ?></dd>
        </dl>
        
        <div>
        =>
        <?php
        echo $html->link(
            sprintf(__("Show latest activity", true)),
            array(
                'controller' => 'users',
                'action' => 'show',
                $userId
            )
        );
        ?>
        </div>
    </div>
    
    <?php
    if ($isDisplayed) {
        ?>
        <div class="module">
            <h2><?php __('Settings'); ?></h2>
            <ul class="annexeMenu">
                <li class="item">
                    <?php
                    if ($notificationsEnabled) {
                        __('Email notifications are ENABLED.');
                    } else {
                        __('Email notifications are DISABLED.');
                    }
                    ?>
                </li>
                
                <li class="item">
                    <?php    
                    if ($isPublic) {
                        __(
                            'Access to this profile is PUBLIC. '.
                            'All the information can be seen by everyone.'
                        );
                    } else {
                        __(
                            'Access to this profile is RESTRICTED. '.
                            'Only Tatoeba members can see the personal information '.
                            'and the description.'
                        );
                    }
                    ?>
                </li>
            </ul>
            <p>
            <?php
            if ($username == $currentMember) {
                $members->displayEditButton(
                    array(
                        'controller' => 'user',
                        'action' => 'settings'
                    )
                ); 
            }
            ?>
            </p>
        </div>
    <?php
    }
    ?>
    
</div>

<div id="main_content">
    <div class="module profileSummary">
        <?php 
        if ($username == $currentMember) {
            $members->displayEditButton(
                array(
                    'controller' => 'user',
                    'action' => 'edit_profile'
                )
            ); 
        }
        ?>
        
        <?php
        echo $html->image(
            IMG_PATH . 'profiles_128/'.$userImage,
            array(
                'alt' => $username
            )
        );
        ?>
            
        <div class="info">
            <div class="username"><?php echo $username; ?></div>
            
            <?php
            if ($isDisplayed) {
                if (!empty($birthday)) {
                    $birthday = date('F j, Y', strtotime($birthday));
                }
                if (!empty($homepage)) {
                    $homepage = $clickableLinks->clickableURL($homepage);
                }
                $userSince = date('F j, Y', strtotime($userSince));
                $fields = array(
                    __('Name', true) => $realName,
                    __('Country', true) => $countryName,
                    __('Birthday', true) => $birthday,
                    __('Homepage', true) => $homepage
                );
                
                foreach ($fields as $fieldName => $value) {
                    ?>
                    <div>
                        <span class="field <?php echo $statusClass ?>">
                        <?php echo $fieldName; ?>
                        </span>
                        <span class="value">
                        <?php 
                        if (!empty($value)) {
                            echo $value; 
                        } else {
                            echo ' - ';
                        }
                        ?>
                        </span>
                    </div>
                    <?php
                }
            }
            ?>
            
            <div>
                <span class="field <?php echo $statusClass ?>">
                <?php echo __('Member since'); ?>
                </span>
                <span class="value"><?php echo $userSince; ?></span>
            </div>
        </div>
        
        <div class="status <?php echo $statusClass ?>">
        <?php echo $userStatus; ?>
        </div>

    </div>
        
    <?php
    if (!empty($userDescription)) {
        $descriptionContent = $clickableLinks->clickableURL($userDescription);
        $descriptionContent = nl2br($descriptionContent); 
    } else {
        $descriptionContent = '<div class="tip">';
        $descriptionContent.= __('No description.', true);
        $descriptionContent.= '<br/><br/>';
        if ($username == $currentMember) {
            $descriptionContent.= __(
                'TIP: We encourage you to indicate the languages you know.', true
            );
        } else {
            $descriptionContent.= __(
                'TIP: Encourage this user to indicate the languages he/she knows.',
                true
            );
        }
        $descriptionContent.= '</div>';
    }
    
    if ($isDisplayed) {
        ?>
        <div class="module profileDescription">
        <?php 
        if ($username == $currentMember) {
            $members->displayEditButton(
                array(
                    'controller' => 'user',
                    'action' => 'edit_profile',
                    '#' => 'description'
                )
            ); 
        }
        ?>
        
        <div class="content">
        <?php
        echo $descriptionContent;
        ?>
        </div>
        </div>
        <?php
    }
    ?>
</div>
