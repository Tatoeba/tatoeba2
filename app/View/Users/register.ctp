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

$this->set('title_for_layout', $this->Pages->formatTitle(__('Register')));

$this->Html->script(JS_PATH . 'users/register.ctrl.js', array('block' => 'scriptBottom'));

$this->Security->enableCSRFProtection();
echo $this->Form->create('User', array(
    'name' => 'registrationForm',
    'url' => array('action' => 'register'),
    'class' => 'md-whiteframe-1dp',
    'ng-controller' => 'UsersRegisterController as ctrl'
));


echo $this->Form->checkbox(
    'mask_password',
    array(
        'class' => 'ng-hide',
        'value' => '{{registration.maskPassword}}'
    )
);

echo $this->Form->checkbox(
    'acceptation_terms_of_use',
    array(
        'class' => 'ng-hide',
        'checked' => '{{registration.termsOfUse}}',
        'value' => '{{registration.termsOfUse ? 1 : 0 }}'
    )
);

$lang = $this->request->params['lang'];
$label = format(
    __('I accept the <a href="{}">terms of use</a>'),
    $this->Html->url(array("controller"=>"pages", "action"=>"terms_of_use", "#"=>$lang))
);
?>
<h2><? echo __('Register'); ?></h2>

<div layout="row" layout-align="center center">
    <md-input-container class="md-icon-float md-icon-left md-block" flex>
        <label for="registrationUsername"><?php echo __('Username'); ?></label>
        <md-icon>person</md-icon>
        <?php
        echo $this->Form->input(
            'username',
            array(
                'label' => '',
                'id' => 'registrationUsername',
                'ng-model' => 'user.username',
                'required' => '',
                'minlength' => 2,
                'maxlength' => 20,
                'ng-pattern' => '/^\w{2,20}$/',
                'unique-username' => '',
                'ng-init' => "user.username = '$username'"
            )
        );
        ?>
        <div ng-messages="registrationForm['data[User][username]'].$error">
            <div ng-message="required">
                <? echo __('Field required') ?>
            </div>
            <div ng-message="minlength">
                <? echo __('Username must be at least two characters long') ?>
            </div>
            <div ng-message="pattern">
                <? echo __('Username can only contain letters, numbers, or underscore') ?>
            </div>
            <div ng-message="uniqueUsername">
                <? echo __('Username already taken.') ?>
            </div>
        </div>
    </md-input-container>
    <md-input-container class="registration-loader">
        <md-progress-circular md-diameter="16"
                              ng-if="registrationForm['data[User][username]'].$pending">
        </md-progress-circular>
    </md-input-container>
</div>

<div layout="row">
    <md-input-container class="md-icon-float md-icon-left md-block" flex>
        <label for="registrationPassword"><?php echo __('Password'); ?></label>
        <md-icon>lock</md-icon>
        <?php
        echo $this->Form->input(
            'password',
            array(
                'label' => '',
                'id' => 'registrationPassword',
                'ng-model' => 'user.password',
                'required' => '',
                'minlength' => 6,
            )
        );
        ?>
        <div ng-messages="registrationForm['data[User][password]'].$error">
            <div ng-message="required">
                <? echo __('Field required') ?>
            </div>
            <div ng-message="minlength">
                <? echo __('Password must be at least 6 characters long') ?>
            </div>
        </div>
    </md-input-container>
    <md-input-container>
        <md-button class="md-icon-button"
                   ng-click="ctrl.togglePassword()"
                   aria-label="<? echo __('unmask password') ?>"
                   tabindex="-1">
            <md-icon ng-if="ctrl.isPasswordVisible">visibility</md-icon>
            <md-icon ng-if="!ctrl.isPasswordVisible">visibility_off</md-icon>
            <md-tooltip ng-if="!ctrl.isPasswordVisible">
                <? echo __('unmask password') ?>
            </md-tooltip>
        </md-button>
    </md-input-container>
</div>

<div layout="row" layout-align="center center">
    <md-input-container class="md-icon-float md-icon-left md-block" flex>
        <label for="registrationEmail"><?php echo __('Email address'); ?></label>
        <md-icon>email</md-icon>
        <?php
        $pattern = '/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/';
        echo $this->Form->input(
            'email',
            array(
                'label' => '',
                'id' => 'registrationEmail',
                'class' => 'registrationField',
                'ng-model' => 'user.email',
                'required' => '',
                'ng-pattern' => $pattern,
                'unique-email' => '',
                'ng-init' => "user.email = '$email'"
            )
        );
        ?>
        <div ng-messages="registrationForm['data[User][email]'].$error">
            <div ng-message="required">
                <? echo __('Field required') ?>
            </div>
            <div ng-message="pattern">
                <? echo __('Invalid email address') ?>
            </div>
            <div ng-message="uniqueEmail">
                <? echo __('Email address already used.') ?>
            </div>
        </div>
    </md-input-container>
    <md-input-container class="registration-loader">
        <md-progress-circular md-diameter="16"
                              ng-if="registrationForm['data[User][email]'].$pending">
        </md-progress-circular>
    </md-input-container>
</div>

<div id="native-language" layout="column">
    <div class="input" layout="row">
        <md-icon>language</md-icon>
        <label for="UserLanguage" flex><? echo __('Native language:'); ?></label>
        <?
        $languagesList = $this->Languages->languagesArrayWithNone(false);
        $language = $language ? $language : 'none';
        echo $this->Form->select(
            'language',
            $languagesList,
            array(
                'class' => 'language-selector',
                'empty' => false,
                'ng-model' => 'user.language',
                'ng-init' => "user.language = '$language'"
            ),
            false
        );
        ?>
    </div>
    <?
    echo $this->Html->div('hint',
        __(
            "If you don't find your native language in the list, ".
            "leave this as 'None'.", true
        )
    );
    ?>
</div>

<div id="human-check" layout="column">
    <div layout="row" layout-align="center start">
        <md-icon>verified_user</md-icon>
        <div class="title" flex>
            <? echo __('We need to make sure you are human.'); ?>
        </div>
    </div>

    <div class="instructions">
        <? echo __('What are the first five characters of your email address?'); ?>
    </div>

    <md-input-container class="md-block">
        <?
        echo $this->Form->input(
            'quiz',
            array(
                'label' => __('Answer'),
                'ng-model' => 'registration.quizAnswer',
                'required' => '',
            )
        );

        echo $this->Html->div('hint',
            __('For instance, if your email address is a.b.cd@example.com, type a.b.c into the box.')
        );
        ?>
    </md-input-container>
</div>

<md-input-container class="md-block">
    <md-checkbox ng-model="registration.termsOfUse" class="md-primary">
        <?= $label ?>
    </md-checkbox>
</md-input-container>

<div layout="column">
    <md-button type="submit" class="md-raised md-primary">
        <?php echo __('Register'); ?>
    </md-button>
</div>


<?php
echo $this->Form->end();
$this->Security->disableCSRFProtection();
?>
