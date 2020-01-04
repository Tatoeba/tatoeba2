<?php
use Cake\Core\Configure;

$sentenceBaseUrl = $this->Url->build([
    'controller' => 'sentences',
    'action' => 'show',
]);
$audioBaseUrl = Configure::read('Recordings.url');
?>
<div ng-repeat="translation in <?= $translations ?>" layout="row" layout-align="start center"
     class="translation" ng-class="{'not-reliable' : translation.correctness === -1}">
    
    <md-icon class="chevron">chevron_right</md-icon>

    <div class="lang">
        <img class="language-icon" src="/img/flags/{{translation.lang ? translation.lang : 'unknown'}}.svg" />
    </div>

    <div class="text" dir="{{translation.dir}}" flex>
        {{translation.text}}
    </div>
    
    <div ng-if="translation.correctness === -1">
        <md-icon class="md-warn" >warning</md-icon>
        <md-tooltip md-direction="top">
            <?= __('This sentence is not reliable.') ?>
        </md-tooltip>
    </div>

    <md-button class="md-icon-button" ng-if="translation.editable" ng-click="vm.editTranslation(translation)">
        <md-icon>edit</md-icon>
        <md-tooltip>
            <?= __('Edit this translation'); ?>
        </md-tooltip>
    </md-button>

    <md-button class="md-icon-button audioAvailable" href="<?= $audioBaseUrl ?>{{translation.lang}}/{{translation.id}}.mp3"
                ng-click="vm.playAudio($event)" ng-if="translation.audios && translation.audios.length > 0">
        <md-icon>volume_up</md-icon>
        <md-tooltip md-direction="top" ng-if="!vm.getAudioAuthor(translation)">
            <?= __('Play audio'); ?>
        </md-tooltip>
        <md-tooltip md-direction="top" ng-if="vm.getAudioAuthor(translation)">
            <?= format(
                __('Play audio recorded by {author}', true),
                ['author' => '{{vm.getAudioAuthor(translation)}}']
            ); ?>
        </md-tooltip>
    </md-button>

    <md-button class="md-icon-button audioUnavailable" target="_blank" ng-if="!translation.audios || translation.audios.length === 0"
                href="https://en.wiki.tatoeba.org/articles/show/contribute-audio">
        <md-icon>volume_off</md-icon>
        <md-tooltip md-direction="top">
            <?= __('No audio for this sentence. Click to learn how to contribute.') ?>
        </md-tooltip>
    </md-button>
    
    <md-button class="md-icon-button" href="<?= $sentenceBaseUrl ?>/{{translation.id}}">
        <md-icon>info</md-icon>
    </md-button>
</div>