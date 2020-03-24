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
    </div>
</div>