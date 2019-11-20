<?php
$statsUrl = $this->Url->build([
    'controller' => 'stats',
    'action' => 'sentences_by_language'
]);
?>

<div class="annexe-menu md-whiteframe-1dp" flex>
    <md-subheader><?= __('Stats') ?></md-subheader>
    <?php
    echo $this->Html->div('stat', format(
        __n('{number} contribution today',
            '{number} contributions today',
            $contribToday,
            true),
        ['number' => $this->Html->tag('strong', $this->Number->format($contribToday))]
    ));
    echo $this->Html->div('stat', format(
        __n('{number} supported language',
            '{number} supported languages',
            $numberOfLanguages,
            true),
        ['number' => $this->Html->tag('strong', $this->Number->format($numberOfLanguages))]
    ));
    echo $this->Html->div('stat', format(
        __n('{number} sentence',
            '{number} sentences',
            $numSentences,
            true),
        ['number' => $this->Html->tag('strong', $this->Number->format($numSentences))]
    ));
    ?>

    <div layout="row" layout-align="center center">
        <md-button class="md-primary" href="<?= $statsUrl ?>">
            <?= __('stats per languages') ?>
            <md-icon>keyboard_arrow_right</md-icon>
        </md-button>
    </div>
</div>