<?php
extract($menu);
$activeItems = array_filter($menu);
$menuJSON = htmlspecialchars(json_encode($activeItems), ENT_QUOTES, 'UTF-8');
$isUnapproved = $sentence->correctness == -1;
?>

<div class="menu-wrapper" sentence-menu flex="{{vm.isMenuExpanded ? '100' : 'none'}}" ng-init="vm.isMenuExpanded = <?= (int)$expanded ?>">
    <div class="menu" layout="row" layout-align="space-between center">
        <div>
            <md-button class="md-icon-button" ng-click="vm.translate(<?= $sentence->id ?>)" ng-disabled="<?= $isUnapproved ? 'true' : 'false' ?>">
                <md-icon>translate</md-icon>
                <md-tooltip><?= __('Translate') ?></md-tooltip>
            </md-button>

            <?php if ($canEdit) { ?>
            <md-button class="md-icon-button" ng-click="vm.edit()">
                <md-icon>edit</md-icon>
                <md-tooltip><?= __('Edit') ?></md-tooltip>
            </md-button>
            <?php } ?>

            <md-button class="md-icon-button" ng-click="vm.list()">
                <md-icon>list</md-icon>
                <md-tooltip><?= __('Add sentence to list') ?></md-tooltip>
            </md-button>

            <md-button class="md-icon-button" ng-if="vm.isMenuExpanded" ng-click="vm.favorite()">
                <md-icon>{{vm.sentence.isFavorite ? 'favorite' : 'favorite_border'}}</md-icon>
                <md-tooltip ng-if="!vm.sentence.isFavorite"><?= __('Add to favorites') ?></md-tooltip>
                <md-tooltip ng-if="vm.sentence.isFavorite"><?= __('Remove from favorites') ?></md-tooltip>
            </md-button>

            <?php if ($canLink) { ?>
            <md-button class="md-icon-button" ng-if="vm.isMenuExpanded" ng-disabled="true">
                <md-icon>link</md-icon>
            </md-button>
            <?php } ?>

            <?php if ($canDelete) { ?>
            <md-button class="md-icon-button" ng-if="vm.isMenuExpanded"
                       onclick="return confirm('<?= __('Are you sure?') ?>');"
                       href="<?= $this->Url->build(['controller' => 'sentences', 'action' => 'delete', $sentence->id]) ?>">
                <md-icon>delete</md-icon>
                <md-tooltip><?= __('Delete') ?></md-tooltip>
            </md-button>
            <?php } ?>
        </div>

        <div ng-if="vm.isMenuExpanded">
        <?php if ($canReview) { ?>
            <md-button class="md-icon-button" ng-disabled="true">
                <md-icon>check_circle</md-icon>
            </md-button>
            <md-button class="md-icon-button" ng-disabled="true">
                <md-icon>help</md-icon>
            </md-button>
            <md-button class="md-icon-button" ng-disabled="true">
                <md-icon>error</md-icon>
            </md-button>
        <?php } ?>
        </div>

        <div>
            <?php if ($canAdopt) { ?>
            <md-button class="md-icon-button" ng-if="vm.isMenuExpanded" ng-click="vm.adopt()">
                <md-icon>{{vm.sentence.isOwnedByCurrentUser ? 'person' : 'person_outline'}}</md-icon>
                <md-tooltip ng-if="!vm.sentence.isOwnedByCurrentUser"><?= __('Click to adopt') ?></md-tooltip>
                <md-tooltip ng-if="vm.sentence.isOwnedByCurrentUser"><?= __('Click to unadopt') ?></md-tooltip>
            </md-button>
            <?php } ?>

            <md-button class="md-icon-button" ng-click="vm.toggleMenu()">
                <md-icon>more_horiz</md-icon>
            </md-button>
        </div>
    </div>
</div>
