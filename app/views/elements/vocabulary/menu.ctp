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
    <div layout="column" flex>
        <md-button class="md-primary" href="<?= $indexUrl ?>">
            <? __('My vocabulary items'); ?>
        </md-button>
        <md-button class="md-primary" href="<?= $addUrl ?>">
            <? __('Add vocabulary items'); ?>
        </md-button>
        <md-button class="md-primary" href="<?= $addSentencesUrl ?>">
            <? __('Sentences wanted'); ?>
        </md-button>
    </div>
</div>