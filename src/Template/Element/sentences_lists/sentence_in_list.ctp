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

    <?php if ($canRemove) { ?>
    <div flex layout="row" layout-align="start center" class="removed-confirmation md-whiteframe-1dp" ng-if="vm.isRemoved[<?= $sentenceId ?>]">
        <p flex><?= __('Sentence removed from list.') ?></p>

        <md-button class="md-primary" ng-click="vm.undoRemoval(<?= $sentenceId ?>)">
            <?= __('Undo removal') ?>
        </md-button>
    </div>
    <?php } ?>

    <?= $this->element('sentences/sentence_and_translations', $sentenceAndTranslationsParams); ?>

    <?php if ($canRemove) { ?>
    <div class="remove-from-list" ng-if="!vm.isRemoved[<?= $sentenceId ?>]">
        <md-button class="md-icon-button md-warn" ng-click="vm.removeSentence(<?= $sentenceId ?>)">
            <md-icon>remove_circle_outline
                <md-tooltip><?= __('Remove from list') ?></md-tooltip>
            </md-icon>
        </md-button>
    </div>
    <?php } ?>
</div>