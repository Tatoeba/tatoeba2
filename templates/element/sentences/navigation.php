<?php
$this->Html->script('/js/sentences/navigation.ctrl.js', ['block' => 'scriptBottom']);
$langArray = $this->Languages->languagesArrayAlone();
$selectedLanguage = $this->request->getSession()->read('random_lang_selected');
if (!$selectedLanguage) {
    $selectedLanguage = 'und';
}
$currentId = $currentId ? $currentId : 'null';
$prev = $prev ? $prev : 'null';
$next = $next ? $next : 'null';
$sentenceUrl = $this->Url->build([
    'controller' => 'sentences',
    'action' => 'show'
]);
?>
<div ng-app="app" ng-controller="SentencesNavigationController as vm" 
     ng-init="vm.init('<?= $selectedLanguage ?>', <?= $currentId ?>, <?= $prev ?>, <?= $next ?>)" 
     class="navigation" layout="row" layout-align="center center" layout-wrap ng-cloak>

    <div layout="row" layout-align="space-around center" layout-margin layout-wrap flex="auto">
        <div layout="row" layout-align="space-around center" flex="noshrink">
            <md-button ng-href="<?= $sentenceUrl ?>/{{vm.prev}}" class="md-primary"
                       ng-disabled="!vm.prev">
                <md-icon>keyboard_arrow_left</md-icon>
                <?php /* @translators: link to neighbour sentence on sentence page */ ?>
                <span hide-xs><?= __('previous') ?></span>
            </md-button>

            <md-button ng-href="<?= $sentenceUrl ?>/{{vm.lang}}" class="md-primary">
                <?php /* @translators: link to a random sentence on sentence page */ ?>
                <?= __('random') ?>
            </md-button>

            <md-button ng-href="<?= $sentenceUrl ?>/{{vm.next}}" class="md-primary"
                       ng-disabled="!vm.next">
                <?php /* @translators: link to neighbour sentence on sentence page */ ?>
                <span hide-xs><?= __('next') ?></span>
                <md-icon>keyboard_arrow_right</md-icon>
            </md-button>
        </div>

        <div>
        <md-tooltip md-direction="top">
            <?= __('Language for previous, next or random sentence'); ?>
        </md-tooltip>
        <?php
        echo $this->element(
            'language_dropdown', 
            array(
                'name' => 'lang',
                'initialSelection' => $selectedLanguage,
                /* @translators: placeholder used in language dropdown
                                 in navigation block on sentence pages */
                'languages' => $this->Languages->getSearchableLanguagesArray(__x('navigation', 'Any language')),
                'onSelectedLanguageChange' => 'vm.onSelectedLanguageChange(language)',
                'forceItemSelection' => true,
            )
        );
        ?>
        </div>
    </div>

    <?php
    // go to form
    echo $this->Form->create(null, [
        'id' => 'go-to-form',
        'url' => ['controller' => 'sentences', 'action' => 'go_to_sentence'],
        'type' => 'get',
        'hide-xs' => '',
        'layout' => 'row',
        'layout-align' => 'center center'
    ]);
    ?>
    <md-input-container layout="row" layout-align="start center">
        <?php
        echo $this->Form->control('sentence_id', [
            'type' => 'text',
            'label' => __('Show sentence #: '),
            'value' => $currentId,
            'lang' => '',
            'dir' => 'ltr',
        ]);
        ?>
        <md-button type="submit" class="go-button">
            <md-icon>arrow_forward</md-icon>
        </md-button>
    </md-input-container>
    <?php
    echo $this->Form->end();
    ?>
</div>
