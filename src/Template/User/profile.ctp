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
$this->AssetCompress->script('sentence-component.js', ['block' => 'scriptBottom']); // TODO: this is just to get the languageIcon directive
echo $this->Html->css('user/language.css');

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

    <md-list class="annexe-menu md-whiteframe-1dp">
        <?php
            $url = $this->Url->build([
                'controller' => 'private_messages',
                'action' => 'write',
                $username,
            ]);
        ?>
        <md-list-item href="<?= $url ?>">
            <md-icon>email</md-icon>
            <p><?= format(__('Contact {user}'), ['user' => $username]) ?></p>
        </md-list-item>
    </md-list>

    <div class="section md-whiteframe-1dp">
        <?php /* @translators: header text in side bar of profile pages (noun) */ ?>
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
            <?php /* @translators: header text for settings on profile page */ ?>
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
                        <?php /* @translators: edit button for settings on profile page (verb) */ ?>
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
                            <?php /* @translators: profile edition button on profile page (verb) */ ?>
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
                __x('user', 'Name') => h($realName),
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
                        <div flex><span ng-non-bindable><?= $value ? $value : '-' ?></span></div>
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
$userLanguages = h(json_encode($userLanguages));
?>
    <div class="section md-whiteframe-1dp"
         ng-cloak
         ng-controller="LanguageController as vm"
         ng-init="vm.init(<?= str_replace('{{', '\{\{', $userLanguages) ?>)">
        <?php /* @translators: header text on profile page */ ?>
        <h2><?= __('Languages'); ?></h2>

        <p ng-if="vm.langs.length === 0">
            <?= __('No language added.') ?>
        </p>
        <p ng-if="vm.langs.length === 0">
            <?php
            if ($username == $currentMember) {
                echo __('TIP: We encourage you to indicate the languages you know.');
            } else {
                echo __(
                    'TIP: Encourage this user to indicate the languages '.
                    'he or she knows.'
                );
            }
            ?>
        </p>
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
                    <language-level level="lang.level"></language-level>
                    <p>
                        {{lang.details}}
                    </p>
                </div>
                <?php if ($username == $currentMember) {
                    ?>
                    <md-button class="md-secondary md-icon-button"
                               aria-label="<?= __('Edit') ?>"
                               ng-href="<?= $editLangUrl.'/{{lang.language_code}}' ?>">
                        <?php /* @translators: user language edition button on profile page (verb) */ ?>
                        <md-icon aria-label="<?= __('Edit') ?>">
                            edit
                        </md-icon>
                    </md-button>

                    <md-button type="submit" class="md-secondary md-icon-button"
                               ng-href="<?= $deleteUrl.'/{{lang.id}}'; ?>"
                               onclick="return confirm('<?= $confirmation; ?>');">
                        <?php /* @translators: user language deletion button on profile page (verb) */ ?>
                        <md-icon aria-label="<?= __('Delete') ?>">
                            delete
                        </md-icon>
                    </md-button>
                <?php } ?>
            </md-list-item>
        </md-list>
        <?php
        if ($username == $currentMember) {
            ?>
            <md-button aria-label="<?= __('Add a language') ?>"
                       class="md-primary md-raised"
                       ng-click="vm.addLangNextStep()"
                       ng-if="vm.addLangStep === ''">
                <?= __('Add a language') ?>
            </md-button>
            <div ng-if="vm.addLangStep != ''" class="user-language-form">
                <md-divider></md-divider>
                <h3><?= __('Add a language'); ?></h3>
                <md-list><md-list-item ng-if="vm.addLangStep != 'selection'" class="md-2-line">
                    <language-icon lang="vm.selectedLang.code" title="vm.selectedLang.name"></language-icon>
                    <div class="md-list-item-text">
                        <h3 flex>
                            {{vm.selectedLang.name}}
                        </h3>
                        <language-level level="vm.selectedLang.level"></language-level>
                        <p>
                            {{vm.selectedLang.details}}
                        </p>
                    </div>
                </md-list-item></md-list>

                <div ng-if="vm.addLangStep === 'selection'">
                    <md-list-item class="md-2-line">
                        <div class="info" layout="row" layout-align="start center">
                            <?php
                            $languagesList = $this->Languages->onlyLanguagesArray(false);

                            echo $this->Html->tag(
                                'label',
                                __('Language:'),
                                ['for' => 'language_code']
                            );
                            echo $this->element(
                                'language_dropdown',
                                [
                                    'name' => 'language_code',
                                    'languages' => $languagesList,
                                    'selectedLanguage' => 'vm.selectedLang',
                                ]
                            );
                            ?>
                        </div>
                    </md-list-item>

                    <?php
                    $hintText = format(
                        __('If your language is missing, please read our article on how to <a href="{}">request a new language</a>.'),
                        $this->Pages->getWikiLink('new-language-request')
                    );
                    echo $this->Html->para('hint', $hintText);
                    ?>
                </div>

                <!-- Level -->
                <div ng-if="vm.addLangStep === 'level'" class="info">
                    <?php
                    $radioLabels = $this->Languages->getLevelsLabels();
                    ?>
                    <label><?= __('What is your level?') ?></label>
                    <md-radio-group ng-model='vm.selectedLang.level'>
                        <?php foreach($radioLabels as $key => $radioLabel) { ?>
                            <md-radio-button ng-value='<?= $key ?>' class='md-primary'>
                                <?= $radioLabel ?>
                            </md-radio-button>
                        <?php } ?>
                    </md-radio-group>
                </div>

                <!-- Details -->
                <div ng-if="vm.addLangStep === 'details'" class="info">
                    <?php
                    echo $this->Form->label(
                        'details',
                        __(
                            'Details (optional). '.
                            'For instance, which dialect or from which country.'
                        )
                    );
                    echo $this->Form->textarea('details', ['ng-model' => 'vm.selectedLang.details']);
                    ?>
                </div>

                <!-- Spinner -->
                <md-progress-circular ng-if="vm.addLangStep === 'loading'" md-mode="indeterminate" class="block-loader"></md-progress-circular>

                <!-- Error message -->
                <div ng-if="vm.addLangStep === 'error'">
                    <p>{{vm.error}}</p>
                    <md-button ng-click="vm.addLangNextStep()" class="md-raised">
                        <?php /* @translators: closing button of error message on user language addition form */ ?>
                        <?= __('OK') ?>
                    </md-button>
                </div>

                <!-- Form buttons -->
                <div ng-if="vm.addLangStep != 'error' && vm.addLangStep != 'loading'" layout="row">
                    <md-button class="md-raised" ng-click="vm.resetForm()">
                        <?php /* @translators: cancel button of user language addition form (directly from profile page) (verb) */ ?>
                        <?= __('Cancel') ?>
                    </md-button>

                    <md-button type="submit" ng-disabled="!vm.selectedLang" ng-click="vm.addLangNextStep()" class="md-raised md-primary">
                        <span ng-if="vm.addLangStep !== 'details'"><?= __('Next') ?></span>
                        <span ng-if="vm.addLangStep === 'details'"><?= __('Add this language') ?></span>
                    </md-button>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
