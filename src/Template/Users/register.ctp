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
 * @link     https://tatoeba.org
 */
use Cake\Core\Configure;

/**
 * Page to register.
 *
 * @category Users
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

/* @translators: title of the Register page (verb) */
$this->set('title_for_layout', $this->Pages->formatTitle(__('Register')));

$this->Html->script('users/register.ctrl.js', ['block' => 'scriptBottom']);

$this->Security->enableCSRFProtection();
echo $this->Form->create($user, array(
    'id' => 'UserRegisterForm',
    'name' => 'registrationForm',
    'url' => array('action' => 'register'),
    'class' => 'md-whiteframe-1dp',
    'ng-controller' => 'UsersRegisterController as ctrl',
    'ng-submit' => 'registrationForm.$valid || $event.preventDefault()',
));

$label = format(
    __('I accept the <a href="{}">terms of use</a>'),
    $this->Url->build(array("controller"=>"pages", "action"=>"terms_of_use"))
);
?>
<h2><?= __('Register'); ?></h2>

<div ng-cloak>
<div layout="row" layout-align="center center">
    <md-input-container class="md-icon-float md-icon-left md-block" flex>
        <label for="registrationUsername"><?php echo __('Username'); ?></label>
        <md-icon>person</md-icon>
        <?php
        echo $this->Form->control(
            'username',
            array(
                'label' => '',
                'id' => 'registrationUsername',
                'ng-model' => 'user.username',
                'required' => true,
                'server-error' => $this->Form->isFieldError('username'),
                'minlength' => 2,
                'maxlength' => 20,
                'ng-pattern' => '/^\w{2,20}$/',
                'unique-username' => '',
                'ng-trim' => 'false',
                'value' => $this->Form->getSourceValue('username'),
                'error' => false,
            )
        );
        ?>
        <div ng-messages="registrationForm['username'].$error">
            <?php if ($this->Form->isFieldError('username')): ?>
                <div ng-message="serverError">
                    <?= $this->Form->error('username') ?>
                </div>
            <?php endif; ?>
            <div ng-message="required">
                <?= __('Field required') ?>
            </div>
            <div ng-message="minlength">
                <?= __('Username must be at least two characters long') ?>
            </div>
            <div ng-message="pattern">
                <?= __('Username can only contain letters, numbers, or underscore') ?>
            </div>
            <div ng-message="uniqueUsername">
                <?= __('Username already taken.') ?>
            </div>
        </div>
    </md-input-container>
    <md-input-container class="registration-loader">
        <md-progress-circular md-diameter="16"
                              ng-if="registrationForm['username'].$pending">
        </md-progress-circular>
    </md-input-container>
</div>

<div layout="row">
    <md-input-container class="md-icon-float md-icon-left md-block" flex>
        <label for="registrationPassword"><?php echo __('Password'); ?></label>
        <md-icon>lock</md-icon>
        <?php
        echo $this->Form->control(
            'password',
            array(
                'label' => '',
                'id' => 'registrationPassword',
                'ng-model' => 'user.password',
                'required' => true,
                'minlength' => 6,
                'server-error' => $this->Form->isFieldError('password'),
                'value' => '',
                'error' => false,
            )
        );
        ?>
        <div ng-messages="registrationForm['password'].$error">
            <?php if ($this->Form->isFieldError('password')): ?>
                <div ng-message="serverError">
                    <?= $this->Form->error('password') ?>
                </div>
            <?php endif; ?>
            <div ng-message="required">
                <?= __('Field required') ?>
            </div>
            <div ng-message="minlength">
                <?= __('Password must be at least 6 characters long') ?>
            </div>
        </div>
    </md-input-container>
    <md-input-container>
        <md-button class="md-icon-button"
                   ng-click="ctrl.togglePassword()"
                   aria-label="<?= __('unmask password') ?>"
                   tabindex="-1">
            <md-icon ng-if="ctrl.isPasswordVisible">visibility</md-icon>
            <md-icon ng-if="!ctrl.isPasswordVisible">visibility_off</md-icon>
            <md-tooltip ng-if="!ctrl.isPasswordVisible">
                <?= __('Unmask password') ?>
            </md-tooltip>
        </md-button>
    </md-input-container>
</div>

<div layout="row" layout-align="center center">
    <md-input-container class="md-icon-float md-icon-left md-block" flex>
        <label for="registrationEmail"><?php echo __('Email address'); ?></label>
        <md-icon>email</md-icon>
        <?php
        echo $this->Form->control(
            'email',
            array(
                'label' => '',
                'id' => 'registrationEmail',
                'class' => 'registrationField',
                'ng-model' => 'user.email',
                'server-error' => $this->Form->isFieldError('email'),
                'required' => true,
                'unique-email' => '',
                'value' => $this->Form->getSourceValue('email'),
                'error' => false,
            )
        );
        ?>
        <div ng-messages="registrationForm['email'].$error">
            <?php if ($this->Form->isFieldError('email')): ?>
                <div ng-message="serverError">
                    <?= $this->Form->error('email') ?>
                </div>
            <?php endif; ?>
            <div ng-message="required">
                <?= __('Field required') ?>
            </div>
            <div ng-message="email">
                <?= __('Invalid email address') ?>
            </div>
            <div ng-message="uniqueEmail">
                <?= __('Email address already used.') ?>
            </div>
        </div>
    </md-input-container>
    <md-input-container class="registration-loader">
        <md-progress-circular md-diameter="16"
                              ng-if="registrationForm['email'].$pending">
        </md-progress-circular>
    </md-input-container>
</div>

<div layout="row" layout-align="center center" ng-show="confirm">
    <md-input-container class="md-icon-float md-icon-left md-block" flex>
        <md-icon>email</md-icon>
        <?php
        echo $this->Form->control(
            'confirm',
            array(
/* @translators: This is a simple countermeasure against spam bots trying to
   automatically register on Tatoeba. There is an empty field on the
   registration page, but you won't see it because it is purposely hidden.
   As a result, a regular user will never fill out this field. Spam bots on
   the other hand don't know which part of the page is visible or not and are
   likely to carelessly fill all the fields. If anything is written in that
   hidden field, registration won't work. This strategy is called a honeypot.
   However, visually-impaired people browsing the page using a screen reader
   may notice the presence of the hidden field. To help them, we leave a hidden
   text explaining what to do with that field. This text is what you have to
   translate here. */
                'label' => __('Leave this blank'),
                'type' => 'email',
                'required' => false,
            )
        );
        ?>
    </md-input-container>
</div>

<div id="native-language" layout="column">
    <div class="input" layout="row">
        <md-icon>language</md-icon>
        <label for="UserLanguage" flex><?= __('Native language:'); ?></label>
        <?php
        $languagesList = $this->Languages->onlyLanguagesArray();
        echo $this->element(
            'language_dropdown',
            array(
                'name' => 'language',
                'languages' => $languagesList,
                /* @translators: native language dropdown placeholder in Register page */
                'placeholder' => __('None'),
                'initialSelection' => $language,
                'onSelectedLanguageChange' => 'user.language = language.code',
            )
        );
        ?>
    </div>
    <?php
    echo $this->Html->div('hint',
        __(
            "If you don't find your native language in the list, ".
            "leave this as 'None'.", true
        )
    );
    ?>
</div>

<md-input-container class="md-block">
    <md-checkbox ng-model="registration.termsOfUse"
                 ng-change="registrationForm.acceptation_terms_of_use.$setDirty()"
                 ng-init="registration.termsOfUse = 0"
                 class="md-primary">
        <?= $label ?>
    </md-checkbox>
    <?= $this->Form->checkbox(
        'acceptation_terms_of_use',
        array(
            'class' => 'ng-hide',
            'ng-model' => 'registration.termsOfUse',
            'server-error' => !($this->Form->getSourceValue('acceptation_terms_of_use') ?? true),
        )
    ); ?>
    <div ng-messages="registrationForm.acceptation_terms_of_use.$error">
        <div ng-message="serverError">
            <?= __('You did not accept the terms of use.') ?>
        </div>
    </div>
</md-input-container>

<div layout="column">
    <md-button type="submit" class="md-raised md-primary">
        <?php /* @translators: button to submit registration form (verb) */ ?>
        <?php echo __('Register'); ?>
    </md-button>
</div>
</div>

<?php
echo $this->Form->end();
$this->Security->disableCSRFProtection();
?>
