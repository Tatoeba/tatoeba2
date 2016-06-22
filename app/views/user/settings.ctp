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
$this->set('title_for_layout', $pages->formatTitle(__('Settings', true)));
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
    <div class="module options">
        <?php echo $form->create(null, array('action' => 'save_settings')); ?>

        <h2><?php __('Options'); ?></h2>
        <fieldset>
            <div>
                <?php $sendNotifications = $this->data['User']['send_notifications']; ?>
                <md-checkbox 
                    ng-false-value="0" 
                    ng-true-value="1" 
                    ng-model="sendNotifications" 
                    ng-init="sendNotifications = <?= $sendNotifications ?>"
                    class="md-primary">
                <label> <?= __('Email notifications') ?></label> 
                </md-checkbox>
                <?php
                    echo $form->hidden(
                        'send_notifications',
                        array(
                        'value' => '{{sendNotifications}}'
                        )
                    );
                ?>
            </div>

            <div>
                <?php $isPublic = $this->data['User']['settings']['is_public']; ?>
                <md-checkbox 
                    ng-false-value="0" 
                    ng-true-value="1" 
                    ng-model="isPublic" 
                    ng-init="isPublic = <?= $isPublic ?>" 
                    class="md-primary"> 
                <label><?= __('Set your profile public?') ?></label>
                </md-checkbox>
                <?php
                    echo $form->hidden(
                        'settings.is_public',
                        array(
                        'value' => '{{isPublic}}'
                        )
                    );
                ?>
            </div>

            <div>
                <?php $useRecent = $this->data['User']['settings']['use_most_recent_list']; ?>
                <md-checkbox 
                    ng-false-value="0"
                    ng-true-value="1" 
                    ng-model="useRecent" 
                    ng-init="useRecent = <?= $useRecent ?>" 
                    class="md-primary"> 
                <label><?=  __(
                'Remember the last list to which you assigned' . 
                ' a sentence, and select it by default.'
                ) ?> </label>
                </md-checkbox>
                <?php
                    echo $form->hidden(
                        'settings.use_most_recent_list',
                        array(
                        'value' => '{{useRecent}}'
                        )
                    );
                ?>
            </div>

            <div>
                <?php $collapsibleTranslations = $this->data['User']['settings']['collapsible_translations']; ?>
                <md-checkbox 
                    ng-false-value="0" 
                    ng-true-value="1" 
                    ng-model="collapsibleTranslations" 
                    ng-init="collapsibleTranslations = <?= $collapsibleTranslations ?>" 
                    class="md-primary">
                <label><?= __(
                'Display a link to expand/collapse translations ' . 
                'when there are too many translations.'
                ) ?></label>
                </md-checkbox>
                <?php
                    echo $form->hidden(
                        'settings.collapsible_translations',
                        array(
                        'value' => '{{collapsibleTranslations}}'
                        )
                    );
                ?>
            </div>

            <div>
                <?php $showTranscriptions = $this->data['User']['settings']['show_transcriptions']; ?>
                <md-checkbox 
                    ng-false-value="0" 
                    ng-true-value="1" 
                    ng-model="showTranscriptions" 
                    ng-init="showTranscriptions = <?= $showTranscriptions ?>" 
                    class="md-primary">
                <label><?= __('Always show transcriptions and alternative scripts') ?> </label>
                </md-checkbox>
                <input type="checkbox" name="data[User][settings][show_transcriptions]" value="{{showTranscriptions}}" style="display: none;" checked/>
            </div>

            <div>
                <?php $hideRandomSentence = $this->data['User']['settings']['hide_random_sentence']; ?>
                <md-checkbox 
                    ng-false-value="0" 
                    ng-true-value="1" 
                    ng-model="hideRandomSentence" 
                    ng-init="hideRandomSentence = <?= $hideRandomSentence ?>" 
                    class="md-primary">
                <label><?= __('Hide random sentence on the homepage') ?> </label>
                </md-checkbox>
                <?php
                    echo $form->hidden(
                        'settings.hide_random_sentence',
                        array(
                        'value' => '{{hideRandomSentence}}'
                        )
                    );
                ?>
            </div>

            <div>
                <?php
                $sentencesByLanguageURL = $html->url(
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
                echo $form->input(
                    'settings.lang',
                    array(
                        'label' => __('Languages', true),
                        'after' => '<div>'.$tip.'</div>'
                    )
                );
                ?>
            </div>

            <div>
                <?php echo $form->input('settings.sentences_per_page', array(
                    'options' => array(10 => 10, 20 => 20, 50 => 50, 100 => 100),
                    'label' => __('Number of sentences per page', true),
                )); ?>
            </div>
        </fieldset>


        <fieldset>
            <legend><?php __('Experimental options'); ?></legend>

            <?php
            echo $html->div('experimental-info',
                __(
                    'Options in this category are not fully functional, '.
                    'may not work for everyone and/or are in a phase of '.
                    'beta testing. They may change or be removed in the future.',
                    true
                )
            );
            ?>

            <div>

            <div>
                <?php $collectionRatings = $this->data['User']['settings']['users_collections_ratings']; ?>
                <md-checkbox 
                    ng-false-value="0" 
                    ng-true-value="1" 
                    ng-model="collectionRatings" 
                    ng-init="collectionRatings = <?= $collectionRatings ?>" 
                    class="md-primary">
                <label><?= __('Activate the feature to rate sentences and build your collection of sentences.') ?></label>
                </md-checkbox>
                <?php
                    echo $form->hidden(
                        'settings.users_collections_ratings',
                        array(
                        'value' => '{{collectionRatings}}'
                        )
                    );
                ?>
            </div>

            <div>
                <?php $nativeIndicator = $this->data['User']['settings']['native_indicator']; ?>
                <md-checkbox 
                    ng-false-value="0" 
                    ng-true-value="1"
                    ng-model="nativeIndicator" 
                    ng-init="nativeIndicator = <?= $nativeIndicator ?>" 
                    class="md-primary">
                <label><?= __(
                'Display "(native)" next to username on sentences ' . 
                'when the owner indicated in their profile that they have a native '.
                'level in the language of the sentence.'
                ) ?></label>
                </md-checkbox>
                <?php
                    echo $form->hidden(
                        'settings.native_indicator',
                        array(
                        'value' => '{{nativeIndicator}}'
                        )
                    );
                ?>
            </div>

            <div>
            <?php $copyButton = $this->data['User']['settings']['copy_button']; ?>
                <md-checkbox 
                    ng-false-value="0" 
                    ng-true-value="1" 
                    ng-model="copyButton" 
                    ng-init="copyButton = <?= $copyButton ?>" 
                    class="md-primary">
                <label><?= __('Display button to copy a sentence to the clipboard.') ?></label>
                </md-checkbox>
                <?php
                    echo $form->hidden(
                        'settings.copy_button',
                        array(
                        'value' => '{{copyButton}}'
                        )
                    );
                ?>
            </div>
        </fieldset>

        <md-button type="submit" class="md-raised md-primary">
            <?php __('Save'); ?>
        </md-button>
        <?php echo $form->end(); ?>
    </div>

    
    <div class="module">
        <h2><?php __('Change email address'); ?></h2>
        <?php
        echo $form->create(
            null,
            array(
                'action' => 'save_basic'
            )
        );
        echo $form->input(
            'User.email',
            array(
                'label' => __('Email address', true)
            )
        );
        ?>
        <md-button type="submit" class="md-raised md-primary">
            <?php __('Save'); ?>
        </md-button>
        <?php
        echo $form->end();
        ?>
    </div>
    
    <div class="module">
        <h2><?php __('Change password'); ?></h2>
        <?php
        echo $form->create(
            'User',
            array(
                'url' => array(
                    'controller' => 'user',
                    'action' => 'save_password'
                )
            )
        );
        echo $form->input(
            'old_password',
            array(
                "label" => __('Old password', true),
                "type" => "password"
            )
        );
        echo $form->input(
            'new_password',
            array(
                "label" => __('New password', true),
                "type" => "password"
            )
        );
        echo $form->input(
            'new_password2',
            array(
                "label" => __('New password again', true),
                "type" => "password"
            )
        );
        ?>
        <md-button type="submit" class="md-raised md-primary">
            <?php __('Save'); ?>
        </md-button>
        <?php
        echo $form->end();
        ?>
    </div>
</div>
