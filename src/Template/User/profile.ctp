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

$this->Html->script('user/language.ctrl.js', ['block' => 'scriptBottom']);
$this->Html->script('/js/directives/sentence-and-translations.dir.js', array('block' => 'scriptBottom')); // TODO: this is just to get the languageIcon directive

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

<div id="annexe_content" ng-cloak>
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

<?php
$userLanguages = htmlspecialchars(json_encode($userLanguages), ENT_QUOTES, 'UTF-8');
?>
    <div class="section md-whiteframe-1dp"
         ng-cloak
         ng-controller="LanguageController as vm"
         ng-init="vm.init(<?= $userLanguages ?>)">
        <h2><?= __('Languages'); ?></h2>

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
                <?php
                    $editLangUrl = $this->Url->build([
                        'controller' => 'user',
                        'action' => 'language'
                    ]);
                    $deleteUrl = $this->Url->build([
                        'controller' => 'users_languages',
                        'action' => 'delete'
                    ]);
                    $confirmation = __('Are you sure?');
                    ?>
                    <md-list-item class="md-2-line" ng-repeat="lang in vm.langs">
                        <language-icon lang="lang.language_code" title="lang.name"></language-icon>
                        <div class="md-list-item-text">
                            <h3 flex>
                                {{lang.name}}
                            </h3>
                            <div class="languageLevel">
                                <?php $maxLanguageLevel = 5; ?>
                                <md-icon ng-repeat="n in [].constructor(lang.level) track by $index" class="md-primary">star</md-icon><md-icon ng-repeat="n in [].constructor(<?= $maxLanguageLevel ?>-lang.level) track by $index">star_border</md-icon>
                            </div>
                            <p>
                                {{lang.details}}
                            </p>
                        </div>
                        <?php if ($username == $currentMember) {
                            ?>
                            <md-button class="md-secondary md-icon-button"
                                       aria-label="<?= __('Edit') ?>"
                                       ng-href="<?= $editLangUrl.'/{{lang.language_code}}' ?>">
                                <md-icon aria-label="<?= __('Edit') ?>">
                                    edit
                                </md-icon>
                            </md-button>

                            <md-button type="submit" class="md-secondary md-icon-button"
                                       ng-href="<?= $deleteUrl.'/{{lang.id}}'; ?>"
                                       onclick="return confirm('<?= $confirmation; ?>');">
                                <md-icon aria-label="<?= __('Delete') ?>">
                                    delete
                                </md-icon>
                            </md-button>
                        <?php } ?>
                    </md-list-item>
            </md-list>
            <?php
        }
        ?>
    </div>
</div>
