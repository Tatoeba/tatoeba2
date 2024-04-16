<?php
use App\Model\CurrentUser;
$this->Sentences->javascriptForAJAXSentencesGroup();
$this->Html->script(JS_PATH . 'sentences.contribute.js', ['block' => 'scriptBottom']);
?>

<section>
    
    <?php
    $langArray = $this->Languages->profileLanguagesArray(true);
    $currentUserLanguages = CurrentUser::getProfileLanguages();
    if (empty($currentUserLanguages)) {

        $this->Languages->displayAddLanguageMessage(true);

    } else {
        $preSelectedLang = $this->request->getSession()->read('contribute_lang');
        echo $this->Form->create(null, [
            'id' => 'sentence-form',
            'url' => '/sentences/add_an_other_sentence',
            'onsubmit' => 'return false',
            'ng-cloak' => true,
        ]);
        ?>

        <div layout="column" layout-margin>
            <div layout="row">
                <div class="language-select" layout="row" layout-align="start center" flex>
                    <?php /* @translators: language field label on new sentence addition form */ ?>
                    <label><?= __('Language'); ?></label>
                    <?php
                    echo $this->Form->select(
                        'contributionLang',
                        $langArray,
                        array(
                            'id' => 'contributionLang',
                            "value" => $preSelectedLang,
                            "class" => "language-selector",
                            "empty" => false
                        ),
                        false
                    );
                    ?>
                </div>

                <?php if (CurrentUser::getSetting('can_switch_license')) : ?>
                <div class="license-select" layout="row" layout-align="end center" flex>
                    <?php /* @translators: licence field label on new sentence addition form */ ?>
                    <label><?= __('License'); ?></label>
                    <?php
                    echo $this->Form->select(
                        'sentenceLicense',
                        $this->SentenceLicense->getLicenseOptions(),
                        array(
                            'id' => 'sentenceLicense',
                            "value" => CurrentUser::getSetting('default_license'),
                            "class" => "license-selector",
                            "empty" => false
                        ),
                        false
                    );
                    ?>
                </div>
                <?php endif; ?>
            </div>

            <md-input-container flex>
                <label><?= __('Sentence'); ?></label>
                <textarea id="SentenceText" type="text" ng-model="ctrl.data.text"
                          autocomplete="off"
                          ng-disabled="ctrl.isAdding">
                </textarea>
            </md-input-container>

            <div layout="row" layout-align="center center">
                <md-button id="submitNewSentence" class="md-raised md-primary">
                    <?php /* @translators: submit button of new sentence addition form */ ?>
                    <?= __('OK') ?>
                </md-button>
            </div>
        </div>
        <?php
        echo $this->Form->end();
    }
    ?>
</section>

<section>
    <md-toolbar class="md-hue-2">
        <div class="md-toolbar-tools">
            <h2><?php echo __('Sentences added'); ?></h2>
        </div>
    </md-toolbar>
    

    <div class="sentencesAddedloading" style="display:none">
        <md-progress-circular md-mode="indeterminate" class="block-loader">
        </md-progress-circular>
    </div>

    <div id="sentencesAdded" class="section md-whiteframe-1dp">
    <?php
    if (isset($sentence)) {
        $sentence['Translation'] = array();
        $this->Sentences->displaySentencesGroup($sentence);
    }
    ?>
    </div>
</section>
