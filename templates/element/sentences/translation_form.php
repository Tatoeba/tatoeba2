<div ng-if="vm.visibility.translation_form" class="translation-form">

<?php if (!empty($langs)) { ?>
    <form layout="column" layout-margin>
        <md-input-container>
            <?php /* @translators: translation field label on new translation addition form */ ?>
            <label><?= __('Translation') ?></label>
            <textarea ng-attr-id="translation-form-{{vm.sentence.id}}" ng-model="vm.newTranslation.text" 
                      ng-enter="vm.saveTranslation(vm.sentence.id)"></textarea>
        </md-input-container>
        
        <div layout="row" layout-align="start center">
            <md-input-container flex="50">
                <?php /* @translators: language field label on new translation addition form */ ?>
                <label><?= __('Language') ?></label>
                <md-select ng-model="vm.newTranslation.lang">
                    <md-option value="auto" ng-if="vm.showAutoDetect"><?= __('Auto detect') ?></md-option>
                    <md-option ng-repeat="(code, name) in vm.userLanguages" ng-value="code">
                        {{name}}
                    </md-option>
                </md-select>
            </md-input-container>
            
            <div class="language-icon-div">
                <img ng-src="/img/flags/{{vm.newTranslation.lang}}.svg" ng-if="vm.newTranslation.lang && vm.newTranslation.lang !== 'auto'" 
                     width="30" height="20" class="language-icon"/>
            </div>
        </div>

        <div layout="row" layout-align="end center">
            <md-button class="md-raised" ng-click="vm.hide('translation_form')">
                <?php /* @translators: cancel button of translation addition/edition form (verb) */ ?>
                <?= __('Cancel') ?>
            </md-button>
            <md-button class="md-raised md-primary" ng-click="vm.saveTranslation(vm.sentence.id)">
                <span ng-if="!vm.newTranslation.editable">
                    <?= __('Add translation') ?>
                </span>
                <span ng-if="vm.newTranslation.editable">
                    <?= __('Edit translation') ?>
                </span>
            </md-button>
        </div>
    </form>
<?php 
} else {
    ?><div layout-padding><?php
    $this->Languages->displayAddLanguageMessage(false);
    ?></div><?php
} 
?>

</div>