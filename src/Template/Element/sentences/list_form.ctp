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
      ng-init="vm.initLists(<?= $listsJSON ?>)" ng-if="vm.isListFormVisible">
    <div layout="column" layout-margin>
        <md-input-container>
            <label><?= __('Search list or enter new list name') ?></label>
            <input id="list-form-<?= $sentenceId ?>" ng-model="vm.listSearch">
        </md-input-container>
        <div layout="row" layout-align="end">
            <md-button class="md-raised" ng-click="vm.isListFormVisible = false"><?= __('Close') ?></md-button>
            <md-button class="md-raised md-primary"><?= __('Add to new list') ?></md-button>
        </div>
    </div>

    <md-list style="height: 200px; overflow-y: scroll; border-top: 1px solid #f1f1f1">
        <md-subheader><?= __('Add to existing list') ?></md-subheader>
        <md-list-item ng-repeat="list in vm.lists | filter: { name: vm.listSearch }" ng-click="vm.toggleList(list)">
            <md-checkbox
                ng-model="list.hasSentence"
                class="md-primary"></md-checkbox> 
            <p>{{list.name}}</p>
        </md-list-item>
    </md-list>
</form>