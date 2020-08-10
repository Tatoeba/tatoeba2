<?php
use App\Lib\LanguagesLib;

$type = 'sentence';
if (isset($log->type)) {
    $type = $log->type;
}

$sentenceLink = '';
if ($log->sentence_id) {
    $sentenceId = $log->sentence_id;
    $sentenceText = $log->text ? $log->text : ($log->sentence ? $log->sentence->text : null);
    $sentenceLink = $this->ClickableLinks->buildSentenceLink($sentenceId, $sentenceText);
}

$sentenceText = $log->text;
$sentenceScript = $log->script;

$translationLink = null;
if ($log->translation_id) {
    $translationId = $log->translation_id;
    $translationText = $log->translation ? $log->translation->text : null;
    $translationLink = $this->ClickableLinks->buildSentenceLink($translationId, $translationText);
}

$action = $log->action;
$username = $log->user ? $log->user->username : null;
$sentenceDate = $log->datetime;
$infoLabel = $this->Logs->getInfoLabel($type, $action, $username, $sentenceDate);

$langCode = $log->sentence_lang;

?>
<div class="md-list-item-text" layout="column">
    <div class="content">
        <?php
        if ($type =='sentence') {
            if (isset($withSentenceLink)) {
                echo $sentenceLink.' ';
            }
            echo $this->Languages->tagWithLang(
                'span', $langCode, $sentenceText, [], $sentenceScript
            );
        } elseif ($type == 'license') {
            if (isset($withSentenceLink)) {
                echo $sentenceLink.' ';
            }
            $licenseName = $this->SentenceLicense->getLicenseName($log->text);
            echo ' ➜ '.$this->Html->tag('span', $licenseName, array('class' => 'license'));
        } else { // link
            echo $sentenceLink.' ➜ '.$translationLink;
        }
        ?>
    </div>
    <p><?= $infoLabel ?></p>
</div>
