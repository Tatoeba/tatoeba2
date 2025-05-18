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

if (!$userLanguage) {
    $title = __('Add a language');
    $submitLabel = __('Add language');
} else {
    $title = __('Edit language');
    /* @translators: submit button of user language edition form (verb) */
    $submitLabel = __('Save');
    $userLanguage->details = $this->safeForAngular($userLanguage->details);
}

$this->set('title_for_layout', h($this->Pages->formatTitle($title)));
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
    <div class="user-language-form section md-whiteframe-1dp">
        <?php
        echo $this->Html->tag('h2', $title);

        echo $this->Form->create($userLanguage, [
            'ng-cloak' => true,
            'url' => ['controller' => 'users_languages', 'action' => 'save']
        ]);
        echo $this->Form->hidden('id');
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
            if (!$userLanguage) {
                echo $this->element(
                    'language_dropdown',
                    array(
                        'name' => 'language_code',
                        'languages' => $languagesList
                    )
                );
            } else {
                $languageCode = $userLanguage->language_code;
                echo $this->Languages->codeToNameAlone($languageCode);
            }
            ?>
        </div>

        <?php
        if (!$userLanguage) {
            $hintText = format(
                __('If your language is missing, please read our article on how to <a href="{}">request a new language</a>.'),
                $this->Pages->getWikiLink('new-language-request')
            );
            echo $this->Html->para('hint', $hintText);
        }
        ?>

        <md-divider></md-divider>

        <!-- Level -->
        <div class="info">
            <?php
            $selected = '';
            if ($userLanguage && !is_null($userLanguage->level)) {
                $selected = $userLanguage->level;
            }
            $selected = h(json_encode($selected));

            $radioLabels = $this->Languages->getLevelsLabels();

            $this->Form->unlockField('level');
            echo $this->Form->hidden(
                'level', array('value' => '{{userLanguage.level}}')
            );
            ?>

            <label><?= __('What is your level?') ?></label>
            <md-radio-group ng-model='userLanguage.level' ng-init="userLanguage.level = <?= $selected ?>">
                <?php foreach($radioLabels as $key => $radioLabel) { ?>
                    <md-radio-button ng-value='<?= $key ?>' class='md-primary'>
                        <?= $radioLabel ?>
                    </md-radio-button>
                <?php } ?>
            </md-radio-group>
        </div>

        <md-divider></md-divider>

        <!-- Details -->
        <div class="info">
            <?php
            echo $this->Form->label(
                'details',
                __(
                    'Details (optional). '.
                    'For instance, which dialect or from which country.', true
                )
            );
            echo $this->Form->textarea('details');
            ?>
        </div>

        <div layout="row" layout-align="end center">
            <?php
            $cancelUrl = $this->Url->build(
                array(
                    'controller' => 'user',
                    'action' => 'profile',
                    $username
                )
            );
            ?>
            <md-button class="md-raised" href="<?= $cancelUrl; ?>">
                <?php /* @translators: cancel button of user language addition/edition form (verb) */ ?>
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
