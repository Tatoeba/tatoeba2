<?php $this->Html->script('exports/exports.ctrl.js', ['block' => 'scriptBottom']); ?>

<div ng-controller="exportsCtrl"
     ng-init="init(<?= h(json_encode($exports)); ?>)">

<div class="section md-whiteframe-1dp">
<h2><?= __('My exports') ?></h2>
<p ng-cloak ng-show="!exports.length"><?= __("There are no exports.") ?></p>
<md-list-item ng-cloak
              ng-show="exports.length"
              ng-repeat="export in exports">
    <p>{{export.name}}</p>
    <span ng-show="export.status == 'online' && export.generated">{{export.generated | date:'yyyy-MM-dd'}}</span>
    <md-button class="md-raised md-primary"
               ng-href="/exports/download/{{export.id}}/{{export.pretty_filename | urlEncode}}"
               ng-show="export.status == 'online'"><?= __x('button', 'Download') ?></md-button>
    <span ng-show="export.status == 'queued'"><?= __('Export in progress') ?></span>
    <span ng-show="export.status == 'failed'">
        <md-icon>error</md-icon>
        <?= __('Export failed') ?>
    </span>
</md-list-item>
</div>


<div class="section md-whiteframe-1dp">
<h2><?= __('New export') ?></h2>
<?php
    $this->loadHelper('Lists');
    $listOptions = $this->Lists->listsAsSelectable($searchableLists);
    echo $this->Form->input('list', array(
        'id' => 'listToExport',
        'ng-model' => 'selectedList',
        'options' => $this->safeForAngular($listOptions),
    ));
?>
<md-button ng-click="addListExport()"
           ng-disabled="!selectedList"
           class="md-raised md-primary">
  <?= __('Export list') ?>
</md-button>
</div>

</div>
