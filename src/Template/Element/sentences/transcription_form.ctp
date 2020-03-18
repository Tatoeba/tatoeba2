<?php
if (!isset($sentenceVar)) {
    $sentenceVar = 'vm.sentence';
}
?>
<form name="transcriptionForm" layout="column" ng-if="transcription.showForm && transcription.editing_format" flex>
    <div layout="row" layout-align="start center">
        <div class="icon">
            <md-icon>subdirectory_arrow_right</md-icon>
        </div>
        <md-input-container flex>
            <label><?= __('Transcription') ?></label>
            <textarea ng-attr-id="transcription-form-{{transcription.sentence.id}}" ng-model="transcription.editing_format" ng-change="transcription.error = null"
                      ng-enter="vm.saveTranscription(transcription, <?= $sentenceVar ?>, 'save')" ng-escape="vm.cancelEditTranscription(transcription)"></textarea>

            <div ng-messages="transcriptionForm.$error" role="alert">
                {{transcription.error}}
            </div>
        </md-input-container>
    </div>

    <div layout="row" layout-align="end center">
        <md-button class="md-raised" ng-click="vm.cancelEditTranscription(transcription)">
            <?= __('Cancel') ?>
        </md-button>
        <md-button class="md-raised" ng-click="vm.saveTranscription(transcription, <?= $sentenceVar ?>, 'reset')">
            <?= __('Reset') ?>
        </md-button>
        <md-button class="md-raised md-primary" ng-click="vm.saveTranscription(transcription, <?= $sentenceVar ?>, 'save')">
            <?= __('Save') ?>
        </md-button>
    </div>
</form>