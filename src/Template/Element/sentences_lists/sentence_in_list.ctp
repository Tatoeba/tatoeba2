<?php
if (!isset($sentenceId)) {
    $sentenceId = 'sentence.id';
}
if (!isset($ngRepeat)) {
    $ngRepeat = '';
} else {
    $ngRepeat = 'ng-repeat="'.$ngRepeat.'"';
}
if (!isset($canRemove)) {
    $canRemove = false;
}
$editableCss = $canRemove ? 'removeable' : '';
?>

<div class="sentence-and-translations-wrapper <?= $editableCss ?>" layout="row" layout-align="center start" 
     ng-class="{'removed': vm.isRemoved[<?= $sentenceId ?>]}"
     <?= $ngRepeat ?>>

    <div flex class="removed-confirmation md-whiteframe-1dp" ng-if="vm.isRemoved[<?= $sentenceId ?>]">
        <?= __('Sentence removed from list.') ?>
    </div>

    <?= $this->element('sentences/sentence_and_translations', $sentenceAndTranslationsParams); ?>

    <?php if ($canRemove) { ?>
    <div class="remove-from-list" ng-if="!vm.isRemoved[<?= $sentenceId ?>]">
        <md-button class="md-icon-button md-warn" ng-click="vm.removeSentence(<?= $sentenceId ?>)">
            <md-icon>remove_circle_outline
                <md-tooltip><?= __('Remove from list') ?></md-tooltip>
            </md-icon>
        </md-button>
    </div>

    <div class="remove-from-list" ng-if="vm.isRemoved[<?= $sentenceId ?>]">
        <md-button class="md-icon-button" ng-click="vm.undoRemoval(<?= $sentenceId ?>)">
            <md-icon>undo
                <md-tooltip><?= __('Undo') ?></md-tooltip>
            </md-icon>
        </md-button>
    </div>
    <?php } ?>
</div>