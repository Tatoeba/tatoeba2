<?php
$langs = $this->loadHelper('Languages')->onlyLanguagesArray(false);
?>

<md-dialog id="vocab-edit"
           aria-label="<?= __('Edit vocabulary item') ?>"
           style="max-width: 500px"
           ng-cloak>
    <md-toolbar>
        <div class="md-toolbar-tools">
            <h2 flex><?= __('Edit vocabulary item'); ?></h2>
            <md-button class="md-icon-button" ng-click="ctrl.close()">
              <md-icon>close</md-icon>
            </md-button>
        </div>
    </md-toolbar>

    <md-dialog-content layout-margin>
        <div layout="row" layout-margin layout-align="start center" ng-if="!ctrl.canEdit">
            <md-icon>info</md-icon>
            <span layout-fill><?= h(
                __('You cannot edit this vocabulary item because other members '.
                   'happen to have added it, too. You may remove it from your own '.
                   'list of vocabulary items.')
            ) ?></span>
        </div>

        <div layout="row" layout-margin layout-align="center end" ng-if="ctrl.canEdit">
            <div layout="column">
                <label for="lang"><?= __('Language:') ?></label>
                <?= $this->element(
                    'language_dropdown',
                    array(
                        'name' => 'lang',
                        'languages' => $langs,
                        'initialSelection' => '{{ctrl.vocab.lang}}',
                        'selectedLanguage' => 'ctrl.selected_lang',
                    )
                ) ?>
            </div>
            <md-input-container>
                <label for="text"><?= __('Vocabulary item') ?></label>
                <input name="text" ng-attr-lang="{{ctrl.selected_lang.code}}" dir="auto"
                       ng-model="ctrl.vocab.text" />
            </md-input-container>
        </div>
    </md-dialog-content>

    <md-dialog-actions layout-margin>
        <md-button flex="33" type="submit" class="md-raised md-warn"
                   ng-click="ctrl.remove(ctrl.vocab)">
            <?php /* @translators: button to delete a vocabulary request */ ?>
            <?= __('Remove'); ?>
        </md-button>
        <div flex />
        <md-button flex="33" type="submit" class="md-raised md-primary"
                   ng-disabled="!ctrl.canEdit || !ctrl.selected_lang || !ctrl.vocab.text"
                   ng-click="ctrl.save(ctrl.vocab)">
            <?php /* @translators: button to save a vocabulary request after edit */ ?>
            <?= __('Save'); ?>
        </md-button>
    </md-dialog-actions>
</md-dialog>
