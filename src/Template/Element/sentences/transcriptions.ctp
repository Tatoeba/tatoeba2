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
        <div class="text" ng-bind-html="transcription.html" flex></div>

        <?php if (CurrentUser::isMember()) { ?>
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