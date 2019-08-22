<?php
use App\Lib\LanguagesLib;

$showExtra = '';
if ($isExtra) {
    $showExtra = 'ng-if="vm.isExpanded"';
}
$translationUrl = $this->Url->build(array(
    'controller' => 'sentences',
    'action' => 'show',
    $translation->id
));
$notReliable = $translation->correctness == -1;
?>
<div layout="row" layout-align="start center" <?= $showExtra ?>
     class="translation <?= $notReliable ? 'not-reliable' : '' ?>">
    <md-icon class="chevron">chevron_right</md-icon>
    <div class="lang">
        <?= $this->Languages->icon(
                $translation->lang,
                [
                    'width' => 30,
                    'height' => 20
                ]
            ) ?>
    </div>
    <div class="text" flex
         dir="<?= LanguagesLib::getLanguageDirection($translation->lang) ?>">
        <?= h($translation->text) ?>
    </div>
    <?php if ($notReliable) { ?>
        <md-icon class="md-warn">warning</md-icon>
        <md-tooltip md-direction="top">
            <?= __('This sentence is not reliable.') ?>
        </md-tooltip>
    <?php } ?>

    <?= $this->element('sentence_buttons/audio', ['sentence' => $translation]); ?>
    
    <md-button class="md-icon-button"
               href="<?= $translationUrl ?>">
        <md-icon>info</md-icon>
    </md-button>
</div>