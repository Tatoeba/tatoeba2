<?php
$addUrl = $html->url(
    array(
        'controller' => 'vocabulary',
        'action' => 'add'
    )
);
$indexUrl = $html->url(
    array(
        'controller' => 'vocabulary',
        'action' => 'of',
        CurrentUser::get('username')
    )
);
$addSentencesUrl = $html->url(
    array(
        'controller' => 'vocabulary',
        'action' => 'add_sentences'
    )
);
?>
<div class="section" layout="column" md-whiteframe="1">
    <h2><? __('Vocabulary items'); ?></h2>
    <md-list>
        <md-list-item href="<?= $indexUrl ?>">
            <? __('My vocabulary items'); ?>
        </md-list-item>
        <md-list-item href="<?= $addUrl ?>">
            <? __('Add vocabulary items'); ?>
        </md-list-item>
        <md-list-item href="<?= $addSentencesUrl ?>">
            <? __('Sentences wanted'); ?>
        </md-list-item>
    </md-list>
</div>