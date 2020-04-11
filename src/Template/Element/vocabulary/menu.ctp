<?php
use App\Model\CurrentUser;

$filteredLanguage = $this->request->getSession()->read('vocabulary_requests_filtered_lang');

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
        'action' => 'add_sentences', 
        $filteredLanguage 
    )
);
?>
<md-list class="annexe-menu md-whiteframe-1dp" ng-cloak>
    <md-subheader><?= __('Vocabulary requests'); ?></md-subheader>
    
    <md-list-item href="<?= $indexUrl ?>">
        <p>
            <md-icon>keyboard_arrow_right</md-icon>
            <?= __('My vocabulary requests'); ?>
        </p>
    </md-list-item>
    <md-list-item href="<?= $addUrl ?>">
        <p>
            <md-icon>keyboard_arrow_right</md-icon>
            <?= __('Add vocabulary requests'); ?>
        </p>
    </md-list-item>
    <md-list-item href="<?= $addSentencesUrl ?>">
        <p>
            <md-icon>keyboard_arrow_right</md-icon>
            <?= __('Existing vocabulary requests'); ?>
        </p>
    </md-list-item>
</md-list>