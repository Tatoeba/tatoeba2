<?php
use App\Lib\LanguagesLib;

$langCode = $log['Contribution']['sentence_lang'];
$sentenceId = null;
$translationId = null;
$sentenceText = $log['Contribution']['text'];
$sentenceDate = $log['Contribution']['datetime'];
$username = $log['User']['username'];
$action =  $log['Contribution']['action'];
$type = $log['Contribution']['type'];
$sentenceLink = null;
$translationLink = null;

if (isset($log['Contribution']['sentence_id'])) {
    $sentenceId = $log['Contribution']['sentence_id'];
    $sentenceLink = $this->Html->link(
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
    $translationLink = $this->Html->link(
        '#'.$translationId,
        array(
            'controller' => 'sentences',
            'action' => 'show',
            $translationId
        )
    );
}

$infoLabel = $this->Logs->getInfoLabel($type, $action, $username, $sentenceDate);
$langDir = LanguagesLib::getLanguageDirection($langCode);
?>

<md-list-item class="md-2-line <?= $type.'-'.$action ?>"
              data-translation-id="<?= $translationId ?>">
    <div class="md-list-item-text" layout="column">
        <div class="content" dir="<?= $langDir ?>">
            <?
            if ($type =='sentence') {
                echo $sentenceLink.' '.Sanitize::html($sentenceText);
            } elseif ($type == 'license') {
                echo $sentenceLink.' ➜ '.$this->Html->tag('span', $sentenceText, array('class' => 'license'));
            } else { // link
                echo $sentenceLink.' ➜ '.$translationLink;
            }
            ?>
        </div>
        <p><?= $infoLabel ?></p>
    </div>
</md-list-item>