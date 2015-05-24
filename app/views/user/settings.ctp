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
    <div class="module">
        <h2><?php __('Options'); ?></h2>
        
        <?php        
        echo $form->create(
            null, 
            array(
                'action' => 'save_settings'
            )
        );
        ?>
        
        <div>
            <?php echo $form->checkbox('settings.send_notifications'); ?>
            <label for="UserSendNotifications">
                <?php __('Email notifications'); ?>
            </label>
        </div>
        
        <div>
            <?php echo $form->checkbox('settings.is_public'); ?>
            <label for="UserIsPublic">
                <?php __('Set your profile public?'); ?>
            </label>
        </div>

        <div>
            <?php echo $form->checkbox('jquery_chosen'); ?>
            <label for="UserJqueryChosen">
                <?php __(
                    'Advanced language selector (experimental). Note: this '.
                    'option is saved in your cookies so it will only apply '.
                    'for your current browser.'
                ); ?>
            </label>
        </div>
        
        <div>
            <?php echo $form->checkbox('use_most_recent_list'); ?>
            <label for="UserUseMostRecentList">
                <?php __(
                    'Remember the last list to which you assigned a '.
                    'sentence, and select it by default.'
                ); ?>
            </label>
        </div>

        <div>
            <?php echo $form->checkbox('collapsible_translations_enabled'); ?>
            <label for="UserCollapsibleTranslationsEnabled">
                <?php __(
                    'Display a link to expand/collapse translations '.
                    'when there are too many translations.'
                ); ?>
            </label>
        </div>
        
        <div>
            <?php echo $form->checkbox('restrict_search_langs_enabled'); ?>
            <label for="RestrictSearchLangsEnabled">
                <?php __(
                    'Restrict languages listed in the search bar '.
                    'to the ones in your profile.'
                ); ?>
            </label>
        </div>
        
        <div>
        <?php
        $tip = __(
            'Enter ISO 639-3 codes, separated with a comma (e.g.: jpn,epo,ara,deu). '.
            'Tatoeba will then only display translations in the languages you '.
            'indicated. You can leave this empty to display translations in all '.
            'languages.', true
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
        
        <?php echo $form->end(__('Save', true)); ?>
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
        echo $form->end(__('Save', true));
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
        echo $form->end(__('Save', true));
        ?>
    </div>
</div>
