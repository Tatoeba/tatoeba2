<?php
if (!isset($sentenceVar)) {
    $sentenceVar = 'vm.sentence';
}
?>
<div layout="row" layout-align="start center" class="transcription" 
        ng-repeat="transcription in <?= $sentenceVar ?>.transcriptions">
    <div class="icon">
        <md-icon>subdirectory_arrow_right</md-icon>
    </div>
    <div class="text" ng-bind-html="transcription.html" flex></div>
</div>