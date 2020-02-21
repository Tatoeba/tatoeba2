<?php
use App\Model\CurrentUser;
use Cake\ORM\TableRegistry;

$SentencesLists = TableRegistry::getTableLocator()->get('SentencesLists');
$lists = $SentencesLists->getUserChoices(
    CurrentUser::get('id'), $sentenceId, true
);
$mostRecentList = $this->request->getSession()->read('most_recent_list');
$listsJSON = htmlspecialchars(json_encode($lists), ENT_QUOTES, 'UTF-8');
?>

<form layout="column" style="border-top: 1px solid #f1f1f1; padding-top: 10px;"
      ng-init="vm.initLists(<?= $listsJSON ?>, <?= CurrentUser::get('id') ?>)" ng-show="vm.visibility.list_form">
    <div layout="row" layout-margin>
        <md-input-container flex>
            <label><?= __('Search list or enter new list name') ?></label>
            <input id="list-form-<?= $sentenceId ?>" ng-model="vm.listSearch"
                   ng-change="vm.searchList()" ng-enter="vm.addToNewList()" ng-escape="vm.closeList()">
        </md-input-container>
        <md-button class="md-raised md-primary" ng-click="vm.addToNewList()"><?= __('Create') ?></md-button>
    </div>

    <md-list style="height: 200px; overflow-y: scroll; border-top: 1px solid #f1f1f1">
        <md-subheader ng-if="vm.listType === 'of_user'"><?= __('Your most recent lists') ?></md-subheader>
        <md-subheader ng-if="vm.listType === 'search'"><?= __('Search results') ?></md-subheader>
        <md-list-item class="list" ng-repeat="list in vm.lists">
            <md-checkbox
                ng-change="vm.toggleList(list)"
                ng-model="list.hasSentence"
                class="md-primary"></md-checkbox> 
            <p flex ng-class="{'is-mine': list.is_mine === '1'}">{{list.name}}</p>
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