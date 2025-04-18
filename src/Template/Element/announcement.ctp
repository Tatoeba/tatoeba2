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
use App\Model\CurrentUser;
use App\Model\Entity\User;

$isDisplayingAnnouncement = false;

if (!CurrentUser::hasAcceptedNewTermsOfUse()) {
    $isDisplayingAnnouncement = true;
    $termsOfUseUrl = $this->Url->build([
        'controller' => 'pages', 
        'action' => 'terms_of_use'
    ]);
    $contactUrl = $this->Url->build([
        'controller' => 'pages', 
        'action' => 'contact'
    ]);
    echo $this->Form->create('Users', [
        'class' => 'announcement md-whiteframe-1dp',
        'url' => ['controller' => 'user', 'action' => 'accept_new_terms_of_use']
    ]);
    echo $this->Form->hidden('settings.new_terms_of_use', ['value' => User::TERMS_OF_USE_LATEST_VERSION]);
    ?>
    <p>
    <?= format(
        __('We have updated our <a href="{termsOfUse}">Terms of Use</a>. ' .
           'By closing this announcement, you agree with the new Terms of Use. ' .
           'If you have any question, feel free to <a href="{contact}">contact us</a>.'),
        ['termsOfUse' => $termsOfUseUrl, 'contact' => $contactUrl]
    ) ?>
    </p>
    <div layout="row" layout-align="end center">
        <?php /* @translators: button to accept new terms of use */ ?>
        <md-button type="submit" class="md-primary"><?= __('Accept and close') ?></md-button>
    </div>
    <?php
    echo $this->Form->end();
}

if ($this->Announcement->isDisplayed()) {
    $isDisplayingAnnouncement = true;
    ?>
    <div class="announcement md-whiteframe-1dp" info-banner ng-init="vm.init('hide_announcement')" ng-cloak>
        <p>
        Announcement text here.
        </p>
        <div layout="row" layout-align="end center">
            <?php /* @translators: button to close the blue announcement banner */ ?>
            <md-button class="md-primary" ng-click="vm.hideAnnouncement()"><?= __('Close') ?></md-button>
        </div>
    </div>
    <?php
}

if ($message = $this->Announcement->getMaintenanceMessage()) {
    if ($this->Announcement->isMaintenanceImminent()) {
        // Forcefully display alarming maintenance message
        echo $this->Html->div('maintenance', $this->Html->tag('span', $message));
    } else {
        // Display maintenance message as closeable banner
        $isDisplayingAnnouncement = true;
        ?>
        <div class="announcement md-whiteframe-1dp" info-banner ng-init="vm.init('hide_maintenance')" ng-cloak>
            <div layout="row">
                <md-icon>warning</md-icon>
                <p><?= h($message) ?></p>
            </div>
            <div layout="row" layout-align="end center">
                <?php /* @translators: button to close the blue announcement banner */ ?>
                <md-button class="md-primary" ng-click="vm.hideAnnouncement()"><?= __('Close') ?></md-button>
            </div>
        </div>
        <?php
    }
}

if (Configure::read('Tatoeba.devStylesheet')) {
    $isDisplayingAnnouncement = true;
    ?>
    <div class="announcement md-whiteframe-1dp" info-banner ng-init="vm.init('hide_dev_warning')" ng-cloak>
        <div layout="row">
            <md-icon>warning</md-icon>
            <p>
                <?= __(
                'Warning: this website is for testing purposes. '.
                'Everything you submit will be definitely lost.', true
                ); ?>
            </p>
        </div>
        <div layout="row" layout-align="end center">
            <?php /* @translators: button to close the blue announcement banner */ ?>
            <md-button class="md-primary" ng-click="vm.hideAnnouncement()"><?= __('Close') ?></md-button>
        </div>
    </div>
    <?php
}

if ($isDisplayingAnnouncement) {
    $this->Html->script(JS_PATH . 'directives/info-banner.dir.js', ['block' => 'scriptBottom']);
}
?>
