<? $this->Html->script('exports/exports.ctrl.js', ['block' => 'scriptBottom']); ?>

<div ng-controller="exportsCtrl"
     ng-init="init(<?= h(json_encode($exports)); ?>)">

<div class="section" md-whiteframe="1">
<h2><?= __('My exports') ?></h2>
<p ng-show="!exports.length"><?= __("There are no exports.") ?></p>
<md-list-item ng-cloak
              ng-show="exports.length"
              ng-repeat="export in exports">
    <p>{{export.name}}</p>
    <md-button class="md-raised md-primary"
               ng-href="{{export.url}}"
               ng-show="export.url"><?= __x('button', 'Download') ?></md-button>
    <span ng-show="!export.url"><?= __('Export in progress') ?></span>
</md-list-item>
</div>


<div class="section" md-whiteframe="1">
<h2><?= __('New export') ?></h2>
<?php
    $this->loadHelper('Lists');
    $listOptions = $this->Lists->listsAsSelectable($searchableLists);
    echo $this->Form->input('list', array(
        'id' => 'listToExport',
        'ng-model' => 'selectedList',
        'options' => $listOptions,
    ));
?>
<md-button ng-click="addListExport()"
           ng-disabled="!selectedList"
           class="md-raised md-primary">
  <?= __('Export list') ?>
</md-button>
</div>

</div>
