<icon-with-progress is-loading="vm.iconsInProgress.reviewOk">
    <md-button class="md-icon-button" aria-label="<?= h(__('Mark as "OK"')) ?>"
               ng-click="vm.setReview(1)" ng-if="vm.correctness !== 1">
        <md-icon>check_circle</md-icon>
        <md-tooltip><?= __('Mark as "OK"') ?></md-tooltip>
    </md-button>
    <md-button class="md-icon-button" aria-label="<?= h(__('Unmark sentence')) ?>"
               ng-click="vm.resetReview()" ng-if="vm.correctness === 1">
        <md-icon class="ok">check_circle</md-icon>
        <md-tooltip><?= __('Unmark sentence') ?></md-tooltip>
    </md-button>
</icon-with-progress>

<icon-with-progress is-loading="vm.iconsInProgress.reviewUnsure">
    <md-button class="md-icon-button" aria-label="<?= h(__('Mark as "unsure"')) ?>"
               ng-click="vm.setReview(0)" ng-if="vm.correctness !== 0">
        <md-icon>help</md-icon>
        <md-tooltip><?= __('Mark as "unsure"') ?></md-tooltip>
    </md-button>
    <md-button class="md-icon-button" aria-label="<?= h(__('Unmark sentence')) ?>"
               ng-click="vm.resetReview()" ng-if="vm.correctness === 0">
        <md-icon class="unsure">help</md-icon>
        <md-tooltip><?= __('Unmark sentence') ?></md-tooltip>
    </md-button>
</icon-with-progress>

<icon-with-progress is-loading="vm.iconsInProgress.reviewNotOk">
    <md-button class="md-icon-button" aria-label="<?= h(__('Mark as "not OK"')) ?>"
               ng-click="vm.setReview(-1)" ng-if="vm.correctness !== -1">
        <md-icon>error</md-icon>
        <md-tooltip><?= __('Mark as "not OK"') ?></md-tooltip>
    </md-button>
    <md-button class="md-icon-button not-ok" aria-label="<?= h(__('Unmark sentence')) ?>"
               ng-click="vm.resetReview()" ng-if="vm.correctness === -1">
        <md-icon class="not-ok">error</md-icon>
        <md-tooltip><?= __('Unmark sentence') ?></md-tooltip>
    </md-button>
</icon-with-progress>
