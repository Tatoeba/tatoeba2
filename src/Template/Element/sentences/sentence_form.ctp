<form layout="column" layout-margin ng-if="vm.visibility.sentence_form">

<div ng-if="vm.sentence.audios && vm.sentence.audios.length > 0 && vm.sentence.transcriptions.length === 0">
    <p><?= __('You cannot edit this sentence because it has audio.'); ?>

    <div layout="row" layout-align="end center">
        <md-button class="md-raised" ng-click="vm.cancelEdit()">
            <?= __('Close') ?>
        </md-button>
    </div>
</div>

<div name="sentenceForm" layout="column" ng-if="!(vm.sentence.audios && vm.sentence.audios.length > 0 && vm.sentence.transcriptions.length === 0)">

    <div layout="row" layout-align="start center" ng-if="vm.sentence.permissions.canEdit">
        <md-input-container flex="50">
            <?php /* @translators: language field label on sentence addition form */ ?>
            <label><?= __('Language') ?></label>
            <md-select ng-model="vm.sentence.lang" ng-disabled="vm.sentence.audios && vm.sentence.audios.length > 0">
                <md-option value="unknown"><?= __('Other language') ?></md-option>
                <md-option ng-repeat="(code, name) in vm.userLanguages" ng-value="code">
                    {{name}}
                </md-option>
            </md-select>
        </md-input-container>
        
        <div class="language-icon-div">
            <img ng-src="/img/flags/{{vm.sentence.lang}}.svg"
                 width="30" height="20" class="language-icon"/>
        </div>
    </div>

    <md-input-container >
        <?php /* @translators: sentence text field label on new sentence addition form */ ?>
        <label><?= __('Sentence') ?></label>
        <textarea ng-attr-id="sentence-form-{{vm.sentence.id}}" ng-model="vm.sentence.text" 
                  ng-enter="vm.editSentence()" ng-escape="vm.cancelEdit()" ng-disabled="!vm.sentence.permissions.canEdit || (vm.sentence.audios && vm.sentence.audios.length > 0)"></textarea>
    </md-input-container>


    <div layout="column" ng-if="transcription.markup" ng-repeat="transcription in vm.sentence.transcriptions">
        <div layout="row" layout-align="start center" flex>
            <md-input-container flex>
                <?php /* @translators: label for transcription edition/addition form */ ?>
                <label><?= __('Transcription') ?></label>
                <textarea ng-attr-id="transcription-form-{{transcription.sentence.id}}" ng-model="transcription.markup"
                          ng-change="transcription.error = null; transcription.needsReview = false;"
                          ng-enter="vm.saveTranscription(transcription, vm.sentence, 'save')" ng-escape="vm.cancelEdit()"></textarea>
                
                <div ng-repeat="error in transcription.errors" ng-messages="sentenceForm.$error" role="alert" ng-bind-html="error"></div>
            </md-input-container>
            
            <md-button class="md-icon-button" ng-click="vm.saveTranscription(transcription, vm.sentence, 'reset')">
                <md-icon>undo</md-icon>
                <?php /* @translators: reset button of transcription edition form (verb) */ ?>
                <md-tooltip><?= __('Reset') ?></md-tooltip>
            </md-button>
                        
            <md-button class="md-icon-button" ng-click="vm.saveTranscription(transcription, vm.sentence, 'save')"  ng-if="transcription.needsReview">
                <md-icon>check_circle</md-icon>
                <md-tooltip><?= __('Mark as reviewed') ?></md-tooltip>
            </md-button>
        </div>
    </div>

    <div layout="row" layout-align="end center">
        <md-button class="md-raised" ng-click="vm.cancelEdit()">
            <?php /* @translators: cancel button of sentence text edition form (verb) */ ?>
            <?= __('Cancel') ?>
        </md-button>
        <md-button class="md-raised md-primary" ng-click="vm.editSentence()">
            <?php /* @translators: submit button of sentence text edition form (verb) */ ?>
            <?= __('Save') ?>
        </md-button>
    </div>
</div>

</form>