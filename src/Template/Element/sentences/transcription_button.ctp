<div class="menu-wrapper" ng-if="vm.hasHiddenTranscriptions">
    <div class="menu" layout="row" layout-align="space-between center">
        <md-button ng-if="!vm.isMenuExpanded" class="md-icon-button" ng-click="vm.toggleTranscriptions()">
            <md-icon>unfold_more</md-icon>
            <md-tooltip><?= __('Show unreviewed transcriptions') ?></md-tooltip>
        </md-button>

        <md-button ng-if="vm.isMenuExpanded" class="md-icon-button" ng-click="vm.toggleTranscriptions()">
            <md-icon>unfold_less</md-icon>
            <md-tooltip><?= __('Hide unreviewed transcriptions') ?></md-tooltip>
        </md-button>
    </div>
</div>