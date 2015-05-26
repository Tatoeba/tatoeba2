<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
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

/**
 * Page to register.
 *
 * @category Users
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */ 

$this->set('title_for_layout', $pages->formatTitle(__('Register', true)));

echo $javascript->link(JS_PATH . 'users.check_registration.js', false);

echo $form->create('User', array("action" => "register"));
?>
<table id="userInformation">
<tr>
    <td class="field">
        <label for="registrationUsername"><?php __('Username:'); ?></label>
    </td>
    <td>
        <?php
        echo $form->input(
            'username',
            array(
                "label" => "",
                "id" => "registrationUsername",
                "class" => "registrationField",
                'error' => array(
                    'alphanumeric' => __(
                        'Username can only contain letters, numbers, or underscore',
                        true
                    ),
                    'isUnique' => __(
                        'Username already taken.', 
                        true
                    ),
                    'min' => __(
                        'Username must be at least two characters long',
                        true
                    )
                )
            )
        );
        ?>
    </td>
</tr>
<tr>
    <td class="field">
        <label for="registrationPassword"><?php __('Password:'); ?></label>
    </td>
    <td>
        <div id="unmaskedPasswordContainer"></div>
        <?php
        echo $form->input(
            'password',
            array(
                "label" => "",
                "id" => "registrationPassword",
                "class" => "registrationField"
            )
        );
        
        // Box for users to actually see what their writing
        echo $form->input('mask_password', array(
            'type' => 'checkbox',
            'label' => __('unmask password', true),
        ));
        ?>
    </td>
</tr>
<tr>
    <td class="field">
        <label for="registrationEmail"><?php __('Email address:'); ?></label>
    </td>
    <td>
        <?php
        echo $form->input(
            'email',
            array(
                "label" => "",
                "id" => "registrationEmail",
                "class" => "registrationField",
                'error' => array(
                    'email' => __(
                        'Invalid email address', 
                        true
                    ),
                    'isUnique' => __(
                        'Email address already used.', 
                        true
                    )
                )
            )
        );
        ?>
    </td>
</tr>
</table>

<div id="quiz">
<?php
__('We need to make sure you are human.');

echo $form->input(
    'quiz', 
    array(
        "label" => __('What are the first five characters of your email address?', true)
    )
);

__('For instance, if your email address is a.b.cd@example.com, type a.b.c into the box.');
?>
</div>

<div id="termsOfUse">
<?php
$lang = $this->params['lang'];
$label = format(
    __('I accept the <a href="{}">terms of use</a>',true), 
    $html->url(array("controller"=>"pages", "action"=>"terms_of_use#$lang"))
);
echo $form->input('acceptation_terms_of_use', array(
    'type' => 'checkbox',
    'label' => $label,
));
?>
</div>

<?php
echo $form->submit(__('Register',true));
echo $form->end();
?>
