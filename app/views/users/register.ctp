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

$this->pageTitle = 'Tatoeba - ' . __('Register', true);

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
                "class" => "registrationField"
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
        echo $form->checkbox('mask_password'); 
        echo ' ';
        __('unmask password');
        ?>
    </td>
</tr>
<tr>
    <td class="field">
        <label for="registrationEmail"><?php __('Email:'); ?></label>
    </td>
    <td>
        <?php
        echo $form->input(
            'email',
            array(
                "label" => "",
                "id" => "registrationEmail",
                "class" => "registrationField"
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
        "label" => __('What are the five first letters of your email?', true)
    )
);

__('For instance, if your email is me12345@example.com, the answer is "me123".');
?>
</div>

<div id="termsOfUse">
<?php
$lang = $this->params['lang'];
echo $form->checkbox('acceptation_terms_of_use'); echo ' ';
echo sprintf(
    __('I accept the <a href="%s">terms of use</a>',true), 
    $html->url(array("controller"=>"pages", "action"=>"terms_of_use#$lang"))
);
?>
</div>

<?php
echo $form->submit(__('Register',true), array("id" => "registerButton"));
echo $form->end();
?>
