<?php
/**
 * This component is kind of a Frankenstein component that mixes data coming
 * from PHP and data coming from Angular.
 * 
 * (1) Example for displaying a sentence with PHP variables:
 * 
 * $this->element('sentences/sentence_and_translation', [
 *     'sentence' => $sentence,
 *     'translations' => $translations
 * ]);
 * 
 * (2) Example for displaying a sentence with Angular variables:
 * 
 * $this->element('sentences/sentence_and_translation', [
 *     'sentenceData' => 'sentence',
 *     'directTranslationsData' => 'sentence.translations[0]',
 *     'indirectTranslationsData' => 'sentence.translations[1]'
 * ]);
 */

use App\Lib\LanguagesLib;
use App\Model\CurrentUser;

$this->AssetCompress->script('sentence-component.js', ['block' => 'scriptBottom']);
if (CurrentUser::isMember()) {
    $this->Html->script('/js/services/list-data.srv.js', array('block' => 'scriptBottom'));
    $this->Html->script('/js/directives/edit-review.dir.js', ['block' => 'scriptBottom']);
    $this->AngularTemplate->addTemplate(
        $this->element('reviews/edit_review'),
        'edit-review-template'
    );
}
$this->Html->script('/js/directives/audio-button.dir.js', ['block' => 'scriptBottom']);
$this->AngularTemplate->addTemplate(
    $this->element('sentence_buttons/audio'),
    'audio-button-template'
);

if (!isset($menuExpanded)) {
    $menuExpanded = false;
}

if (isset($translations)) {
    list($directTranslations, $indirectTranslations) = $translations;
} else {
    $directTranslations = $indirectTranslations = [];
}

$langs = $this->Languages->profileLanguagesArray();

if (!isset($translationLang)) {
    $translationLang = 'und';
}
if (!isset($userLanguagesData)) {
    $userLanguagesData = h(json_encode($langs));
}
if (!isset($sentenceData)) {
    $sentenceData = $this->Sentences->sentenceForAngular($sentence);
}
if (!isset($directTranslationsData)) {
    $directTranslationsData = $this->Sentences->translationsForAngular($directTranslations);
}
if (!isset($indirectTranslationsData)) {
    $indirectTranslationsData = $this->Sentences->translationsForAngular($indirectTranslations);    
}
// Prevent interpolation by AngularJS
$sentenceData = str_replace('{{', '\{\{', $sentenceData);
$directTranslationsData = str_replace('{{', '\{\{', $directTranslationsData);
$indirectTranslationsData = str_replace('{{', '\{\{', $indirectTranslationsData);

if (!isset($duplicateWarning)) {
    $duplicateWarning = __('Your sentence was not added because the following already exists.');
}


$profileUrl = $this->Url->build([
    'controller' => 'user',
    'action' => 'profile'
]);
$sentenceUrl = $this->Url->build([
    'controller' => 'sentences',
    'action' => 'show'
]);
?>
<div ng-cloak flex
     sentence-and-translations
     ng-init="vm.init(<?= $userLanguagesData ?>, <?= $sentenceData ?>, <?= $directTranslationsData ?>, <?= $indirectTranslationsData ?>, '<?= $translationLang ?>')"
     class="sentence-and-translations md-whiteframe-1dp">
    <div ng-if="vm.sentence.duplicate" layout="row" layout-padding class="duplicate-warning">
        <md-icon class="md-warn">warning</md-icon>
        <div flex><?= $duplicateWarning ?></div>
    </div>
    <div layout="column">
        <div layout="row" layout-wrap class="header">
            <?php
            if (CurrentUser::isMember()) {
                echo $this->element('sentences/sentence_menu', [
                    'expanded' => $menuExpanded,
                ]);
            } else {
                echo $this->element('sentences/transcription_button');
            }
            ?>

            <md-subheader flex="auto">
                <span ng-if="vm.sentence.user && vm.sentence.user.username">
                    <?php
                    $linkText = $this->Pages->formatSentenceIdWithSharp('{{vm.sentence.id}}');
                    echo format(
                        __('Sentence {number} — belongs to {username}'),
                        array(
                            'number' => '<a ng-href="'.$sentenceUrl.'/{{vm.sentence.id}}">'.h($linkText).'</a>',
                            'username' => '<a ng-href="'.$profileUrl.'/{{vm.sentence.user.username}}">{{vm.sentence.user.username}}</a>'
                        )
                    );
                    ?>
                </span>
                <span ng-if="!vm.sentence.user || !vm.sentence.user.username">
                    <?php
                    echo format(
                        __('Sentence {number}'),
                        array(
                            'number' => '<a ng-href="'.$sentenceUrl.'/{{vm.sentence.id}}">#{{vm.sentence.id}}</a>'
                        )
                    );
                    ?>
                </span>
            </md-subheader>
        </div>

        <div class="sentence" ng-class="{'not-reliable' : vm.sentence.correctness === -1}" ng-if="!vm.visibility.sentence_form">
            <div layout="row" layout-align="stretch" flex>
            <div ng-click="translation.showActions = !translation.showActions" layout="row" layout-align="start center" role="switch" flex>
            <div class="lang">
                <language-icon lang="vm.sentence.lang" title="vm.sentence.lang_name"></language-icon>
            </div>
            
            <div class="text" dir="{{vm.sentence.dir}}" lang="{{vm.sentence.lang_tag}}">
                <span ng-if="vm.sentence.highlightedText" ng-bind-html="vm.sentence.highlightedText"></span>
                <span ng-if="!vm.sentence.highlightedText">
                    <span ng-if="vm.sentence.furigana" ng-bind-html="vm.sentence.furigana.html">
                        <md-tooltip md-direction="top">{{vm.sentence.furigana.info_message}}</md-tooltip>
                    </span>
                    <span ng-if="!vm.sentence.furigana">{{vm.sentence.text}}</span>
                </span>
            </div>
            </div>

            <div class="indicator" ng-if="vm.sentence.user.is_native === '1'">
                <md-icon>
                    star
                    <md-tooltip md-direction="top">
                        <?= __('This sentence belongs to a native speaker.') ?>
                    </md-tooltip>
                </md-icon>
            </div>

            <?= $this->element('sentences/sentence_icons', ['angularVar' => 'vm.sentence']); ?>
            </div>
            
            <?= $this->element('sentences/transcriptions'); ?>
        </div>
    </div>

    <?php
    if (CurrentUser::isMember()) {
        echo $this->element('sentences/translation_form', [
            'langs' => $langs
        ]);

        echo $this->element('sentences/list_form');

        echo $this->element('sentences/sentence_form');
    }
    ?>

    <md-progress-linear ng-if="vm.inProgress"></md-progress-linear>

    <div layout="column" class="direct translations"
         ng-if="::vm.visibility.translations && vm.directTranslations.length > 0 || undefined"
         ng-show="vm.visibility.translations && vm.directTranslations.length > 0">
        <md-divider></md-divider>
        <?php /* @translators: text divider between a sentence and its translations */ ?>
        <md-subheader><?= __('Translations') ?></md-subheader>

        <?php
        echo $this->element('sentences/translation', [
            'translations' => 'vm.directTranslations'
        ]);
        ?>
    </div>

    <div layout="column" class="indirect translations"
         ng-if="::vm.visibility.translations && vm.indirectTranslations.length > 0 && !vm.indirectTranslations[0].isHidden || undefined"
         ng-show="vm.visibility.translations && vm.indirectTranslations.length > 0 && !vm.indirectTranslations[0].isHidden">
        <md-subheader><?= __('Translations of translations') ?></md-subheader>

        <?php
        echo $this->element('sentences/translation', [
            'translations' => 'vm.indirectTranslations'
        ]);
        ?>
    </div>

    <div class="expand-collapse" layout="column"
         ng-if="::vm.sentence.expandLabel && vm.visibility.translations || undefined"
         ng-show="vm.sentence.expandLabel && vm.visibility.translations">

        <md-button ng-click="vm.expandOrCollapse(!vm.isExpanded)">
            <md-icon>{{vm.expandableIcon}}</md-icon>
            <span ng-if="!vm.isExpanded">
                {{vm.sentence.expandLabel}}
            </span>
            <span ng-if="vm.isExpanded">
                <?php echo __('Fewer translations') ?>
            </span>
        </md-button>
    </div>
</div>
