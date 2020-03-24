<div class="menu-wrapper" sentence-menu flex="{{vm.isMenuExpanded ? '100' : 'none'}}" 
     ng-init="vm.initMenu(<?= (int)$expanded ?>, vm.sentence.permissions)">
    <div class="menu" layout="row" layout-align="space-between center">
        <div>
            <md-button class="md-icon-button" ng-click="vm.translate(vm.sentence.id)" ng-disabled="vm.sentence.correctness === -1">
                <md-icon>translate</md-icon>
                <md-tooltip><?= __('Translate') ?></md-tooltip>
            </md-button>

            <md-button ng-if="vm.menu.canEdit || vm.menu.canTranscribe" class="md-icon-button" ng-click="vm.edit()">
                <md-icon>edit</md-icon>
                <md-tooltip><?= __('Edit') ?></md-tooltip>
            </md-button>
            
            <md-button class="md-icon-button" ng-click="vm.list()">
                <md-icon>list</md-icon>
                <md-tooltip><?= __('Add sentence to list') ?></md-tooltip>
            </md-button>

            <md-button class="md-icon-button" ng-if="vm.isMenuExpanded" ng-click="vm.favorite()">
                <md-icon>{{vm.sentence.is_favorite ? 'favorite' : 'favorite_border'}}</md-icon>
                <md-tooltip ng-if="!vm.sentence.is_favorite"><?= __('Add to favorites') ?></md-tooltip>
                <md-tooltip ng-if="vm.sentence.is_favorite"><?= __('Remove from favorites') ?></md-tooltip>
            </md-button>

            <md-button class="md-icon-button" ng-if="vm.isMenuExpanded && vm.menu.canLink" ng-disabled="true">
                <md-icon>link</md-icon>
                <md-tooltip><?= __('This feature is not yet implemented.') ?></md-tooltip>
            </md-button>

            <md-button class="md-icon-button" ng-if="vm.isMenuExpanded && vm.menu.canDelete"
                       onclick="return confirm('<?= __('Are you sure?') ?>');"
                       ng-href="<?= $this->Url->build(['controller' => 'sentences', 'action' => 'delete']) ?>/{{vm.sentence.id}}">
                <md-icon>delete</md-icon>
                <md-tooltip><?= __('Delete') ?></md-tooltip>
            </md-button>
        </div>

        <div ng-if="vm.isMenuExpanded && vm.menu.canReview">
            <md-button class="md-icon-button" ng-disabled="true">
                <md-icon>check_circle</md-icon>
                <md-tooltip><?= __('This feature is not yet implemented.') ?></md-tooltip>
            </md-button>
            <md-button class="md-icon-button" ng-disabled="true">
                <md-icon>help</md-icon>
                <md-tooltip><?= __('This feature is not yet implemented.') ?></md-tooltip>
            </md-button>
            <md-button class="md-icon-button" ng-disabled="true">
                <md-icon>error</md-icon>
                <md-tooltip><?= __('This feature is not yet implemented.') ?></md-tooltip>
            </md-button>
        </div>

        <div>
            <md-button class="md-icon-button" ng-if="vm.isMenuExpanded && vm.menu.canAdopt" ng-click="vm.adopt()">
                <md-icon>{{vm.sentence.is_owned_by_current_user ? 'person' : 'person_outline'}}</md-icon>
                <md-tooltip ng-if="!vm.sentence.is_owned_by_current_user"><?= __('Click to adopt') ?></md-tooltip>
                <md-tooltip ng-if="vm.sentence.is_owned_by_current_user"><?= __('Click to unadopt') ?></md-tooltip>
            </md-button>

            <md-button ng-if="!vm.isMenuExpanded" class="md-icon-button" ng-click="vm.toggleMenu()">
                <md-icon>unfold_more</md-icon>
                <md-tooltip><?= __('Expand menu') ?></md-tooltip>
            </md-button>

            <md-button ng-if="vm.isMenuExpanded" class="md-icon-button" ng-click="vm.toggleMenu()">
                <md-icon>unfold_less</md-icon>
                <md-tooltip><?= __('Collapse menu') ?></md-tooltip>
            </md-button>
        </div>
    </div>
</div>
