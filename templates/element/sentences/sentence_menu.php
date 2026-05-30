<div class="menu-wrapper" sentence-menu flex="{{vm.isMenuExpanded ? '100' : 'none'}}"
     ng-init="vm.initMenu(<?= (int)$expanded ?>, vm.sentence.permissions)">
    <div class="menu" layout="row" layout-align="space-between center">
        <div flex="{{vm.isMenuExpanded ? '80' : 'none'}}" layout="row" layout-align="space-between center" layout-wrap>
            <div>
                <md-button class="md-icon-button" ng-click="vm.translate(vm.sentence.id)"
                    ng-disabled="vm.sentence.correctness === -1 || vm.sentence.license === ''">
                    <md-icon>translate</md-icon>
                    <?php /* @translators: translate button on sentence menu (verb) */ ?>
                    <md-tooltip><?= __('Translate') ?></md-tooltip>
                </md-button>

                <md-button ng-if="vm.menu.canEdit || vm.menu.canTranscribe" class="md-icon-button" ng-click="vm.edit()">
                    <md-icon>edit</md-icon>
                    <?php /* @translators: edit button on sentence menu (verb) */ ?>
                    <md-tooltip><?= __('Edit') ?></md-tooltip>
                </md-button>

                <md-button class="md-icon-button" ng-click="vm.list()">
                    <md-icon>list</md-icon>
                    <md-tooltip><?= __('Add sentence to list') ?></md-tooltip>
                </md-button>

                <icon-with-progress is-loading="vm.iconsInProgress.favorite">
                    <md-button class="md-icon-button" ng-if="vm.isMenuExpanded" ng-click="vm.favorite()">
                        <md-icon>{{vm.sentence.is_favorite ? 'favorite' : 'favorite_border'}}</md-icon>
                        <md-tooltip ng-if="!vm.sentence.is_favorite"><?= __('Add to favorites') ?></md-tooltip>
                        <md-tooltip ng-if="vm.sentence.is_favorite"><?= __('Remove from favorites') ?></md-tooltip>
                    </md-button>
                </icon-with-progress>

                <md-button class="md-icon-button" ng-if="vm.isMenuExpanded && vm.menu.canDelete"
                           onclick="return confirm('<?= __('Are you sure?') ?>');"
                           ng-href="<?= $this->Url->build(['controller' => 'sentences', 'action' => 'delete']) ?>/{{vm.sentence.id}}">
                    <md-icon>delete</md-icon>
                    <?php /* @translators: delete button on sentence menu (verb) */ ?>
                    <md-tooltip><?= __('Delete') ?></md-tooltip>
                </md-button>
            </div>

            <div ng-if="vm.isMenuExpanded && vm.menu.canReview" class="correctness-info">
                <edit-review sentence-id="{{vm.sentence.id}}"
                             correctness="vm.sentence.current_user_review">
                </edit-review>
            </div>
        </div>

        <icon-with-progress is-loading="vm.iconsInProgress.adopt">
            <md-button class="md-icon-button" ng-if="vm.isMenuExpanded && vm.menu.canAdopt" ng-click="vm.adopt()">
                <md-icon>{{vm.sentence.is_owned_by_current_user ? 'person' : 'person_outline'}}</md-icon>
                <md-tooltip ng-if="!vm.sentence.is_owned_by_current_user"><?= __('Click to adopt') ?></md-tooltip>
                <md-tooltip ng-if="vm.sentence.is_owned_by_current_user"><?= __('Click to unadopt') ?></md-tooltip>
            </md-button>
        </icon-with-progress>

        <md-button ng-if="!vm.isMenuExpanded" class="md-icon-button" ng-click="vm.toggleMenu()">
            <md-icon>unfold_more</md-icon>
            <md-tooltip><?= __('Show more features') ?></md-tooltip>
        </md-button>

        <md-button ng-if="vm.isMenuExpanded" class="md-icon-button" ng-click="vm.toggleMenu()">
            <md-icon>unfold_less</md-icon>
            <md-tooltip><?= __('Show fewer features') ?></md-tooltip>
        </md-button>
    </div>
</div>
