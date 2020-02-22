<?php
use App\Lib\LanguagesLib;
use App\Model\CurrentUser;

$this->Html->script('/js/directives/sentence-and-translations.dir.js', array('block' => 'scriptBottom'));

if (!isset($menuExpanded)) {
    $menuExpanded = false;
}

list($directTranslations, $indirectTranslations) = $translations;

$username = $user ? $user->username : null;
$sentenceMenu = [
    'canEdit' => CurrentUser::canEditSentenceOfUser($username),
    'canReview' => CurrentUser::get('settings.users_collections_ratings'),
    'canAdopt' => CurrentUser::canAdoptOrUnadoptSentenceOfUser($user),
    'canDelete' => CurrentUser::canRemoveSentence($sentence->id, null, $username),
    'canLink' => CurrentUser::isTrusted(),
];
$langs = $this->Languages->profileLanguagesArray(false, false);

$userLanguagesJSON = htmlspecialchars(json_encode($langs), ENT_QUOTES, 'UTF-8');
$sentenceJSON = $this->Sentences->sentenceForAngular($sentence);
$directTranslationsJSON = $this->Sentences->translationsForAngular($directTranslations);
$indirectTranslationsJSON = $this->Sentences->translationsForAngular($indirectTranslations);

$profileUrl = $this->Url->build([
    'controller' => 'user',
    'action' => 'profile'
]);
$sentenceUrl = $this->Url->build([
    'controller' => 'sentences',
    'action' => 'show'
]);
?>
<div ng-cloak
     sentence-and-translations
     ng-init="vm.init(<?= $userLanguagesJSON ?>, <?= $sentenceJSON ?>, <?= $directTranslationsJSON ?>, <?= $indirectTranslationsJSON ?>)"
     class="sentence-and-translations md-whiteframe-1dp">
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
                    'sentence' => $sentence,
                    'menu' => $sentenceMenu,
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
            
            <div class="text" flex dir="{{vm.sentence.dir}}" 
                 ng-bind-html="vm.sentence.highlightedText ? vm.sentence.highlightedText : vm.sentence.text"></div>

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

            <?= $this->element('sentence_buttons/audio', ['angularVar' => 'vm.sentence']); ?>

            <md-button class="md-icon-button" ng-href="<?= $sentenceUrl ?>/{{vm.sentence.id}}">
                <md-icon>info</md-icon>
            </md-button>
        </div>
    </div>

    <?php
    if (CurrentUser::isMember()) {
        echo $this->element('sentences/translation_form', [
            'sentenceId' => $sentence->id,
            'langs' => $langs
        ]);

        echo $this->element('sentences/list_form', [
            'sentenceId' => $sentence->id
        ]);
    }

    if ($sentenceMenu['canEdit']) {
        echo $this->element('sentences/sentence_form', [
            'sentence' => $sentence
        ]);
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

    <div layout="column" class="indirect translations" ng-if="vm.visibility.translations && vm.indirectTranslations.length > 0"
            ng-init="vm.initIndirectTranslations(<?= $this->Sentences->translationsForAngular($indirectTranslations) ?>)">
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
