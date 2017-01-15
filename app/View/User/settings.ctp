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
 * @link     http://tatoeba.org
 */
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
    <div md-whiteframe="1" class="options settings-form">
        <?php echo $this->Form->create(null, array('url' => array('action' => 'save_settings'))); ?>
        <h2><?php echo __('Options'); ?></h2>
        <md-list flex role="list" class="flex" >
            <md-subheader><?php echo __('Main options'); ?></md-subheader>
            <md-list-item>
                <?php $sendNotifications = $this->request->data['User']['send_notifications']; ?>
                <md-checkbox
                    ng-false-value="0"
                    ng-true-value="1"
                    ng-model="sendNotifications"
                    ng-init="sendNotifications = <?= $sendNotifications ?>"
                    class="md-primary">
                </md-checkbox>
                <p> <?php echo __('Email notifications') ?></p>
                <?php
                    echo $this->Form->hidden(
                        'send_notifications',
                        array(
                        'value' => '{{sendNotifications}}'
                        )
                    );
                ?>
            </md-list-item>

            <md-list-item>
                <?php $isPublic = $this->request->data['User']['settings']['is_public']; ?>
                <md-checkbox
                    ng-false-value="0"
                    ng-true-value="1"
                    ng-model="isPublic"
                    ng-init="isPublic = <?= $isPublic ?>"
                    class="md-primary">
                </md-checkbox>

                <p><?php echo __('Set your profile public?') ?></p>
                <?php
                    echo $this->Form->hidden(
                        'settings.is_public',
                        array(
                        'value' => '{{isPublic}}'
                        )
                    );
                ?>
            </md-list-item>

            <md-list-item>
                <?php $useRecent = $this->request->data['User']['settings']['use_most_recent_list']; ?>
                <md-checkbox
                    ng-false-value="0"
                    ng-true-value="1"
                    ng-model="useRecent"
                    ng-init="useRecent = <?= $useRecent ?>"
                    class="md-primary">

                </md-checkbox>
                <p><?php  __(
                'Remember the last list to which you assigned' .
                ' a sentence, and select it by default.'
                ) ?> </p>
                <?php
                    echo $this->Form->hidden(
                        'settings.use_most_recent_list',
                        array(
                        'value' => '{{useRecent}}'
                        )
                    );
                ?>
            </md-list-item>

            <md-list-item>
                <?php $collapsibleTranslations = $this->request->data['User']['settings']['collapsible_translations']; ?>
                <md-checkbox
                    ng-false-value="0"
                    ng-true-value="1"
                    ng-model="collapsibleTranslations"
                    ng-init="collapsibleTranslations = <?= $collapsibleTranslations ?>"
                    class="md-primary">

                </md-checkbox>
                <p><?php echo __(
                'Display a link to expand/collapse translations ' .
                'when there are too many translations.'
                ) ?></p>
                <?php
                    echo $this->Form->hidden(
                        'settings.collapsible_translations',
                        array(
                        'value' => '{{collapsibleTranslations}}'
                        )
                    );
                ?>
            </md-list-item>

            <md-list-item>
                <?php $showTranscriptions = $this->request->data['User']['settings']['show_transcriptions']; ?>
                <md-checkbox
                    ng-false-value="0"
                    ng-true-value="1"
                    ng-model="showTranscriptions"
                    ng-init="showTranscriptions = <?= $showTranscriptions ?>"
                    class="md-primary">
                </md-checkbox>
                <p><?php echo __('Always show transcriptions and alternative scripts') ?> </p>
                <input type="checkbox" name="data[User][settings][show_transcriptions]" value="{{showTranscriptions}}" style="display: none;" checked/>
            </md-list-item>

            <md-list-item>
                <?php $hideRandomSentence = $this->request->data['User']['settings']['hide_random_sentence']; ?>
                <md-checkbox
                    ng-false-value="0"
                    ng-true-value="1"
                    ng-model="hideRandomSentence"
                    ng-init="hideRandomSentence = <?= $hideRandomSentence ?>"
                    class="md-primary">
                </md-checkbox>
                <p><?php echo __('Hide random sentence on the homepage') ?> </p>
                <?php
                    echo $this->Form->hidden(
                        'settings.hide_random_sentence',
                        array(
                        'value' => '{{hideRandomSentence}}'
                        )
                    );
                ?>
            </md-list-item>

            <md-list-item>
                <?php
                $sentencesByLanguageURL = $this->Html->url(
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
                        'languages.', true
                    ),
                    array(
                        'url' => $sentencesByLanguageURL
                    )
                );
                ?>
                <md-input-container class="md-block">
                    <?php
                    echo $this->Form->input(
                        'settings.lang',
                        array(
                            'label' => __('Languages'),
                            'after' => '<div class="hint">'.$tip.'</div>'
                        )
                    );
                    ?>
                </md-input-container>
            </md-list-item>

            <md-list-item>
                <p><? echo __('Number of sentences per page'); ?></p>
                <?php echo $this->Form->input('settings.sentences_per_page', array(
                    'options' => array(10 => 10, 20 => 20, 50 => 50, 100 => 100),
                    'label' => ''
                )); ?>
            </md-list-item>
        </md-list>

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
                <?php $collectionRatings = $this->request->data['User']['settings']['users_collections_ratings']; ?>
                <md-checkbox
                    ng-false-value="0"
                    ng-true-value="1"
                    ng-model="collectionRatings"
                    ng-init="collectionRatings = <?= $collectionRatings ?>"
                    class="md-primary">
                </md-checkbox>
                <p><?php echo __('Activate the feature to rate sentences and build your collection of sentences.') ?></p>
                <?php
                    echo $this->Form->hidden(
                        'settings.users_collections_ratings',
                        array(
                        'value' => '{{collectionRatings}}'
                        )
                    );
                ?>
            </md-list-item>
            <md-list-item>
                <?php $nativeIndicator = $this->request->data['User']['settings']['native_indicator']; ?>
                <md-checkbox
                    ng-false-value="0"
                    ng-true-value="1"
                    ng-model="nativeIndicator"
                    ng-init="nativeIndicator = <?= $nativeIndicator ?>"
                    class="md-primary">
                </md-checkbox>
                <p><?php echo __(
                'Display "(native)" next to username on sentences ' .
                'when the owner indicated in their profile that they have a native '.
                'level in the language of the sentence.'
                ) ?></p>
                <?php
                    echo $this->Form->hidden(
                        'settings.native_indicator',
                        array(
                        'value' => '{{nativeIndicator}}'
                        )
                    );
                ?>
            </md-list-item>
            <md-list-item>
                <?php $copyButton = $this->request->data['User']['settings']['copy_button']; ?>
                <md-checkbox
                    ng-false-value="0"
                    ng-true-value="1"
                    ng-model="copyButton"
                    ng-init="copyButton = <?= $copyButton ?>"
                    class="md-primary">
                </md-checkbox>
                <p><?php echo __('Display button to copy a sentence to the clipboard.') ?></p>
                <?php
                    echo $this->Form->hidden(
                        'settings.copy_button',
                        array(
                        'value' => '{{copyButton}}'
                        )
                    );
                ?>
            </md-list-item>
            <md-list-item>
                <?php $useNewDesign = $this->request->data['User']['settings']['use_new_design']; ?>
                <md-checkbox
                    ng-false-value="0"
                    ng-true-value="1"
                    ng-model="useNewDesign"
                    ng-init="useNewDesign = <?= $useNewDesign ?>"
                    class="md-primary">
                </md-checkbox>
                <p><?php echo __(
                    'Display sentences with the new design. '.
                    'Note that you will not have all the features '.
                    'from the old design.'
                ) ?></p>
                <?php
                echo $this->Form->hidden(
                    'settings.use_new_design',
                    array(
                        'value' => '{{useNewDesign}}'
                    )
                );
                ?>
            </md-list-item>
        </md-list>

        <div layout="row" layout-align="center center">
            <md-button type="submit" class="md-raised md-primary">
                <?php echo __('Save'); ?>
            </md-button>
        </div>

        <?php echo $this->Form->end(); ?>
    </div>
    <div md-whiteframe="1" class="settings-form">
        <?php
        echo $this->Form->create(
            null,
            array(
                'url' => array('action' => 'save_basic')
            )
        );
        ?>
            <h2><?php echo __('Change email address'); ?></h2>
            <md-input-container class="md-block">
                <?php
                echo $this->Form->input(
                    'User.email',
                    array(
                        'label' => __('Email address')
                    )
                );
                ?>
            </md-input-container>
            <div layout="row" layout-align="center center">
                <md-button type="submit" class="md-raised md-primary">
                    <?php echo __('Save'); ?>
                </md-button>
            </div>
        <?php
        echo $this->Form->end();
        ?>
    </div>

    <div md-whiteframe="1" class="settings-form">
        <?php
        echo $this->Form->create(
            'User',
            array(
                'url' => array(
                    'controller' => 'user',
                    'action' => 'save_password'
                )
            )
        );
        ?>
        <h2><?php echo __('Change password'); ?></h2>
            <md-input-container class="md-block">
                <?php
                echo $this->Form->input(
                    'old_password',
                    array(
                        "label" => __('Old password'),
                        "type" => "password"
                    )
                );
                ?>
            </md-input-container>
            <md-input-container class="md-block">
                <?php
                echo $this->Form->input(
                    'new_password',
                    array(
                        "label" => __('New password'),
                        "type" => "password"
                    )
                );
                ?>
            </md-input-container>
            <md-input-container class="md-block">
                <?php
                echo $this->Form->input(
                    'new_password2',
                    array(
                        "label" => __('New password again'),
                        "type" => "password"
                    )
                );
                ?>
            </md-input-container>
            <div layout="row" layout-align="center center">
                <md-button type="submit" class="md-raised md-primary">
                    <?php echo __('Save'); ?>
                </md-button>
            </div>
        <?php
        echo $this->Form->end();
        ?>
    </div>
</div>
