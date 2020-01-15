<?php 
$hasAudio = count($sentence->audios) > 0;
?>
<form layout="column" layout-margin style="padding-top: 10px" ng-if="vm.isSentenceFormVisible">
<?php if ($hasAudio) { ?>

    <p><?= __('You cannot edit this sentence because it has audio.'); ?>

    <div layout="row" layout-align="end center">
        <md-button class="md-raised" ng-click="vm.isSentenceFormVisible = false">
            <?= __('Close') ?>
        </md-button>
    </div>

<?php } else { ?>

    <div layout="row" layout-align="start center">
        <md-input-container flex="50">
            <label><?= __('Language') ?></label>
            <md-select ng-model="vm.sentence.lang">
                <md-option value="auto"><?= __('Auto detect') ?></md-option>
                <md-option ng-repeat="(code, name) in vm.userLanguages" ng-value="code">
                    {{name}}
                </md-option>
            </md-select>
        </md-input-container>
        
        <div style="padding: 10px 10px 0 10px">
            <img ng-src="/img/flags/{{vm.sentence.lang}}.svg" ng-if="vm.sentence.lang && vm.sentence.lang !== 'auto'" 
                 width="30" height="20" class="language-icon"/>
        </div>
    </div>

    <md-input-container>
        <label><?= __('Sentence') ?></label>
        <textarea ng-model="vm.sentence.text" ng-enter="vm.editSentence()"></textarea>
    </md-input-container>

    <div layout="row" layout-align="end center">
        <md-button class="md-raised" ng-click="vm.isSentenceFormVisible = false">
            <?= __('Cancel') ?>
        </md-button>
        <md-button class="md-raised md-primary" ng-click="vm.editSentence()">
            <?= __('Save') ?>
        </md-button>
    </div>

<?php } ?>
</form>