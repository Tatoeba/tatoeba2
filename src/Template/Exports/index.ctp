<? $this->Html->script('exports/exports.ctrl.js', ['block' => 'scriptBottom']); ?>

<div class="section"
     md-whiteframe="1"
     ng-controller="exportsCtrl"
     ng-init="init(<?= h(json_encode($exports)); ?>)">

<h2><?= __('My exports') ?></h2>
<p ng-show="!exports.length"><?= __("There are no exports.") ?></p>
<md-list-item ng-cloak
              ng-show="exports.length"
              ng-repeat="export in exports">
    <p>{{export.name}}</p>
    <md-button ng-show="export.url" class="md-raised md-primary"><?= __x('button', 'Download') ?></md-button>
    <span ng-show="!export.url"><?= __('Export in progress') ?></span>
</md-list-item>
</div>
