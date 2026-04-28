<?php
$translationId = null;
$action =  $log->action;
$type = $log->type;

if ($log->translation_id) {
    $translationId = $log->translation_id;
}
?>

<md-list-item class="md-2-line <?= $type.'-'.$action ?>"
              data-translation-id="<?= $translationId ?>">
    <?php
        $withSentenceLink = true;
        echo $this->element('logs/log_text', compact('log', 'withSentenceLink'));
    ?>
</md-list-item>
