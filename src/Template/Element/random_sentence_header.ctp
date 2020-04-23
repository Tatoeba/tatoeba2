<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009-2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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

use App\Model\CurrentUser;

$this->Html->script('sentences/random.ctrl.js', ['block' => 'scriptBottom']);

$langArray = $this->Languages->languagesArrayAlone();
?>

<div ng-controller="RandomSentenceController as vm" ng-init="vm.init()">
<md-toolbar class="md-hue-2">
    <div class="md-toolbar-tools">
        <?php /* @translators: random sentence block header on the home page for members */ ?>
        <h2 flex><?= __('Random sentence') ?></h2>

        <span>
        <?php
        echo $this->Form->select('randomLangChoice', $langArray, [
            'id' => 'randomLangChoice',
            'class' => 'language-selector',
            'empty' => false,
            'ng-model' => 'vm.lang'
        ]);
        ?>
        </span>

        <md-button id="showRandom" ng-click="vm.showAnother(vm.lang)">
            <?= __('show another ') ?>
        </md-button>
    </div>
</md-toolbar>

<?php if (!CurrentUser::getSetting('use_new_design')) { ?>
<div class="md-whiteframe-1dp" layout-padding style="background: #fafafa" ng-if="vm.showNewDesignAnnouncement" ng-cloak>
    <p><?= __(
        'We are starting to roll out the new design for the sentences. '.
        'It is currently only used for the random sentence below. '.
        'You can enable it for other pages with the option '.
        '"Display sentences with the new design" in your Settings.'
    ) ?></p>
    <div layout="row" layout-align="end center">
        <md-button class="md-primary" href="/user/settings"><?= __('Go to settings') ?></md-button>
        <?php /* @translators: button to close the announcement about the new design (verb) */ ?>
        <md-button class="md-primary" ng-click="vm.hideAnnouncement()"><?= __('Close') ?></md-button>
    </div>
</div>
<?php } ?>

</div>