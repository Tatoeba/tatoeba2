<?php
$this->Html->script('sentences/add.ctrl.js', ['block' => 'scriptBottom']);
?>
<section ng-controller="SentencesAddController as vm" class="md-whiteframe-1dp" style="padding-bottom: 1px; background: #fafafa">
    <md-toolbar class="md-hue-2">
        <div class="md-toolbar-tools">
            <h2><?php echo __('Add new sentences'); ?></h2>
        </div>
    </md-toolbar>
    
    <?= $this->element('sentences/add_sentence_form'); ?>

    <md-toolbar class="md-hue-1">
        <div class="md-toolbar-tools">
            <h2><?php echo __('Sentences added'); ?></h2>
        </div>
    </md-toolbar>

    <md-progress-linear ng-if="vm.inProgress"></md-progress-linear>

    <div ng-if="vm.sentences.length === 0" layout-padding>
        <?= __('No sentences added yet.') ?>
    </div>

    <div ng-repeat="sentence in vm.sentences" layout-margin>
    <?php
    echo $this->element('sentences/sentence_and_translations', [
        'sentenceData' => 'sentence'
    ]);
    ?>
    </div>
</section>