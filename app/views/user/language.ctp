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

        $languagesList = $languages->onlyLanguagesArray();

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


        // Level
        echo $form->radio(
            'level',
            array(
                0 => __('0: Almost no knowledge', true),
                1 => __('1: Beginner', true),
                2 => __('2: Intermediate', true),
                3 => __('3: Advanced', true),
                4 => __('4: Fluent', true),
                5 => __('5: Native level', true)
            ),
            array(
                'legend' => __('What is your level?', true),
                'separator' => '<br/>'
            )
        );

        // Details
        echo $html->tag(
            'label',
            __('Details (optional)', true),
            array('for' => 'AddUsersLanguagesDetails')
        );
        echo $form->textarea('details');

        // Buttons
        echo '<div class="buttons">';
        if (!empty($this->data)) {
            echo $html->link(
                __('Delete', true),
                array(
                    'controller' => 'users_languages',
                    'action' => 'delete',
                    $this->data['UsersLanguages']['id']
                ),
                array('class' => 'delete button'),
                __('Are you sure?', true)
            );
        }

        echo $html->link(
            __('Cancel', true),
            array(
                'controller' => 'user',
                'action' => 'profile',
                $username
            ),
            array('class' => 'cancel button')
        );

        echo $form->button(
            $submitLabel,
            array('class' => 'submit button')
        );
        echo '</div>';

        echo $form->end();
        ?>
    </div>
</div>