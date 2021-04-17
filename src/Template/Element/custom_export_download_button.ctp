<div layout="row" layout-align="none center">
  <md-button ng-click="addExport(type, fields, params)"
             ng-disabled="ngDisabled || preparingDownload"
             class="md-raised md-primary uncropped-md-button">
    {{text}}
  </md-button>
  <md-progress-circular ng-if="preparingDownload" md-diameter="16" /></md-progress-circular>
  <div class="progress-info">
    <span ng-if="preparingDownload"><?= __('Preparing download, please wait.') ?></span>
    <span ng-if="export.status == 'failed'"><?= __('Failed to prepare download, please try again.') ?></span>
  </div>
</div>
