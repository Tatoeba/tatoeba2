<?php
use App\Model\CurrentUser;

if (!isset($sentenceVar)) {
    $sentenceVar = 'vm.sentence';
}

if (CurrentUser::getSetting('show_transcriptions')) {
    $ngIf = "!$sentenceVar.furigana || $sentenceVar.highlightedText";
} else {
    $ngIf = "(vm.isMenuExpanded || !transcription.needsReview) && (!$sentenceVar.furigana || $sentenceVar.highlightedText)";
}
?>
<div class="transcription" ng-repeat="transcription in <?= $sentenceVar ?>.transcriptions" ng-if="<?= $ngIf ?>">
    <div layout="row" layout-align="start center" ng-if="!transcription.showForm">
        <div class="icon">
            <md-icon>subdirectory_arrow_right</md-icon>
        </div>
        
        <div layout="row" layout-align="start center">
            <div ng-if="transcription.needsReview">
                <md-icon class="md-warn">warning</md-icon>
            </div>
            <div class="text" lang="{{transcription.lang_tag}}" ng-bind-html="transcription.html" flex></div>
            <md-tooltip md-direction="top">{{transcription.info_message}}</md-tooltip>
        </div>
    </div>
</div>