<?php
$langCode = $log['Contribution']['sentence_lang'];
$sentenceId = $log['Contribution']['sentence_id'];
$translationId = null;
if (isset($log['Contribution']['translation_id'])) {
    $translationId = $log['Contribution']['translation_id'];
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

$avatarUrl = $members->imageUrl($avatar);
$userProfileUrl = $html->url(array(
    'controller' => 'user',
    'action' => 'profile',
    $username
));
$sentenceUrl = $html->url(array(
    'controller' => 'sentences',
    'action' => 'show',
    $sentenceId
));
$infoLabel = $logs->getInfoLabel($type, $action, $username, $sentenceDate);
?>

<md-list-item class="md-2-line <?= $type.'-'.$style ?>">
    <a href="<?= $userProfileUrl ?>">
        <img class="md-avatar" src="<?= $avatarUrl ?>">
    </a>
    <?php
    echo $languages->icon(
        $langCode,
        array(
            'width' => 30,
            'height' => 20,
            'class' => 'md-secondary'
        )
    );
    ?>
    <div class="md-list-item-text" layout="column">
        <? if ($type == 'sentence') { ?>
            <div><?= $sentenceText ?></div>
        <? } else { ?>
            <h3><?= '#'.$sentenceId ?> ➜ <?= '#'.$translationId ?></h3>
        <? } ?>
        
        <p><?= $infoLabel ?></p>
    </div>
    <md-button class="md-secondary md-icon-button" href="<?= $sentenceUrl ?>">
        <md-icon>more_horiz</md-icon>
    </md-button>
</md-list-item>