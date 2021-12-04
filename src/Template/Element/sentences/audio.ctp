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
 * @author   HO Ngoc Phuong Trang <trang@tatoeba.org>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
use App\Model\CurrentUser;
use App\Lib\Licenses;

$hasaudio = count($audios) > 0;
$shouldDisplayBlock = $hasaudio || CurrentUser::isAdmin();
if (!$shouldDisplayBlock) {
    return;
}

$licenseTemplate = <<<EOT
<a ng-if="vm.getLicenseLink(audio.license)" ng-href="{{vm.getLicenseLink(audio.license)}}">
  {{vm.getLicenseName(audio.license)}}
</a>
<span ng-if="!vm.getLicenseLink(audio.license)">
  {{vm.getLicenseName(audio.license)}}
</span>
EOT;

$audioLicenses = json_encode(Licenses::getAudioLicenses());
$audiosJson = json_encode($audios);

// Prevent interpolation by AngularJS
$audioLicenses = str_replace('{{', '\{\{', $audioLicenses);
$audiosJson = str_replace('{{', '\{\{', $audiosJson);

$this->Html->script('/js/sentences/audio-details.ctrl.js', ['block' => 'scriptBottom']);
?>
<div ng-controller="AudioDetailsController as vm"
     ng-init="vm.init(<?= h($audiosJson) ?>, <?= h($audioLicenses) ?>)"
     layout="column" ng-cloak
     class="section audio md-whiteframe-1dp">
    <?php /* @translators: header text in sentence page */ ?>
    <h2><?= __n('Audio', 'Audio', count($audios)) ?></h2>

    <div ng-repeat="audio in vm.audios" ng-class="{'disabled': audio.enabled != '1'}">
        <h3>
            <audio-button include-disabled="true" audios="[audio]"></audio-button>
            <span>
                <?= format(__('by {username}'), [
                    'username' => '<a ng-href="{{audio.attribution_url}}">{{audio.author}}</a>'
                ]) ?>
            </span>
        </h3>

        <div class="audio-details">
            <div class="license"><?= format(__('License: {license}'), ['license' => $licenseTemplate]) ?></div>

            <?php if (CurrentUser::isAdmin()): ?>
                <md-checkbox
                    ng-false-value="'0'"
                    ng-true-value="'1'"
                    ng-model="audio.enabled"
                    class="md-primary">
                    <?= __('Is enabled') ?>
                </md-checkbox>
                <md-input-container class="md-button-right">
                    <?= $this->Form->control('author', [
                        'label' => __d('admin', 'Audio author'),
                        'ng-model' => 'audio.author',
                    ]);
                    ?>
                </md-input-container>
            <?php endif; ?>
        </div>
    </div>
    <?php if (CurrentUser::isAdmin()): ?>
        <md-button type="submit" class="md-primary md-raised" ng-click="vm.editAudio()">
            <?= __d('admin', 'Save') ?>
        </md-button>
    <?php endif; ?>
</div>
