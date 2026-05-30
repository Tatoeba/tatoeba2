<?php
$langCode = $log->sentence_lang;
$sentenceId = $log->sentence_id;
$obsolete = false;
if (isset($log->obsolete)) {
    $obsolete = $log->obsolete;
}
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
?>

<md-list-item class="md-2-line <?= $type.'-'.$style ?>">
    <?php
    echo $this->Members->image($log->user, array('class' => 'md-avatar'));
    if ($type != 'link') {
        echo $this->Languages->icon(
            $langCode,
            array(
                'ng-cloak' => true,
                'class' => 'md-secondary lang'
            )
        );
    }
    echo $this->element('logs/log_text', compact('log'));
    ?>
    <md-button ng-cloak class="md-secondary md-icon-button" href="<?= $sentenceUrl ?>">
        <md-icon>info</md-icon>
    </md-button>
</md-list-item>
