<?php
$statsUrl = $this->Url->build([
    'controller' => 'stats',
    'action' => 'sentences_by_language'
]);
$numSentences = $sentencesToCount->count();
?>

<div class="content" flex>
    <div>
    <?=  format(
        __n('{number} contribution today',
            '{number} contributions today',
            $contribToday,
            true),
        ['number' => $this->Html->tag('strong', $this->Number->format($contribToday))]
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

    <div>
    <?= format(
        __n('{number} sentence',
            '{number} sentences',
            $numSentences,
            true),
        ['number' => $this->Html->tag('strong', $this->Number->format($numSentences))]
    ) ?>
    </div>
</div>

<div layout="row" layout-align="center center">
    <md-button class="md-primary" href="<?= $statsUrl ?>">
        <?= __('stats per languages') ?>
        <md-icon>keyboard_arrow_right</md-icon>
    </md-button>
</div>
