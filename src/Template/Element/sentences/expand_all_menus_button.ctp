<?php 
use App\Model\CurrentUser;

if (CurrentUser::getSetting('use_new_design')) { ?>
<span toggle-all-menus ng-cloak>
    <md-button ng-if="!menu.expanded" class="md-icon-button" ng-click="menu.toggleAll()">
        <md-icon>unfold_more</md-icon>
        <md-tooltip><?= __('Expand menu for all sentences') ?></md-tooltip>
    </md-button>

    <md-button ng-if="menu.expanded" class="md-icon-button" ng-click="menu.toggleAll()">
        <md-icon>unfold_less</md-icon>
        <md-tooltip><?= __('Collapse menu for all sentences') ?></md-tooltip>
    </md-button>
</span>
<?php } ?>