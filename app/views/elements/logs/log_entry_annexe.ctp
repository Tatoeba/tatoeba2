<?php
$langCode = $log['Contribution']['sentence_lang'];
$sentenceId = null;
$translationId = null;
$sentenceText = $log['Contribution']['text'];
$sentenceDate = $log['Contribution']['datetime'];
$username = $log['User']['username'];
$action =  $log['Contribution']['action'];
$type = 'sentence';
$sentenceLink = null;
$translationLink = null;

if (isset($log['Contribution']['sentence_id'])) {
    $sentenceId = $log['Contribution']['sentence_id'];
    $sentenceLink = $html->link(
        '#'.$sentenceId,
        array(
            'controller' => 'sentences',
            'action' => 'show',
            $sentenceId
        )
    );
}
if (isset($log['Contribution']['translation_id'])) {
    $translationId = $log['Contribution']['translation_id'];
    $translationLink = $html->link(
        '#'.$translationId,
        array(
            'controller' => 'sentences',
            'action' => 'show',
            $translationId
        )
    );
    $type = 'link';
}

$infoLabel = $logs->getInfoLabel($type, $action, $username, $sentenceDate);
?>

<md-list-item class="md-2-line <?= $type.'-'.$action ?>"
              data-translation-id="<?= $translationId ?>">
    <div class="md-list-item-text" layout="column">
        <? if ($type =='sentence') { ?>
            <div><?= $sentenceLink.' '.$sentenceText ?></div>
        <? } else { ?>
            <div><?= $sentenceLink ?> ➜ <?= $translationLink ?></div>
        <? } ?>

        <p><?= $infoLabel ?></p>
    </div>
</md-list-item>