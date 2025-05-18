<?php
$sentenceBaseUrl = $this->Url->build([
    'controller' => 'sentences',
    'action' => 'show',
]);

$responsiveNgClass = '{activated: translation.showActions, deactivated: !translation.showActions}';

$this->AngularTemplate->addTemplate(
    $this->element('sentence_buttons/audio'),
    'audio-button-template'
);
?>

<div class="indicator" ng-if="<?= $angularVar ?>.correctness === -1">
    <md-icon class="md-warn" >warning</md-icon>
    <md-tooltip md-direction="top">
        <?= __('This sentence is not reliable.') ?>
    </md-tooltip>
</div>

<md-button class="md-icon-button" ng-class="<?= $responsiveNgClass ?>" ngclipboard data-clipboard-text="{{<?= $angularVar ?>.text}}">
    <md-icon>content_copy</md-icon>
    <md-tooltip><?= __('Copy sentence') ?></md-tooltip>
</md-button>

<audio-button audios="<?= $angularVar ?>.audios"></audio-button>

<md-button class="md-icon-button" ng-class="<?= $responsiveNgClass ?>" ng-href="<?= $sentenceBaseUrl ?>/{{<?= $angularVar ?>.id}}">
    <md-icon>info</md-icon>
    <md-tooltip><?= __('Go to sentence page') ?></md-tooltip>
</md-button>