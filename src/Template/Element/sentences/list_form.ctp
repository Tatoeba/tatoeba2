<form layout="column" class="list-form" ng-if="vm.visibility.list_form">
    <div layout="row" layout-margin>
        <md-input-container flex>
            <label><?= __('Search list or enter new list name') ?></label>
            <input ng-attr-id="list-form-{{vm.sentence.id}}" ng-model="vm.listSearch"
                   ng-change="vm.searchList()" ng-enter="vm.addToNewList()" ng-escape="vm.closeList()">
        </md-input-container>
        <?php /* @translators: button to create a new list directly from a sentence block */ ?>
        <md-button class="md-raised md-primary" ng-click="vm.addToNewList()" ng-disabled="!vm.listSearch"><?= __('Create') ?></md-button>
    </div>

    <md-list>
        <md-subheader ng-if="vm.listType === 'of_user'"><?= __('Your last selected list (if any) and last updated lists') ?></md-subheader>
        <md-subheader ng-if="vm.listType === 'search'"><?= __('Search results') ?></md-subheader>
        <md-list-item class="list" ng-repeat="list in vm.lists">
            <md-checkbox
                ng-change="vm.toggleList(list)"
                ng-model="list.hasSentence"
                class="md-primary"></md-checkbox> 
            <p flex>{{list.name}}</p>
            <em class="last-selected" ng-if="list.isLastSelected"><?= __('(last selected)') ?></em>
            <md-icon ng-if="list.is_collaborative === '1'">
                group
                <md-tooltip><?= __('Collaborative list') ?></md-tooltip>
            </md-icon>
        </md-list-item>
        <md-list-item ng-if="vm.lists.length === 0 && vm.listType === 'search'"><em><?= __('No list found.') ?></em></md-list-item>
        <md-list-item ng-if="vm.lists.length === 0 && vm.listType === 'of_user'"><em><?= __('You have no lists which this sentence can be added to.') ?></em></md-list-item>
    </md-list>
    <md-button ng-click="vm.closeList()">
        <md-icon>close</md-icon> <?= __('Close') ?>
    </md-button>
</form>