<?php
$langs = $this->Languages->profileLanguagesArray(false, false);
?>
<div ng-if="vm.isTranslationFormVisible" style="background: #fafafa; border-top: 1px solid #f1f1f1">

<?php 
if (!empty($langs)) { 
    $userLanguagesJSON = htmlspecialchars(json_encode($langs), ENT_QUOTES, 'UTF-8');
    ?>
    <md-progress-linear ng-if="vm.inProgress"></md-progress-linear>
    <form layout="column" layout-margin ng-init="vm.initUserLanguages(<?= $userLanguagesJSON ?>)" style="padding-top: 10px">
        <md-input-container>
            <label><?= __('Translation') ?></label>
            <textarea id="translation-form-<?= $sentenceId ?>" ng-model="vm.newTranslation.text" 
                      ng-enter="vm.saveTranslation(<?= $sentenceId ?>)"></textarea>
        </md-input-container>
        
        <div layout="row" layout-align="start center">
            <md-input-container flex="50">
                <label><?= __('Language') ?></label>
                <md-select ng-model="vm.newTranslation.lang">
                    <md-option value="auto"><?= __('Auto detect') ?></md-option>
                    <md-option ng-repeat="(code, name) in vm.userLanguages" ng-value="code">
                        {{name}}
                    </md-option>
                </md-select>
            </md-input-container>
            
            <div style="padding: 10px 10px 0 10px">
                <img ng-src="/img/flags/{{vm.newTranslation.lang}}.svg" ng-if="vm.newTranslation.lang && vm.newTranslation.lang !== 'auto'" 
                     width="30" height="20" class="language-icon"/>
            </div>
        </div>

        <div layout="row" layout-align="end center">
            <md-button class="md-raised" ng-click="vm.isTranslationFormVisible = false">
                <?= __('Cancel') ?>
            </md-button>
            <md-button class="md-raised md-primary" ng-click="vm.saveTranslation(<?= $sentenceId ?>)">
                <span ng-if="!vm.newTranslation.editable">
                    <?= __('Submit translation') ?>
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