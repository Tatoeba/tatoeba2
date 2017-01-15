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

if (empty($this->request->data)) {
    $title = __('Add a language');
    $submitLabel = __('Add language');
} else {
    $title = __('Edit language');
    $submitLabel = __('Save');
}

$this->set('title_for_layout', Sanitize::html($this->Pages->formatTitle($title)));
?>
<div id="annexe_content">
    <?php
    echo $this->element(
        'users_menu',
        array('username' => $username)
    );
    ?>
</div>

<div id="main_content">
    <div class="user-language section" md-whiteframe="1">
        <?php
        echo $this->Html->tag('h2', $title);

        echo $this->Form->create('UsersLanguages', array(
            'url' => array('action' => 'save')
        ));
        echo $this->Form->hidden('id');
        echo $this->Form->hidden('of_user_id', array('value' => $ofUserId));
        ?>

        <!-- Language -->
        <div class="info" layout="row" layout-align="start center">
            <?php
            $languagesList = $this->Languages->onlyLanguagesArray(false);

            echo $this->Html->tag(
                'label',
                __('Language:'),
                array('for' => 'UsersLanguagesLanguageCode')
            );
            if (empty($this->request->data)) {
                echo $this->Form->select(
                    'language_code',
                    $languagesList,
                    array(
                        'class' => 'language-selector',
                        'empty' => false
                    ),
                    false
                );
            } else {
                $languageCode = $this->request->data['UsersLanguages']['language_code'];
                echo $this->Languages->codeToNameAlone($languageCode);
            }
            ?>
        </div>

        <md-divider></md-divider>

        <!-- Level -->
        <div class="info">
            <?php
            $selected = -1;
            if (isset($this->request->data['UsersLanguages']['level'])) {
                $selected = $this->request->data['UsersLanguages']['level'];
            }

            $radioLabels = $this->Languages->getLevelsLabels();
            ?>
            <input type="radio"
                   name="data[UsersLanguages][level]"
                   value="{{userLanguage.level}}"
                   ng-init="userLanguage.level = <?= $selected ?>"
                   checked hidden/>
            <label><?= __('What is your level?') ?></label>
            <md-radio-group ng-model='userLanguage.level'>
                <?php foreach($radioLabels as $key => $radioLabel) { ?>
                    <md-radio-button value='<?= $key ?>' class='md-primary'>
                        <?= $radioLabel ?>
                    </md-radio-button>
                <?php } ?>
            </md-radio-group>
        </div>

        <md-divider></md-divider>

        <!-- Details -->
        <div class="info">
            <?php
            echo $this->Html->tag(
                'label',
                __(
                    'Details (optional). '.
                    'For instance, which dialect or from which country.', true
                ),
                array('for' => 'AddUsersLanguagesDetails')
            );
            echo $this->Form->textarea('details');
            ?>
        </div>

        <div layout="row" layout-align="end center">
            <?php
            $cancelUrl = $this->Html->url(
                array(
                    'controller' => 'user',
                    'action' => 'profile',
                    $username
                )
            );
            ?>
            <md-button class="md-raised" href="<?= $cancelUrl; ?>">
                <?php echo __('Cancel'); ?>
            </md-button>

            <md-button type="submit" class="md-raised md-primary">
                <?= $submitLabel ?>
            </md-button>
        </div>

        <?php
        echo $this->Form->end();
        ?>

    </div>
</div>
