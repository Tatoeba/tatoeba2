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

if (empty($this->data)) {
    $title = __('Add a language', true);
    $submitLabel = __('Add language', true);
} else {
    $title = __('Edit language', true);
    $submitLabel = __('Save', true);
}

$this->set('title_for_layout', Sanitize::html($pages->formatTitle($title)));
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
    <div class="module">
        <?php
        echo $html->tag('h2', $title);

        $languagesList = $languages->onlyLanguagesArray(false);

        echo $form->create('UsersLanguages', array('action' => 'save', 'class' => 'form'));

        echo $form->hidden('id');
        echo $form->hidden('of_user_id', array('value' => $ofUserId));

        // Language
        echo $html->tag('label', __('Language:', true), array('for' => 'UsersLanguagesLanguageCode'));
        if (empty($this->data)) {
            echo $form->select(
                'language_code',
                $languagesList,
                null,
                array(
                    'class' => 'language-selector',
                    'empty' => false
                ),
                false
            );
        } else {
            $languageCode = $this->data['UsersLanguages']['language_code'];
            echo $languages->codeToNameAlone($languageCode);
        }

        $selected = -1;
        if (isset($this->data['UsersLanguages']['level'])) {
            $selected = $this->data['UsersLanguages']['level'];
        }

        // Level
        echo $form->radio(
            'level',
            $languages->getLevelsLabels(),
            array(
                'legend' => __('What is your level?', true),
                'separator' => '<br/>',
                'value' => $selected
            )
        );

        // Details
        echo $html->tag(
            'label',
            __('Details (optional). For instance, which dialect or from which country.', true),
            array('for' => 'AddUsersLanguagesDetails')
        );
        echo $form->textarea('details');

        // Buttons
        ?>
        <div layout="row" layout-align="end center">
            <?php
            if (!empty($this->data)) {
                $deleteUrl = $html->url(
                    array(
                        'controller' => 'users_languages',
                        'action' => 'delete',
                        $this->data['UsersLanguages']['id']
                    )
                );
                $confirmation = __('Are you sure?', true);
                ?>
                <md-button type="submit" class="md-raised md-warn"
                           href="<?= $deleteUrl; ?>"
                           onclick="return confirm('<?= $confirmation; ?>');">
                    <?php __('Delete this list'); ?>
                </md-button>
                <?php
            }

            $cancelUrl = $html->url(
                array(
                    'controller' => 'user',
                    'action' => 'profile',
                    $username
                )
            );
            ?>
            <md-button class="md-raised" href="<?= $cancelUrl; ?>">
                <?php __('Cancel'); ?>
            </md-button>

            <md-button type="submit" class="md-raised md-primary">
                <?php __('Submit translation'); ?>
            </md-button>
        </div>

        <?php
        echo $form->end();
        ?>

    </div>
</div>
