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
}

if (!isset($menuExpanded)) {
    $menuExpanded = false;
}

if (isset($translations)) {
    list($directTranslations, $indirectTranslations) = $translations;
} else {
    $directTranslations = $indirectTranslations = [];
}

$langs = $this->Languages->profileLanguagesArray(false, false);

if (!isset($userLanguagesData)) {
    $userLanguagesData = htmlspecialchars(json_encode($langs), ENT_QUOTES, 'UTF-8');
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
     ng-init="vm.init(<?= $userLanguagesData ?>, <?= $sentenceData ?>, <?= $directTranslationsData ?>, <?= $indirectTranslationsData ?>)"
     class="sentence-and-translations md-whiteframe-1dp">
    <div ng-if="vm.sentence.duplicate" layout="row" layout-padding class="duplicate-warning">
        <md-icon class="md-warn">warning</md-icon>
        <div flex><?= $duplicateWarning ?></div>
    </div>
    <div layout="column">
        <div layout="row" class="header">
            <md-subheader flex class="ellipsis">
                <span ng-if="vm.sentence.user && vm.sentence.user.username">
                    <?php
                    echo format(
                        __('Sentence {number} — belongs to {username}'),
                        array(
                            'number' => '<a ng-href="'.$sentenceUrl.'/{{vm.sentence.id}}">#{{vm.sentence.id}}</a>',
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

            <?php
            if (CurrentUser::isMember()) {
                echo $this->element('sentences/sentence_menu', [
                    'expanded' => $menuExpanded
                ]);
            }
            ?>
        </div>

        <div class="sentence" ng-class="{'not-reliable' : vm.sentence.correctness === -1}"
             layout="row" layout-align="start center" ng-if="!vm.visibility.sentence_form">
            <div class="lang">
                <language-icon lang="vm.sentence.lang" title="vm.sentence.lang_name"></language-icon>
            </div>
            
            <div class="text" flex dir="{{vm.sentence.dir}}" lang="{{vm.sentence.lang_tag}}">
                <span ng-if="vm.sentence.highlightedText" ng-bind-html="vm.sentence.highlightedText"></span>
                <span ng-if="!vm.sentence.highlightedText">{{vm.sentence.text}}</span>
            </div>

            <div class="indicator" ng-if="vm.sentence.user.is_native === '1'">
                <md-icon>
                    star
                    <md-tooltip md-direction="top">
                        <?= __('This sentence belongs to a native speaker.') ?>
                    </md-tooltip>
                </md-icon>
            </div>

            <div class="indicator" ng-if="vm.sentence.correctness === -1">
                <md-icon class="md-warn">warning</md-icon>
                <md-tooltip md-direction="top">
                    <?= __('This sentence is not reliable.') ?>
                </md-tooltip>
            </div>

            <md-button class="md-icon-button" ngclipboard data-clipboard-text="{{vm.sentence.text}}">
                <md-icon>content_copy</md-icon>
                <md-tooltip><?= __('Copy sentence') ?></md-tooltip>
            </md-button>

            <?= $this->element('sentence_buttons/audio', ['angularVar' => 'vm.sentence']); ?>

            <md-button class="md-icon-button" ng-href="<?= $sentenceUrl ?>/{{vm.sentence.id}}">
                <md-icon>info</md-icon>
                <md-tooltip><?= __('Go to sentence page') ?></md-tooltip>
            </md-button>
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

    <div layout="column" class="direct translations" ng-if="vm.visibility.translations && vm.directTranslations.length > 0">
        <md-divider></md-divider>
        <md-subheader><?= __('Translations') ?></md-subheader>

        <?php
        echo $this->element('sentences/translation', [
            'translations' => 'vm.directTranslations'
        ]);
        ?>
    </div>

    <div layout="column" class="indirect translations" ng-if="vm.visibility.translations && vm.indirectTranslations.length > 0">
        <md-subheader><?= __('Translations of translations') ?></md-subheader>

        <?php
        echo $this->element('sentences/translation', [
            'translations' => 'vm.indirectTranslations'
        ]);
        ?>
    </div>

    <div layout="column" ng-if="vm.sentence.extraTranslationsCount > 1 && vm.visibility.translations">
        <md-button ng-click="vm.expandOrCollapse()">
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
