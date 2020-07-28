<?php
use App\Model\CurrentUser;
$this->Html->script('sentences/add.ctrl.js', ['block' => 'scriptBottom']);
$langs = $this->Languages->profileLanguagesArray();
$licencesOptions = $this->SentenceLicense->getLicenseOptions();
$userLanguagesJSON = h(json_encode($langs));
$licensesOptionsJSON = h(json_encode($licencesOptions));
$defautLicense = CurrentUser::getSetting('default_license');

if (!isset($withCloseButton)) {
    $withCloseButton = false;
}

if (empty($langs)) {

    $this->Languages->displayAddLanguageMessage(true);

} else {
    ?>
    <form name="sentenceForm" layout="column" layout-margin ng-init="vm.init(<?= $userLanguagesJSON ?>, <?= $licensesOptionsJSON ?>)" ng-cloak>

        <div layout="row" layout-align="start center">
            <md-input-container flex="50">
                <?php /* @translators: language field label on new sentence addition form */ ?>
                <label><?= __('Language') ?></label>
                <md-select ng-model="vm.newSentence.lang">
                    <md-option value="auto" ng-if="vm.showAutoDetect"><?= __('Auto detect') ?></md-option>
                    <md-option ng-repeat="(code, name) in vm.userLanguages" ng-value="code">
                        {{name}}
                    </md-option>
                </md-select>
            </md-input-container>
            
            <div class="language-icon-div" flex>
                <img ng-src="/img/flags/{{vm.newSentence.lang}}.svg" ng-if="vm.newSentence.lang && vm.newSentence.lang !== 'auto'" 
                    width="30" height="20" class="language-icon"/>
            </div>

            <?php if (CurrentUser::getSetting('can_switch_license')) : ?>
            <md-input-container>
                <?php /* @translators: label for licence selection dropdown in sentence addition form */ ?>
                <label><?= __('License'); ?></label>
                <md-select ng-model="vm.newSentence.license" ng-init="vm.newSentence.license = '<?= $defautLicense ?>'">
                    <md-option ng-repeat="(code, name) in vm.licenses" ng-value="code">
                        {{name}}
                    </md-option>
                </md-select>
            </md-input-container>
            <?php endif; ?>
        </div>

        <md-input-container>
            <?php /* @translators: label for sentence text in sentence addition form */ ?>
            <label><?= __('Sentence') ?></label>
            <textarea name="text" ng-model="vm.newSentence.text" ng-enter="vm.addSentence(sentenceForm)"></textarea>
            
            <div ng-messages="sentenceForm['text'].$error" role="alert">
                {{vm.newSentence.error}}
            </div>
        </md-input-container>

        <div layout="row" layout-align="end center">
            <?php if ($withCloseButton) { ?>
            <md-button class="md-raised" ng-click="vm.showForm = false">
                <?= __('Close') ?>
            </md-button>
            <?php } ?>

            <md-button class="md-raised md-primary" ng-click="vm.addSentence(sentenceForm)" ng-disabled="vm.inProgress || !vm.newSentence.text">
                <?= __('Add sentence') ?>
            </md-button>
        </div>

    </form>
    <?php
}
?>
