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
 * @link     https://tatoeba.org
 */
use App\Model\CurrentUser;

/**
 * profile view for Users.
 *
 * @category Users
 * @package  View
 * @author   BEN YAALA Salem <salem.benyaala@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

$dateFormat = [\IntlDateFormatter::LONG, \IntlDateFormatter::NONE];
$userId = $user['id'];
$realName = $user['name'];
$username = $user['username'];
$userDescription = h($user['description']);
$homepage = $user['homepage'];
$birthday = $user['birthday'];
$userSince = $this->Time->i18nFormat($user['since'], $dateFormat);
$userStatus = $this->Members->groupName($user->role);
$statusClass = 'status_'.$user->role;
$currentMember = CurrentUser::get('username');
$languagesSettings = $user['settings']['lang'];
$level = $user['level'];
$countryName = $this->Countries->getCountryNameByCode($user['country_id']);

$userImage = 'unknown-avatar.png';
if (!empty($user['image'])) {
    $userImage = h($user['image']);
}

$title = empty($realName) ? $username : "$username ($realName)";
$this->set('title_for_layout', h($this->Pages->formatTitle($title)));
?>

<div id="annexe_content">
    <?php
        echo $this->element(
        'users_menu',
        array('username' => $username)
    );
    ?>

    <div class="section md-whiteframe-1dp">
        <h2><?php echo __('Stats'); ?></h2>
        <dl>
            <dt><?php echo __('Comments posted'); ?></dt>
            <dd><?php echo $this->Number->format($userStats['numberOfComments']); ?></dd>
            <dt><?php echo __('Sentences owned'); ?></dt>
            <dd><?php echo $this->Number->format($userStats['numberOfSentences']); ?></dd>
            <dt><?php echo __('Audio recordings'); ?></dt>
            <dd><?php echo $this->Number->format($userStats['numberOfAudios']); ?></dd>
            <dt><?php echo __('Sentences favorited'); ?></dt>
            <dd><?php echo $this->Number->format($userStats['numberOfFavorites']); ?></dd>
            <dt><?php echo __('Contributions'); ?></dt>
            <dd><?php echo $this->Number->format($userStats['numberOfContributions']); ?></dd>
        </dl>

        <div>
        <?php
        echo $this->Html->link(
            __("Show latest activity"),
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
        <div class="section md-whiteframe-1dp">
            <h2><?php echo __('Settings'); ?></h2>

            <ul class="annexeMenu">
                <li class="item">
                    <?php
                    if ($notificationsEnabled) {
                        echo __('Email notifications are ENABLED.');
                    } else {
                        echo __('Email notifications are DISABLED.');
                    }
                    ?>
                </li>

                <li class="item">
                    <?php
                    if ($isPublic) {
                        echo __(
                            'Access to this profile is PUBLIC. '.
                            'All the information can be seen by everyone.'
                        );
                    } else {
                        echo __(
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

            <?php
            if ($username == $currentMember) {
                $editSettingsUrl = $this->Url->build(array(
                    'controller' => 'user',
                    'action' => 'settings'
                ));
                ?>
                <div ng-cloak layout="row" layout-align="end center">
                    <md-button class="md-primary md-raised"
                               aria-label="<?= __('Edit') ?>"
                               href="<?= $editSettingsUrl ?>">
                        <?= __('Edit') ?>
                    </md-button>
                </div>
            <?php
            }
            ?>
        </div>
    <?php
    }
    ?>
</div>

<div id="main_content">
    <div ng-cloak id="profile" class="section with-title-button md-whiteframe-1dp" layout="column">

        <div layout="row" class="header">
            <div>
                <?php
                echo $this->Html->image(
                    IMG_PATH . 'profiles_128/'.$userImage,
                    array(
                        'width' => 128,
                        'height' => 128,
                        'alt' => $username
                    )
                );
                ?>
            </div>

            <div layout="column" class="info" flex>
                <div layout="row" layout-align="space-between center">
                    <h2 flex><?= $username ?></h2>
                    <?php
                    $editUrl = null;
                    if ($username == $currentMember) {
                        $editUrl = $this->Url->build(
                            array(
                                'controller' => 'user',
                                'action' => 'edit_profile'
                            )
                        );
                    } else if (CurrentUser::isAdmin()) {
                        $editUrl = $this->Url->build(
                            array(
                                'controller' => 'users',
                                'action' => 'edit',
                                $userId
                            )
                        );
                    }
                    if (!empty($editUrl)){
                        ?>
                        <md-button class="md-primary md-raised"
                                   aria-label="<?= __('Edit') ?>"
                                   href="<?= $editUrl ?>">
                            <?= __('Edit') ?>
                        </md-button>
                        <?php
                    }
                    ?>
                </div>


                <div layout="column" flex layout-margin>
                    <div layout="row">
                        <div class="label"><?= __('Member since') ?></div>
                        <div flex><?= $userSince ?></div>
                    </div>

                    <?php
                    $cssClasses = array('status', $statusClass);
                    $options = [];
                    if ($level == -1) {
                        $cssClasses[] = 'contributionsBlocked';
                        $options = array('title' => __('Contributions blocked'));
                    }
                    echo $this->Html->div(
                        join($cssClasses, ' '),
                        $userStatus,
                        $options
                    );
                    ?>
                </div>


            </div>
        </div>

        <?php
        if ($isDisplayed) {
            // For consistency, this format should match the first part of the format
            // under app/views/helpers/date.php.

            if (!empty($birthday)) {
                $birthday = $this->Date->formatBirthday($birthday, $dateFormat);
            }
            if (!empty($homepage)) {
                $homepage = $this->ClickableLinks->clickableURL(h($homepage));
            }

            $personalInfo = array(
                __x('user', 'Name') => $realName,
                __('Country') => $countryName,
                __('Birthday') => $birthday,
                __('Homepage') => $homepage
            );
            ?>
            <md-divider></md-divider>

            <div class="personal-info" layout-margin>
                <?php foreach ($personalInfo as $label => $value) { ?>
                    <div layout="row">
                        <div flex="33" class="label"><?= $label ?></div>
                        <div flex><?= $value ? $value : '-' ?></div>
                    </div>
                <?php } ?>
            </div>
            <?php
        }
        ?>

        <?php
        if (!empty($userDescription)) {
            $descriptionContent = $this->ClickableLinks->clickableURL($userDescription);
            $descriptionContent = nl2br($descriptionContent);
        } else {
            $descriptionContent = '<div class="tip">';
            $descriptionContent.= __('No description.');
            $descriptionContent.= '</div>';
        }
        ?>

        <?php
        if ($isDisplayed) {
            ?>
            <md-divider></md-divider>
            <?php
            echo $this->Languages->tagWithLang(
                'div', '', $descriptionContent,
                array(
                    'class' => 'profileDescription',
                    'escape' => false
                )
            );
        }
        ?>
    </div>

    <div ng-cloak class="section with-title-button md-whiteframe-1dp">
        <div layout="row" layout-align="start center">
            <h2 flex><?= __('Languages'); ?></h2>
            <?php
            if ($username == $currentMember) {
                $addLangUrl = $this->Url->build(array(
                    'controller' => 'user',
                    'action' => 'language'
                ));
                ?>
                <div layout="row" layout-align="end center">
                    <md-button aria-label="<?= __('Add a language') ?>" class="md-primary md-raised" href="<?= $addLangUrl ?>">
                        <?= __('Add a language') ?>
                    </md-button>
                </div>
            <?php } ?>
        </div>

        <?php
        if (empty($userLanguages))
        {
            echo '<p>';
            __('No language added.');
            echo '</p>';

            echo '<p>';
            if ($username == $currentMember) {
                __('TIP: We encourage you to indicate the languages you know.');
            } else {
                __(
                    'TIP: Encourage this user to indicate the languages '.
                    'he or she knows.'
                );
            }
            echo '</p>';
        }
        else
        {
            ?>
            <md-list>
                <?php foreach($userLanguages as $languageInfo) {
                    $langCode = $languageInfo->language_code;
                    $level = $languageInfo->level;
                    $details = $languageInfo->details;
                    $editLangUrl = $this->Url->build(array(
                        'controller' => 'user',
                        'action' => 'language',
                        $langCode
                    ));
                    ?>
                    <md-list-item class="md-2-line">
                        <?php
                        // Icon
                        echo $this->Languages->icon(
                            $langCode,
                            array(
                                'class' => 'language-icon'
                            )
                        );
                        ?>
                        <div class="md-list-item-text">
                            <h3 flex>
                                <?= $this->Languages->codeToNameAlone($langCode) ?>
                            </h3>
                            <?= $this->Members->displayLanguageLevel($level); ?>
                            <p>
                                <?= $details ?>
                            </p>
                        </div>
                        <?php if ($username == $currentMember) {
                            $deleteUrl = $this->Url->build(
                                array(
                                    'controller' => 'users_languages',
                                    'action' => 'delete',
                                    $languageInfo->id
                                )
                            );
                            $confirmation = __('Are you sure?');
                            ?>
                            <md-button class="md-secondary md-icon-button"
                                       aria-label="<?= __('Edit') ?>"
                                       href="<?= $editLangUrl ?>">
                                <md-icon aria-label="<?= __('Edit') ?>">
                                    edit
                                </md-icon>
                            </md-button>

                            <md-button type="submit" class="md-secondary md-icon-button"
                                       href="<?= $deleteUrl; ?>"
                                       onclick="return confirm('<?= $confirmation; ?>');">
                                <md-icon aria-label="<?= __('Delete') ?>">
                                    delete
                                </md-icon>
                            </md-button>
                        <?php } ?>
                    </md-list-item>
                <?php } ?>
            </md-list>
            <?php
        }
        ?>
    </div>
</div>
