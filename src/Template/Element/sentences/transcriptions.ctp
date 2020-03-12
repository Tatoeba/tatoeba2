<?php
use App\Model\CurrentUser;

if (!isset($sentenceVar)) {
    $sentenceVar = 'vm.sentence';
}
?>
<div class="transcription" ng-repeat="transcription in <?= $sentenceVar ?>.transcriptions">
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
        <md-button class="md-icon-button" ng-if="!transcription.showForm && transcription.needsReview">
            <md-icon>check_circle</md-icon>
            <md-tooltip><?= __('Mark as reviewed') ?></md-tooltip>
        </md-button>

        <md-button class="md-icon-button" ng-if="!transcription.showForm" ng-click="transcription.showForm = true">
            <md-icon>edit</md-icon>
            <md-tooltip><?= __('Edit transcription') ?></md-tooltip>
        </md-button>
        <?php } ?>
    </div>

    <?php
    if (CurrentUser::isMember()) { 
        echo $this->element('sentences/transcription_form');    
    }
    ?>
</div>