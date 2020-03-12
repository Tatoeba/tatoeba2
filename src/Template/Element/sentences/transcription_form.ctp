<form layout="column" ng-if="transcription.showForm" flex>
    <div layout="row" layout-align="start center">
        <div class="icon">
            <md-icon>subdirectory_arrow_right</md-icon>
        </div>
        <md-input-container flex>
            <label><?= __('Transcription') ?></label>
            <textarea ng-attr-id="transcription-form-{{vm.sentence.id}}" ng-model="transcription.editing_format" 
                    ng-enter="vm.editTranscription()" ng-escape="vm.cancelEditTranscription()"></textarea>
        </md-input-container>
    </div>

    <div layout="row" layout-align="end center">
        <md-button class="md-raised" ng-click="transcription.showForm = false">
            <?= __('Cancel') ?>
        </md-button>
        <md-button class="md-raised" ng-click="vm.resetTranscription()">
            <?= __('Reset') ?>
        </md-button>
        <md-button class="md-raised md-primary" ng-click="vm.editTranscription()">
            <?= __('Save') ?>
        </md-button>
    </div>
</form>