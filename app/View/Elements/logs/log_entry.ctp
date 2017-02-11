<?php
$langCode = $log['Contribution']['sentence_lang'];
$sentenceId = $log['Contribution']['sentence_id'];
$sentenceLink = $this->Html->link(
    '#'.$sentenceId,
    array(
        'controller' => 'sentences',
        'action' => 'show',
        $sentenceId
    )
);
$translationId = null;
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
$sentenceText = $log['Contribution']['text'];
$sentenceDate = $log['Contribution']['datetime'];
$obsolete = false;
if (isset($log['Contribution']['obsolete'])) {
    $obsolete = $log['Contribution']['obsolete'];
}
$username = $log['User']['username'];
$avatar = $log['User']['image'];
$action =  $log['Contribution']['action'];
$type = 'sentence';
if (isset($log['Contribution']['type'])) {
    $type = $log['Contribution']['type'];
}
$style = $obsolete ? 'obsolete' : $action;

$avatarUrl = $this->Members->imageUrl($avatar);
$userProfileUrl = $this->Html->url(array(
    'controller' => 'user',
    'action' => 'profile',
    $username
));
$sentenceUrl = $this->Html->url(array(
    'controller' => 'sentences',
    'action' => 'show',
    $sentenceId
));
$infoLabel = $this->Logs->getInfoLabel($type, $action, $username, $sentenceDate);
$langDir = LanguagesLib::getLanguageDirection($langCode);
?>

<md-list-item class="md-2-line <?= $type.'-'.$style ?>">
    <a href="<?= $userProfileUrl ?>">
        <img class="md-avatar" src="<?= $avatarUrl ?>">
    </a>
    <?php
    echo $this->Languages->icon(
        $langCode,
        array(
            'width' => 30,
            'height' => 20,
            'class' => 'md-secondary lang'
        )
    );
    ?>
    <div class="md-list-item-text" layout="column">
        <div class="content" dir="<?= $langDir ?>">
            <?
            if ($type =='sentence') {
                echo Sanitize::html($sentenceText);
            } else {
                echo $sentenceLink.' ➜ '.$translationLink;
            }
            ?>
        </div>
        <p><?= $infoLabel ?></p>
    </div>
    <md-button class="md-secondary md-icon-button" href="<?= $sentenceUrl ?>">
        <md-icon>info</md-icon>
    </md-button>
</md-list-item>