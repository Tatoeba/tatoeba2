<?php
$statsUrl = $this->Url->build([
    'controller' => 'stats',
    'action' => 'sentences_by_language'
]);
$activityUrl = $this->Url->build([
    'controller' => 'contributions',
    'action' => 'activity_timeline'
]);
?>

<div class="content" flex>
    <div class="category">
        <div>
        <?= format(
            __n('{number} sentence',
                '{number} sentences',
                $numSentences,
                true),
            ['number' => $this->Html->tag('strong', $this->Number->format($numSentences))]
        ) ?>
        </div>
        <div>
        <?= format(
            __n('{number} supported language',
                '{number} supported languages',
                $numberOfLanguages,
                true),
            ['number' => $this->Html->tag('strong', $this->Number->format($numberOfLanguages))]
        ) ?>
        </div>
        <div layout="row" layout-align="center center">
            <md-button class="md-primary" href="<?= $statsUrl ?>">
                <?= __('Stats per languages') ?>
                <md-icon ng-cloak>keyboard_arrow_right</md-icon>
            </md-button>
        </div>
    </div>

    <div class="category">
        <?=  format(
            __n('{number} contribution today',
                '{number} contributions today',
                $contribToday,
                true),
            ['number' => $this->Html->tag('strong', $this->Number->format($contribToday))]
        ) ?>
        <div layout="row" layout-align="center center">
            <md-button class="md-primary" href="<?= $activityUrl ?>">
                <?= __('Activity timeline') ?>
                <md-icon ng-cloak>keyboard_arrow_right</md-icon>
            </md-button>
        </div>
    </div>
</div>
