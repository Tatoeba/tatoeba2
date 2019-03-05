<?php
use App\Lib\LanguagesLib;

$langCode = $log->sentence_lang;
$sentenceId = $log->sentence_id;
$sentenceLink = $this->Html->link(
    '#'.$sentenceId,
    array(
        'controller' => 'sentences',
        'action' => 'show',
        $sentenceId
    )
);
$translationId = null;
if (isset($log->translation_id)) {
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
$sentenceText = $log->text;
$sentenceDate = $log->datetime;
$obsolete = false;
if (isset($log->obsolete)) {
    $obsolete = $log->obsolete;
}
$username = $log->user->username;
$avatar = $log->user->image;
$action =  $log->action;
$type = 'sentence';
if (isset($log->type)) {
    $type = $log->type;
}
$style = $obsolete ? 'obsolete' : $action;

$sentenceUrl = $this->Url->build(array(
    'controller' => 'sentences',
    'action' => 'show',
    $sentenceId
));
$infoLabel = $this->Logs->getInfoLabel($type, $action, $username, $sentenceDate);
$langDir = LanguagesLib::getLanguageDirection($langCode);
?>

<md-list-item class="md-2-line <?= $type.'-'.$style ?>">
    <?
    echo $this->Members->image($username, $avatar, array('class' => 'md-avatar'));
    echo $this->Languages->icon(
        $langCode,
        array(
            'ng-cloak' => true,
            'class' => 'md-secondary lang'
        )
    );
    ?>
    <div class="md-list-item-text" layout="column">
        <div class="content" dir="<?= $langDir ?>">
            <?
            if ($type =='sentence') {
                echo h($sentenceText);
            } elseif ($type == 'license') {
                echo ' ➜ '.$this->Html->tag('span', $sentenceText, array('class' => 'license'));
            } else { // link
                echo $sentenceLink.' ➜ '.$translationLink;
            }
            ?>
        </div>
        <p><?= $infoLabel ?></p>
    </div>
    <md-button ng-cloak class="md-secondary md-icon-button" href="<?= $sentenceUrl ?>">
        <md-icon>info</md-icon>
    </md-button>
</md-list-item>