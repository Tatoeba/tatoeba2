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
<div class="section" layout="column" md-whiteframe="1">
    <h2><? echo __('Vocabulary items'); ?></h2>
    <md-list>
        <md-list-item href="<?= $indexUrl ?>">
            <? echo __('My vocabulary items'); ?>
        </md-list-item>
        <md-list-item href="<?= $addUrl ?>">
            <? echo __('Add vocabulary items'); ?>
        </md-list-item>
        <md-list-item href="<?= $addSentencesUrl ?>">
            <? echo __('Sentences wanted'); ?>
        </md-list-item>
    </md-list>
</div>