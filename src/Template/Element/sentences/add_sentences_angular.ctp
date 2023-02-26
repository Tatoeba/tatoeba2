<?php
$this->Html->script('sentences/add.ctrl.js', ['block' => 'scriptBottom']);
?>
<section ng-controller="SentencesAddController as vm" style="padding-bottom: 1px; background: #fafafa">   
    <?= $this->element('sentences/add_sentence_form'); ?>

    <md-toolbar class="md-hue-2">
        <div class="md-toolbar-tools">
            <h2 flex><?php echo __('Sentences added'); ?></h2>

            <?= $this->element('sentences/expand_all_menus_button'); ?>
        </div>
    </md-toolbar>

    <md-progress-linear ng-if="vm.inProgress"></md-progress-linear>

    <div ng-if="vm.sentences.length === 0" class="section">
        <?= __('No sentences added yet.') ?>
    </div>

    <div ng-repeat="sentence in vm.sentences">
    <?php
    echo $this->element('sentences/sentence_and_translations', [
        'sentenceData' => 'sentence',
        'directTranslationsData' => 'sentence.translations[0]',
        'indirectTranslationsData' => 'sentence.translations[1]'
    ]);
    ?>
    </div>
</section>