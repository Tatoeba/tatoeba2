<?php
use Cake\Core\Configure;

$audioBaseUrl = Configure::read('Recordings.url');
?>

<md-button class="md-icon-button audioAvailable" ng-href="<?= $audioBaseUrl ?>{{<?= $angularVar ?>.lang}}/{{<?= $angularVar ?>.id}}.mp3"
           ng-click="vm.playAudio($event)" ng-if="<?= $angularVar ?>.audios && <?= $angularVar ?>.audios.length > 0">
    <md-icon>volume_up</md-icon>
    <md-tooltip md-direction="top" ng-if="!vm.getAudioAuthor(<?= $angularVar ?>)">
        <?= __('Play audio'); ?>
    </md-tooltip>
    <md-tooltip md-direction="top" ng-if="vm.getAudioAuthor(<?= $angularVar ?>)">
        <?= format(
            __('Play audio recorded by {author}', true),
            ['author' => '{{vm.getAudioAuthor('.$angularVar.')}}']
        ); ?>
    </md-tooltip>
</md-button>

<md-button class="md-icon-button audioUnavailable" target="_blank" ng-if="!<?= $angularVar ?>.audios || <?= $angularVar ?>.audios.length === 0"
            href="https://en.wiki.tatoeba.org/articles/show/contribute-audio">
    <md-icon>volume_off</md-icon>
    <md-tooltip md-direction="top">
        <?= __('No audio for this sentence. Click to learn how to contribute.') ?>
    </md-tooltip>
</md-button>