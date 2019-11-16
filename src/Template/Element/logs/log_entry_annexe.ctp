<?php
use App\Lib\LanguagesLib;

$langCode = $log->sentence_lang;
$sentenceId = null;
$translationId = null;
$sentenceText = $log->text;
$sentenceDate = $log->datetime;
$username = $log->user ? $log->user->username : null;
$action =  $log->action;
$type = $log->type;
$sentenceLink = null;
$translationLink = null;

if ($log->sentence_id) {
    $sentenceId = $log->sentence_id;
    $sentenceLink = $this->Html->link(
        '#'.$sentenceId,
        array(
            'controller' => 'sentences',
            'action' => 'show',
            $sentenceId
        )
    );
}
if ($log->translation_id) {
    $translationId = $log->translation_id;
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
            <?php
            if ($type =='sentence') {
                echo $sentenceLink.' '.h($sentenceText);
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
