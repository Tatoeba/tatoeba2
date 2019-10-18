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
<md-list class="annexe-menu md-whiteframe-1dp" ng-cloak>
    <md-subheader><?= __('Vocabulary items'); ?></md-subheader>
    
    <md-list-item href="<?= $indexUrl ?>">
        <p>
            <md-icon>keyboard_arrow_right</md-icon>
            <?= __('My vocabulary items'); ?>
        </p>
    </md-list-item>
    <md-list-item href="<?= $addUrl ?>">
        <p>
            <md-icon>keyboard_arrow_right</md-icon>
            <?= __('Add vocabulary items'); ?>
        </p>
    </md-list-item>
    <md-list-item href="<?= $addSentencesUrl ?>">
        <p>
            <md-icon>keyboard_arrow_right</md-icon>
            <?= __('Sentences wanted'); ?>
        </p>
    </md-list-item>
</md-list>