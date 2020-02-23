<?php
use App\Model\CurrentUser;
$this->Html->script('sentences/add.ctrl.js', ['block' => 'scriptBottom']);
$langs = $this->Languages->profileLanguagesArray(false, false);
$licencesOptions = $this->Sentences->License->getLicenseOptions();
$userLanguagesJSON = htmlspecialchars(json_encode($langs), ENT_QUOTES, 'UTF-8');
$licensesOptionsJSON = htmlspecialchars(json_encode($licencesOptions), ENT_QUOTES, 'UTF-8');
$defautLicense = CurrentUser::getSetting('default_license'); // TODO
?>

<div ng-controller="SentencesAddController as vm" ng-init="vm.init(<?= $userLanguagesJSON ?>, <?= $licensesOptionsJSON ?>)">

<section class="md-whiteframe-1dp" style="padding-bottom: 1px; background: #fafafa">
    <md-toolbar class="md-hue-2">
        <div class="md-toolbar-tools">
            <h2><?php echo __('Add new sentences'); ?></h2>
        </div>
    </md-toolbar>
    
    <?php
    if (empty($langs)) {

        $this->Languages->displayAddLanguageMessage(true);

    } else {
        ?>
        <form layout="column" layout-margin>

            <div layout="row" layout-align="start center">
                <md-input-container flex="50">
                    <label><?= __('Language') ?></label>
                    <md-select ng-model="vm.newSentence.lang">
                        <md-option value="auto" ng-if="vm.showAutoDetect"><?= __('Auto detect') ?></md-option>
                        <md-option ng-repeat="(code, name) in vm.userLanguages" ng-value="code">
                            {{name}}
                        </md-option>
                    </md-select>
                </md-input-container>
                
                <div style="padding: 10px 10px 0 10px" flex>
                    <img ng-src="/img/flags/{{vm.newSentence.lang}}.svg" ng-if="vm.newSentence.lang && vm.newSentence.lang !== 'auto'" 
                        width="30" height="20" class="language-icon"/>
                </div>

                <?php if (CurrentUser::getSetting('can_switch_license')) : ?>
                <md-input-container>
                    <label><?= __('License'); ?></label>
                    <md-select ng-model="vm.newSentence.license">
                        <md-option ng-repeat="(code, name) in vm.licenses" ng-value="code">
                            {{name}}
                        </md-option>
                    </md-select>
                </md-input-container>
                <?php endif; ?>
            </div>

            <md-input-container>
                <label><?= __('Sentence') ?></label>
                <textarea ng-model="vm.newSentence.text" ng-enter="vm.addSentence()"></textarea>
            </md-input-container>

            <div layout="row" layout-align="end center">
                <md-button class="md-raised md-primary" ng-click="vm.addSentence()">
                    <?= __('Add sentence') ?>
                </md-button>
            </div>

        </form>
        <?php
    }
    ?>

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

</div>