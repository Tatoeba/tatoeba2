<?php
use App\Model\CurrentUser;
use Cake\ORM\TableRegistry;
?>

<form layout="column" style="border-top: 1px solid #f1f1f1; padding-top: 10px;" ng-if="vm.visibility.list_form">
    <div layout="row" layout-margin>
        <md-input-container flex>
            <label><?= __('Search list or enter new list name') ?></label>
            <input id="list-form-<?= $sentenceId ?>" ng-model="vm.listSearch"
                   ng-change="vm.searchList()" ng-enter="vm.addToNewList()" ng-escape="vm.closeList()">
        </md-input-container>
        <md-button class="md-raised md-primary" ng-click="vm.addToNewList()" ng-disabled="!vm.listSearch"><?= __('Create') ?></md-button>
    </div>

    <md-list style="height: 310px; overflow-y: scroll; border-top: 1px solid #f1f1f1">
        <md-subheader ng-if="vm.listType === 'of_user'"><?= __('Your last selected list (if any) and last updated lists') ?></md-subheader>
        <md-subheader ng-if="vm.listType === 'search'"><?= __('Search results') ?></md-subheader>
        <md-list-item class="list" ng-repeat="list in vm.lists">
            <md-checkbox
                ng-change="vm.toggleList(list)"
                ng-model="list.hasSentence"
                class="md-primary"></md-checkbox> 
            <p flex>{{list.name}}</p>
            <em ng-if="list.isLastSelected" style="color: grey; padding: 0 5px"><?= __('(last selected)') ?></em>
            <md-icon ng-if="list.is_collaborative === '1'">
                group
                <md-tooltip><?= __('Collaborative list') ?></md-tooltip>
            </md-icon>
        </md-list-item>
        <md-list-item ng-if="vm.lists.length === 0 && vm.listType === 'search'"><em><?= __('No list found.') ?></em></md-list-item>
        <md-list-item ng-if="vm.lists.length === 0 && vm.listType === 'of_user'"><em><?= __('You have no lists which this sentence can be added to.') ?></em></md-list-item>
    </md-list>
    <md-button ng-click="vm.closeList()"><?= __('Close') ?></md-button>
</form>