<?php
use Cake\Core\Configure;
use App\Model\CurrentUser;

$sentenceBaseUrl = $this->Url->build([
    'controller' => 'sentences',
    'action' => 'show',
]);
?>
<div ng-repeat="translation in <?= $translations ?>"
     class="translation" ng-class="{'not-reliable' : translation.correctness === -1}">
    
    <div layout="row" layout-align="start center" flex>
    <md-icon class="chevron">chevron_right</md-icon>

    <div class="lang">
        <language-icon lang="translation.lang" title="translation.lang_name"></language-icon>
    </div>

    <div class="text" dir="{{translation.dir}}" lang="{{translation.lang_tag}}" flex>
        {{translation.text}}
    </div>
    
    <div class="indicator" ng-if="translation.correctness === -1">
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

    <md-button class="md-icon-button" ngclipboard data-clipboard-text="{{translation.text}}">
        <md-icon>content_copy</md-icon>
        <md-tooltip><?= __('Copy sentence') ?></md-tooltip>
    </md-button>
    
    <?= $this->element('sentence_buttons/audio', ['angularVar' => 'translation']); ?>
    
    <md-button class="md-icon-button" href="<?= $sentenceBaseUrl ?>/{{translation.id}}">
        <md-icon>info</md-icon>
        <md-tooltip><?= __('Go to sentence page') ?></md-tooltip>
    </md-button>
    </div>

    <?= $this->element('sentences/transcriptions', ['sentenceVar' => 'translation']); ?>
</div>