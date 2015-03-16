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

$realName = $user['name'];
$username = $user['username'];
$userDescription = Sanitize::html($user['description']);
$homepage = $user['homepage'];
$birthday = $user['birthday'];
$userSince = $user['since'];
$userStatus = $members->groupName($groupId);
$statusClass = 'status'.$groupId;
$currentMember = CurrentUser::get('username');
$languagesSettings = $user['lang'];
$level = $user['level'];

$userImage = 'unknown-avatar.png';
if (!empty($user['image'])) {
    $userImage = Sanitize::html($user['image']);
}

$title = empty($realName) ? $username : "$username ($realName)";
$this->set('title_for_layout', Sanitize::html($pages->formatTitle($title)));
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
            __("Show latest activity", true),
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
                
                <?php
                if (!empty($languagesSettings)) {
                    ?> 
                    <li class="item">
                    <?php echo str_replace(',', ', ', $languagesSettings); ?>
                    </li>
                    <?php
                }
                ?>
            </ul>
            
        </div>
    <?php
    }
    ?>
    
    <?php
    if ($level == -1) {
    ?>
        <div class="module">
            <h2><?php __('Not approved'); ?></h2>
            <?php
            __(
                'Sentences from this user are currently added as '.
                '"not approved".'
            );
            ?>
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
        } else if (CurrentUser::isAdmin()) {
            $members->displayEditButton(
                array(
                    'controller' => 'users',
                    'action' => 'edit',
                    $userId
                )
            ); 
        }
        ?>
        
        <?php
        echo $html->image(
            IMG_PATH . 'profiles_128/'.$userImage,
            array(
                'width' => 128,
                'height' => 128,
                'alt' => $username
            )
        );
        ?>
            
        <div class="info">
            <?php echo $html->tag('div', $username, array('class' => 'username')); ?>
            
            <?php
            if ($isDisplayed) {
                if (!empty($birthday)) {
                    $birthday = date('F j, Y', strtotime($birthday));
                }
                if (!empty($homepage)) {
                    $homepage = $clickableLinks->clickableURL(Sanitize::html($homepage));
                }
                $userSince = date('F j, Y', strtotime($userSince));
                $fields = array(
                    __p('user', 'Name', true) => array($realName, true),
                    __('Country', true)       => array($countryName, false),
                    __('Birthday', true)      => array($birthday, false),
                    __('Homepage', true)      => array($homepage, false),
                );
                
                foreach ($fields as $fieldName => $value) {
                    ?>
                    <div>
                        <span class="field <?php echo $statusClass ?>">
                        <?php echo $fieldName; ?>
                        </span>
                        <?php
                        $options = array('class' => 'value');
                        $dispValue = empty($value[0]) ? ' - ' : $value[0];
                        if ($value[1]) {
                            echo $languages->tagWithLang(
                                'span', '', $dispValue, $options
                            );
                        } else {
                            echo $html->tag('span', $dispValue, $options);
                        }
                        ?>
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

    <div class="module profileLanguages">
        <?php
        if ($username == $currentMember) {
            $members->displayEditButton(
                array(
                    'controller' => 'user',
                    'action' => 'language'
                ),
                __('Add a language', true)
            );
        }

        if (empty($userLanguages))
        {
            __('No language added.');
        }
        else
        {
            echo '<table>';
            foreach($userLanguages as $userLanguage) {
                $languageInfo = $userLanguage['UsersLanguages'];
                $langCode = $languageInfo['language_code'];
                $level = $languageInfo['level'];
                $details = $languageInfo['details'];

                echo '<tr class="languageInfo">';

                // Icon
                echo $html->tag('td', $languages->icon(
                    $langCode,
                    array(
                        "width" => 30,
                        "height" => 20
                    )
                ));

                // Name
                echo $html->tag('td', $languages->codeToNameAlone($langCode));

                // Level
                echo $html->tag('td', $members->displayLanguageLevel($level));

                // Details
                echo $html->tag('td', $details, array('escape' => true));

                // Edit link
                if ($username == $currentMember) {
                    $editLink = $html->link(
                        __('Edit', true),
                        array(
                            'controller' => 'user',
                            'action' => 'language',
                            $langCode
                        )
                    );
                    echo $html->tag('td', $editLink);
                }

                echo '</tr>';
            }
            echo '</table>';
        }
        ?>
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
                'TIP: Encourage this user to indicate the languages '.
                'he or she knows.', true
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
        
        <?php
        echo $languages->tagWithLang(
            'div', '', $descriptionContent,
            array('class' => 'content', 'escape' => false)
        );
        ?>
        </div>
        <?php
    }
    ?>
</div>
