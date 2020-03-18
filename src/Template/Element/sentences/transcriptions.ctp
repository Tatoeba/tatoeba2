<?php
use App\Model\CurrentUser;

if (!isset($sentenceVar)) {
    $sentenceVar = 'vm.sentence';
}

$ngIf = 'ng-if="vm.isMenuExpanded || (!transcription.needsReview && (!transcription.isReviewedFurigana || '.$sentenceVar.'.highlightedText))"';
if (CurrentUser::getSetting('show_transcriptions')) {
    $ngIf = '';
}
?>
<div class="transcription" ng-repeat="transcription in <?= $sentenceVar ?>.transcriptions" <?= $ngIf ?>>
    <div layout="row" layout-align="start center" ng-if="!transcription.showForm">
        <div class="icon">
            <md-icon>subdirectory_arrow_right</md-icon>
        </div>
        
        <div ng-if="transcription.needsReview">
            <md-icon class="md-warn">warning</md-icon>
        </div>

        <div class="text" ng-bind-html="transcription.html" flex>
            <md-tooltip>{{transcription.info_message}}</md-tooltip>
        </div>

        <?php if (CurrentUser::isMember()) { ?>
        <div ng-if="transcription.editing_format">
        <md-button class="md-icon-button" ng-if="!transcription.showForm && transcription.needsReview"
                   ng-click="vm.saveTranscription(transcription, <?= $sentenceVar ?>, 'save')">
            <md-icon>check_circle</md-icon>
            <md-tooltip><?= __('Mark as reviewed') ?></md-tooltip>
        </md-button>

        <md-button class="md-icon-button" ng-if="!transcription.showForm" ng-click="vm.editTranscription(transcription)">
            <md-icon>edit</md-icon>
            <md-tooltip><?= __('Edit transcription') ?></md-tooltip>
        </md-button>
        </div>
        <?php } ?>
    </div>

    <?php
    if (CurrentUser::isMember()) { 
        echo $this->element('sentences/transcription_form', [
            'sentenceVar' => $sentenceVar
        ]);    
    }
    ?>
</div>