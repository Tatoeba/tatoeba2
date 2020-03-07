<?php 
use App\Model\CurrentUser;

if (CurrentUser::getSetting('use_new_design')) { ?>
<md-button toggle-all-menus class="md-icon-button" ng-click="menu.toggleAll()" ng-cloak>
    <md-icon ng-if="!menu.expanded">unfold_more</md-icon>
    <md-tooltip ng-if="!menu.expanded"><?= __('Expand menu for all sentences') ?></md-tooltip>

    <md-icon ng-if="menu.expanded">unfold_less</md-icon>
    <md-tooltip ng-if="menu.expanded"><?= __('Collapse menu for all sentences') ?></md-tooltip>
</md-button>
<?php } ?>