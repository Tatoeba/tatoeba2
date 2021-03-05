<?php
$this->Html->script('exports/exports.ctrl.js', ['block' => 'scriptBottom']);
$exports = h(str_replace('{{', '\{\{', json_encode($exports)));
$languagesList = $this->Languages->onlyLanguagesArray();
?>

<section ng-cloak
         ng-controller="exportsCtrl"
         ng-init="init(<?= $exports ?>)">

<div id="new-export" class="md-whiteframe-1dp">
  <md-toolbar class="md-hue-2">
    <div class="md-toolbar-tools">
      <md-button ng-show="new_export" ng-click="new_export = ''" class="md-icon-button">
        <md-icon>arrow_back</md-icon>
      </md-button>
      <h2 ng-show="!new_export"><?= __('Sentences export') ?></h2>
      <h2 ng-show="new_export == 'list'"><?= __('List') ?></h2>
      <h2 ng-show="new_export == 'pairs'"><?= __('Language pair') ?></h2>
    </div>
  </md-toolbar>

  <md-content layout-margin>
    <div ng-show="!new_export">
      <p>Use this tool to download sentences from Tatoeba into a file.</p>

      <md-button class="export-card md-raised" ng-click="new_export = 'list'">
        <div>
          <md-icon>list</md-icon>
          <strong><?= __('List') ?></strong>
        </div>
        <small><?= __('Download all sentences from a list') ?></small>
      </md-button>

      <md-button class="export-card md-raised" ng-click="new_export = 'pairs'">
        <div>
          <md-icon>translate</md-icon>
          <strong><?= __('Language pair') ?></strong>
        </div>
        <small><?= __('Download all sentences in language A along with all translations in a language B') ?></small>
      </md-button>
    </div>

    <div ng-show="new_export == 'list'">
      <div layout="row" layout-align="center">
        <label for="listToExport" flex="33"><?= __('Choose a list:') ?></label>
        <div flex>
        <?php
          $this->loadHelper('Lists');
          $listOptions = $this->Lists->listsAsSelectable($searchableLists);
          echo $this->Form->input('list', [
            'id' => 'listToExport',
            'ng-model' => 'selectedList',
            'options' => $this->safeForAngular($listOptions),
            'label' => false,
          ]);
        ?>
        </div>
      </div>
      <md-button ng-click="addExport('list', ['id', 'lang', 'text'], {'list_id': selectedList})"
                 ng-disabled="!selectedList"
                 class="md-raised md-primary">
        <?= __('Export list') ?>
      </md-button>
    </div>

    <div ng-show="new_export == 'pairs'">
      <div layout="column" layout-gt-sm="row">
        <div layout="row" layout-align="start center" flex>
        <?php
          echo $this->Html->tag(
            'label',
            __('Sentence language:'),
            ['for' => 'from']
          );
          echo $this->element(
            'language_dropdown',
            [
              'name' => 'from',
              'languages' => $languagesList,
              'selectedLanguage' => 'selectedFrom',
            ]
          );
        ?>
        </div>

        <div layout="row" layout-align="start center" layout-align-gt-sm="end center" flex>
        <?php
          echo $this->Html->tag(
            'label',
            __('Translation language:'),
            ['for' => 'to']
          );
          echo $this->element(
            'language_dropdown',
            [
              'name' => 'to',
              'languages' => $languagesList,
              'selectedLanguage' => 'selectedTo',
            ]
          );
        ?>
        </div>
      </div>

      <md-button ng-click="addExport('pairs', ['id', 'text', 'trans_id', 'trans_text'], {'from': selectedFrom.code, 'to': selectedTo.code})"
                 ng-disabled="!selectedFrom.code || !selectedTo.code"
                 class="md-raised md-primary">
          <?= __('Export language pair') ?>
      </md-button>
    </div>
  </md-content>
</div>

<div id="exports-list"
     class="md-whiteframe-1dp"
     ng-show="exports.length">
  <md-toolbar class="md-hue-2">
    <div class="md-toolbar-tools">
      <h2 flex ng-show="!is_showing_all_exports"><?= __('Your latest exports') ?></h2>
      <h2 flex ng-show="is_showing_all_exports" ><?= __('All your exports') ?></h2>
      <md-button ng-show="!is_showing_all_exports && exports.length > MAX_LATEST_EXPORTS"
                 ng-click="is_showing_all_exports = true">
        <md-icon>expand_more</md-icon>
        <?php /* @translators: button in exports list toolbar in exports page,
                 to show all exports (as opposed to the latests only). */ ?>
        <?= __x('exports', 'Show all') ?>
      </md-button>
    </div>
  </md-toolbar>

  <md-content>
    <md-list-item ng-repeat="export in exports | orderBy: 'generated':true | limitTo: (is_showing_all_exports ? undefined : MAX_LATEST_EXPORTS)">
      <p>{{export.name}}</p>
      <span ng-show="export.status == 'online' && export.generated">{{export.generated | date:'yyyy-MM-dd'}}</span>
      <md-button class="md-raised md-primary uncropped-md-button"
                 ng-href="/exports/download/{{export.id}}/{{export.pretty_filename | urlEncode}}"
                 ng-show="export.status == 'online'">
        <md-icon>get_app</md-icon>
        <?php /* @translators: button to download a list (verb) */ ?>
        <span hide="" show-gt-sm=""><?= __x('button', 'Download') ?></span>
      </md-button>
      <div ng-show="export.status == 'queued'" layout="row" layout-align="none center">
        <md-progress-circular md-mode="indeterminate" md-diameter="16"></md-progress-circular>
        <span><?= __('Export in progress') ?></span>
      </div>
      <span ng-show="export.status == 'failed'">
        <md-icon>error</md-icon>
        <?= __('Export failed') ?>
      </span>
    </md-list-item>
  </md-content>
</div>

</div>

</div>
