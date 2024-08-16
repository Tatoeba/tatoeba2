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

$audios = $sentence->audios;
if (CurrentUser::isAdmin() && isset($sentence->disabled_audios)) {
    /* Combine enabled and disabled audios */
    $audios = array_merge($audios, $sentence->disabled_audios);
    /* Keep audios sorted by id */
    usort($audios, function ($a, $b) { return $a->id - $b->id; });
}
/* Export "enabled", "created_ago", and "modified_ago" properties to this json only */
$audios = array_map(
    function ($a) {
        $new_a = clone $a;
        $new_a->setVirtual(['enabled'], true);
        $new_a->created_ago = $this->Date->ago($new_a->created);
        $new_a->modified_ago = $this->Date->ago($new_a->modified);
        return $new_a;
    },
    $audios
);

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
$confirmMessage = json_encode(__('The audio file will be lost. Are you sure?'));

// Prevent interpolation by AngularJS
$audioLicenses = str_replace('{{', '\{\{', $audioLicenses);
$audiosJson = str_replace('{{', '\{\{', $audiosJson);
$confirmMessage = str_replace('{{', '\{\{', $confirmMessage);

$this->Html->script('/js/sentences/audio-details.ctrl.js', ['block' => 'scriptBottom']);
$this->Html->script('/js/directives/audio-button.dir.js', ['block' => 'scriptBottom']);
$this->AngularTemplate->addTemplate(
    $this->element('sentence_buttons/audio'),
    'audio-button-template'
);
?>
<div ng-controller="AudioDetailsController as vm"
     ng-init="vm.init(<?= h($audiosJson) ?>, <?= h($audioLicenses) ?>)"
     ng-cloak>
<div ng-if="vm.audios.length > 0"
     layout="column"
     class="section audio md-whiteframe-1dp">
    <?php /* @translators: header text in sentence page */ ?>
    <h2><?= __xn('header', 'Audio', 'Audio', count($audios)) ?></h2>

    <div ng-repeat="audio in vm.audios" ng-class="{'disabled': !audio.enabled}">
        <h3>
            <audio-button class="audio-button" audios="[audio]"></audio-button>
            <span class="audio-author">
                <?= format(__('by {username}'), [
                    'username' => '<a ng-href="{{audio.attribution_url}}">{{audio.author}}</a>'
                ]) ?>
            </span>
        </h3>

        <div class="audio-details" layout="column">
            <div class="license"><?= format(__('License: {license}'), ['license' => $licenseTemplate]) ?></div>
            <div class="timestamp">
                <?php /* @translators: header text of the date an audio recording was added */ ?>
                <div><?= __('Added') ?></div>
                <div ng-bind-html="audio.created_ago" class="since"></div>
            </div>

            <div ng-if="audio.created_ago !== audio.modified_ago" class="timestamp">
                <?php /* @translators: header text of the date an audio recording was last modified */ ?>
                <div><?= __('Last modified') ?></div>
                <div ng-bind-html="audio.modified_ago" class="since"></div>
            </div>

            <?php if (CurrentUser::isAdmin()): ?>
                <md-checkbox
                    ng-model="audio.enabled"
                    class="md-primary">
                    <?= __('Is enabled') ?>
                </md-checkbox>
                <md-input-container>
                    <?= $this->Form->control('author', [
                        'label' => __d('admin', 'Audio author'),
                        'ng-model' => 'audio.author',
                    ]);
                    ?>
                </md-input-container>
                <md-button type="submit" class="md-primary md-raised" ng-click="vm.saveAudio(audio)">
                    <?php /* @translators: audio save button on sentence page (verb) */ ?>
                    <?= __d('admin', 'Save') ?>
                </md-button>
                <md-button type="submit" ng-hide="audio.enabled" class="md-warn md-raised" ng-click="vm.deleteAudio(audio, <?= h($confirmMessage) ?>)">
                    <?php /* @translators: audio deletion button on sentence page (verb) */ ?>
                    <?= __d('admin', 'Delete') ?>
                </md-button>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>
