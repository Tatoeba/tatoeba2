<?php
use Cake\Core\Configure;

$sentenceBaseUrl = $this->Url->build([
    'controller' => 'sentences',
    'action' => 'show',
]);
?>
<div ng-repeat="translation in <?= $translations ?>" layout="row" layout-align="start center"
     class="translation" ng-class="{'not-reliable' : translation.correctness === -1}">
    
    <md-icon class="chevron">chevron_right</md-icon>

    <div class="lang">
        <language-icon lang="translation.lang" title="translation.langName"></language-icon>
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

    <?= $this->element('sentence_buttons/audio', ['angularVar' => 'translation']); ?>
    
    <md-button class="md-icon-button" href="<?= $sentenceBaseUrl ?>/{{translation.id}}">
        <md-icon>info</md-icon>
    </md-button>
</div>