<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
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
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
use App\Model\CurrentUser;

/* @translators: title of Settings page */
$this->set('title_for_layout', $this->Pages->formatTitle(__('Settings')));
?>
<div id="annexe_content">
    <?php
        echo $this->element(
        'users_menu',
        array('username' => CurrentUser::get('username'))
    );
    ?>
</div>

<div id="main_content">
    <section class="md-whiteframe-1dp">
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2><?php echo __('Options'); ?></h2>
            </div>
        </md-toolbar>

        <?php echo $this->Form->create($userSettings, [
            'ng-cloak' => true,
            'url' => ['controller' => 'user', 'action' => 'save_settings']
        ]); ?>
        <md-list flex role="list" class="flex" >
            <md-subheader><?php echo __('Main options'); ?></md-subheader>
            <md-list-item>
                <?php $sendNotifications = $userSettings->send_notifications; ?>
                <md-checkbox
                    ng-false-value="0"
                    ng-true-value="1"
                    ng-model="sendNotifications"
                    ng-init="sendNotifications = <?= (int)$sendNotifications ?>"
                    class="md-primary">
                </md-checkbox>
                <p> <?php echo __('Send email notifications') ?></p>
                <div ng-hide="true">
                <?php
                    echo $this->Form->text(
                        'send_notifications',
                        array(
                        'value' => '{{sendNotifications}}'
                        )
                    );
                ?>
                </div>
            </md-list-item>

            <md-list-item>
                <?php $isPublic = $userSettings->settings['is_public']; ?>
                <md-checkbox
                    ng-false-value="0"
                    ng-true-value="1"
                    ng-model="isPublic"
                    ng-init="isPublic = <?= (int)$isPublic ?>"
                    class="md-primary">
                </md-checkbox>

                <p><?php echo __('Set your profile public') ?></p>
                <div ng-hide="true">
                <?php
                    echo $this->Form->control(
                        'settings.is_public',
                        array(
                        'value' => '{{isPublic}}'
                        )
                    );
                ?>
                </div>
                </p>
            </md-list-item>

            <md-list-item>
                <?php $useRecent = $userSettings->settings['use_most_recent_list']; ?>
                <md-checkbox
                    ng-false-value="0"
                    ng-true-value="1"
                    ng-model="useRecent"
                    ng-init="useRecent = <?= (int)$useRecent ?>"
                    class="md-primary">

                </md-checkbox>
                <p><?php echo __(
                'Remember the last list to which you assigned' .
                ' a sentence, and select it by default'
                ) ?> </p>
                <div ng-hide="true">
                <?php
                    echo $this->Form->control(
                        'settings.use_most_recent_list',
                        array(
                            'value' => '{{useRecent}}'
                        )
                    );
                ?>
                </div>
            </md-list-item>

            <md-list-item ng-show="useOldDesign">
                <?php $collapsibleTranslations = $userSettings->settings['collapsible_translations']; ?>
                <md-checkbox
                    ng-false-value="0"
                    ng-true-value="1"
                    ng-model="collapsibleTranslations"
                    ng-init="collapsibleTranslations = <?= (int)$collapsibleTranslations ?>"
                    class="md-primary">

                </md-checkbox>
                <p><?php echo __(
                'Display a link to expand/collapse translations ' .
                'when there are too many translations'
                ) ?></p>
                <div ng-hide="true">
                <?php
                    echo $this->Form->control(
                        'settings.collapsible_translations',
                        array(
                            'value' => '{{collapsibleTranslations}}'
                        )
                    );
                ?>
                </div>
            </md-list-item>

            <md-list-item>
                <?php $showTranscriptions = $userSettings->settings['show_transcriptions']; ?>
                <md-checkbox
                    ng-false-value="0"
                    ng-true-value="1"
                    ng-model="showTranscriptions"
                    ng-init="showTranscriptions = <?= (int)$showTranscriptions ?>"
                    class="md-primary">
                </md-checkbox>
                <p><?php echo __('Always show unreviewed transcriptions and alternative scripts') ?> </p>
                <div ng-hide="true">
                <?php
                    echo $this->Form->control(
                        'settings.show_transcriptions',
                        array(
                            'value' => '{{showTranscriptions}}'
                        )
                    );
                ?>
                </div>
            </md-list-item>

            <md-list-item>
                <?php
                    // The setting is "hide" but we display "show"
                    $hideRandomSentence = $userSettings->settings['hide_random_sentence'];
                ?>
                <md-checkbox
                    ng-false-value="1"
                    ng-true-value="0"
                    ng-model="hideRandomSentence"
                    ng-init="hideRandomSentence = <?= (int)$hideRandomSentence ?>"
                    class="md-primary">
                </md-checkbox>
                <p><?php echo __('Show random sentence on the homepage') ?> </p>
                <div ng-hide="true">
                <?php
                    echo $this->Form->control(
                        'settings.hide_random_sentence',
                        array(
                            'value' => '{{hideRandomSentence}}'
                        )
                    );
                ?>
                </div>
            </md-list-item>

            <md-list-item>
                <?php
                $sentencesByLanguageURL = $this->Url->build(
                    array(
                        'controller' => 'stats',
                        'action' => 'sentences_by_language'
                    )
                );
                $tip = format(
                    __(
                        'Enter <a href="{url}">ISO 639-3 codes</a>, separated with a comma (e.g.: jpn,epo,ara,deu). '.
                        'Tatoeba will then only display translations in the languages you '.
                        'indicated. You can leave this empty to display translations in all '.
                        'languages.'
                    ),
                    array(
                        'url' => $sentencesByLanguageURL
                    )
                );
                ?>
                <md-input-container class="md-block">
                    <?php
                    echo $this->Form->control('settings.lang', [
                        /* @translators: option label on settings page */
                        'label' => __('Languages')
                    ]);
                    ?>
                    <div class="hint"><?= $tip ?></div>
                </md-input-container>
            </md-list-item>

            <md-list-item>
                <p><?= __('Number of sentences per page'); ?></p>
                <?php echo $this->Form->control('settings.sentences_per_page', array(
                    'options' => array(10 => 10, 20 => 20, 50 => 50, 100 => 100),
                    'label' => ''
                )); ?>
            </md-list-item>

            <md-list-item ng-show="!useOldDesign">
                <p><?= __('Number of translations per sentence'); ?></p>
                <?php echo $this->Form->control('settings.max_visible_translations', [
                    'options' => [5 => 5, 10 => 10, 20 => 20, 50 => 50],
                    'label' => ''
                ]); ?>
            </md-list-item>

            <?php if ($userSettings->settings['can_switch_license']) : ?>
                <md-list-item>
                    <p><?= __('Default license for original sentences'); ?></p>
                    <?= $this->Form->control('settings.default_license', [
                        'options' => $this->SentenceLicense->getLicenseOptions(),
                        'label' => ''
                    ]); ?>
                </md-list-item>
            <?php endif; ?>
        </md-list>
        <br>

        <md-list flex role="list" class="flex">
            <md-subheader><?php echo __('Experimental options'); ?></md-subheader>
            <md-list-item>
                <?php
                echo $this->Html->div('experimental-info',
                    __(
                        'Options in this category are not fully functional, '.
                        'may not work for everyone and/or are in a phase of '.
                        'beta testing. They may change or be removed in the future.',
                        true
                    )
                );
                ?>
            </md-list-item>
            <md-list-item>
                <?php $collectionRatings = $userSettings->settings['users_collections_ratings']; ?>
                <md-checkbox
                    ng-false-value="0"
                    ng-true-value="1"
                    ng-model="collectionRatings"
                    ng-init="collectionRatings = <?= (int)$collectionRatings ?>"
                    class="md-primary">
                </md-checkbox>
                <p><?php echo __('Activate the feature to review sentences') ?></p>
                <div ng-hide="true">
                <?php
                    echo $this->Form->control(
                        'settings.users_collections_ratings',
                        array(
                        'value' => '{{collectionRatings}}'
                        )
                    );
                ?>
                </div>
            </md-list-item>
            <md-list-item>
                <?php $nativeIndicator = $userSettings->settings['native_indicator']; ?>
                <md-checkbox
                    ng-false-value="0"
                    ng-true-value="1"
                    ng-model="nativeIndicator"
                    ng-init="nativeIndicator = <?= (int)$nativeIndicator ?>"
                    class="md-primary">
                </md-checkbox>
                <p><?php echo format(
                    __(
                        'Mark sentences when the owner indicated in their profile that ' .
                        'they have a native level in the language of the sentence. In ' .
                        'the old design "(native)" is displayed next to the username. In ' .
                        'the new design a {star} is displayed next to the sentence.'
                    ),
                    ['star' => '<md-icon>star</md-icon>']
                ) ?></p>
                <div ng-hide="true">
                <?php
                    echo $this->Form->control(
                        'settings.native_indicator',
                        array(
                        'value' => '{{nativeIndicator}}'
                        )
                    );
                ?>
                </div>
            </md-list-item>
            <md-list-item>
                <?php $useOldDesign = !$userSettings->settings['use_new_design']; ?>
                <md-checkbox
                    ng-false-value="0"
                    ng-true-value="1"
                    ng-model="useOldDesign"
                    ng-init="useOldDesign = <?= (int)$useOldDesign ?>"
                    class="md-primary">
                </md-checkbox>
                <p><?php echo __(
                    'Display sentences with the old design. '.
                    'Note that the old design will be removed at some point.'
                ) ?></p>
                <div ng-hide="true">
                <?php
                echo $this->Form->control(
                    'settings.use_new_design',
                    array(
                        'value' => '{{1-useOldDesign}}'
                    )
                );
                ?>
                </div>
            </md-list-item>
        </md-list>
        <br>

        <div layout="row" layout-align="center center">
            <md-button type="submit" class="md-raised md-primary">
                <?php /* @translators: submit button of settings edition form in settings page (verb) */ ?>
                <?php echo __('Save'); ?>
            </md-button>
        </div>
        <br>

        <?php echo $this->Form->end(); ?>
    </section>

    <section class="md-whiteframe-1dp">
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2><?php echo __('Change email address'); ?></h2>
            </div>
        </md-toolbar>

        <div layout-padding>
        <?php
        echo $this->Form->create($userSettings, [
            'url' => ['controller' => 'user', 'action' => 'save_basic']
        ]);
        ?>
            <md-input-container class="md-block">
                <?php
                echo $this->Form->control('email', [
                    'label' => __('Email address')
                ]);
                ?>
            </md-input-container>
            <div layout="row" layout-align="center center">
                <md-button type="submit" class="md-raised md-primary">
                    <?php /* @translators: submit button of email address edition form in settings page (verb) */ ?>
                    <?php echo __('Save'); ?>
                </md-button>
            </div>
        <?php
        echo $this->Form->end();
        ?>
        </div>
    </section>

    <section class="md-whiteframe-1dp">
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2><?php echo __('Change password'); ?></h2>
            </div>
        </md-toolbar>

        <div layout-padding>
        <?php
        echo $this->Form->create($userSettings, [
            'url' => ['controller' => 'user', 'action' => 'save_password']
        ]);
        ?>
            <md-input-container class="md-block">
                <?php
                echo $this->Form->control('old_password', [
                    'label' => __('Old password'),
                    'type' => 'password'
                ]);
                ?>
            </md-input-container>
            <md-input-container class="md-block">
                <?php
                echo $this->Form->control('new_password', [
                    'label' => __('New password'),
                    'type' => 'password'
                ]);
                ?>
            </md-input-container>
            <md-input-container class="md-block">
                <?php
                echo $this->Form->control('new_password2', [
                    'label' => __('New password again'),
                    'type' => 'password'
                ]);
                ?>
            </md-input-container>
            <div layout="row" layout-align="center center">
                <md-button type="submit" class="md-raised md-primary">
                    <?php /* @translators: submit button of password edition form in settings page (verb) */ ?>
                    <?php echo __('Save'); ?>
                </md-button>
            </div>
        <?php
        echo $this->Form->end();
        ?>
        </div>
    </section>
</div>
