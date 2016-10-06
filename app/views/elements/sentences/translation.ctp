<?php
$showExtra = '';
if ($isExtra) {
    $showExtra = 'ng-if="sentence.showExtra['.$sentenceId.']"';
}
$translationUrl = $this->Html->url(array(
    'controller' => 'sentences',
    'action' => 'show',
    $translation['id']
));
?>
<div layout="row" layout-align="start center" <?= $showExtra ?>
     class="translation">
    <md-icon class="chevron">chevron_right</md-icon>
    <div class="lang">
        <?
        echo $this->Languages->icon(
            $translation['lang'],
            array(
                'width' => 30,
                'height' => 20
            )
        );
        ?>
    </div>
    <div class="text" flex>
        <?= $translation['text'] ?>
    </div>
    <md-button class="md-icon-button"
               href="<?= $translationUrl ?>">
        <md-icon>info</md-icon>
    </md-button>
</div>