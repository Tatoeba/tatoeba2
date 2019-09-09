<?php
use App\Model\CurrentUser;

$addUrl = $this->Url->build(
    array(
        'controller' => 'vocabulary',
        'action' => 'add'
    )
);
$indexUrl = $this->Url->build(
    array(
        'controller' => 'vocabulary',
        'action' => 'of',
        CurrentUser::get('username')
    )
);
$addSentencesUrl = $this->Url->build(
    array(
        'controller' => 'vocabulary',
        'action' => 'add_sentences'
    )
);
?>
<div class="section md-whiteframe-1dp" layout="column">
    <h2><?= __('Vocabulary items'); ?></h2>
    <md-list>
        <md-list-item href="<?= $indexUrl ?>">
            <?= __('My vocabulary items'); ?>
        </md-list-item>
        <md-list-item href="<?= $addUrl ?>">
            <?= __('Add vocabulary items'); ?>
        </md-list-item>
        <md-list-item href="<?= $addSentencesUrl ?>">
            <?= __('Sentences wanted'); ?>
        </md-list-item>
    </md-list>
</div>