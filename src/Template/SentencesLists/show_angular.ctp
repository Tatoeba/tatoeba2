<?php
use App\Model\CurrentUser;
use App\Model\Entity\SentencesList;

$this->Html->script('/js/sentences_lists/show.ctrl.js', ['block' => 'scriptBottom']);

$listCount = $this->Paginator->param('count');
$listId = $list['id'];
$listVisibility = $list['visibility'];
$listName = h($list['name']);

$this->set('title_for_layout', $this->Pages->formatTitle($listName));
?>

<div id="annexe_content">
    <?php $this->Lists->displayListsLinks(); ?>

    <div class="section md-whiteframe-1dp">
        <h2><?php echo __('About this list'); ?></h2>
        <?php
        $linkToAuthorProfile = $this->Html->link(
            $user['username'],
            array(
                'controller' => 'user',
                'action' => 'profile',
                $user['username']
            )
        );
        $createdBy = format(
            __('created by {listAuthor}'),
            array('listAuthor' => $linkToAuthorProfile)
        );
        $createdDate = $this->Date->ago($list['created']);
        echo $this->Html->tag('p', $createdBy);
        echo $this->Html->tag('p', $createdDate);
        $numberOfSentencesMsg = format(
            __n(
                /* @translators: number of sentences contained in the list */
                'Contains {n}&nbsp;sentence',
                'Contains {n}&nbsp;sentences',
                $listCount,
                true
            ),
            array('n' => $this->Number->format($listCount))
        );
        echo $this->Html->tag('p', $numberOfSentencesMsg);
        ?>
    </div>


    <?php
    if ($permissions['canEdit']) {
        ?>
        <div class="section md-whiteframe-1dp">
            <h2><?php echo __('Options'); ?></h2>
            <ul class="sentencesListActions">
                <?php
                echo '<p>';
                $this->Lists->displayVisibilityOption($listId, $listVisibility);
                echo '</p>';
                echo '<p>';
                $this->Lists->displayEditableByOptions($listId, $list['editable_by']);
                echo '</p>';
                ?>
            </ul>
        </div>
        <?php
    }
    ?>

    <div class="section md-whiteframe-1dp">
    <h2><?php echo __('Actions'); ?></h2>
    <?php
    $this->Lists->displayTranslationsDropdown($listId, $translationsLang);
    ?>
    <div layout="column" layout-align="end center">
        <?php
        if ($permissions['canEdit']) {
            $this->Lists->displayDeleteButton($listId);
        }

        $this->Lists->displayDownloadLink($listId);
        ?>
    </div>
    </div>

</div>

<div id="main_content">

<section class="md-whiteframe-1dp" ng-controller="SentencesListsShowController as vm" ng-init="vm.initList(<?= $listId ?>)">
    <md-toolbar class="md-hue-2">
        <div class="md-toolbar-tools">
            <h2 flex><?= $listName ?></h2>
            
            <?php if ($permissions['canEdit']) { ?>
            <md-button class="md-icon-button" ng-cloak>
                <md-icon>edit
                    <md-tooltip><?= __('Edit name') ?></md-tooltip>
                <md-icon>
            </md-button>
            <?php } ?>

            <?php if ($permissions['canAddSentences']) { ?>
            <md-button class="md-icon-button" ng-click="vm.showForm = true" ng-cloak>
                <md-icon>add
                    <md-tooltip><?= __('Add sentences'); ?></md-tooltip>
                <md-icon>
            </md-button>
            <?php } ?>
        </div>
    </md-toolbar>

    <?php
    if ($permissions['canAddSentences']) {
        ?>
        <div ng-if="vm.showForm" ng-cloak>
            <?php echo $this->element('sentences/add_sentence_form', [
                'withCloseButton' => true
            ]); ?>

            <div class="hint" style="border-top: 1px solid #ccc; border-bottom: 1px solid #ccc; padding: 10px;">
                <?php
                echo format(
                    __(
                        'NOTE : You can also add existing sentences with this icon {addToListButton} '.
                        '(while <a href="{url}">browsing</a> for instance).', true
                    ),
                    array(
                        'addToListButton' => '<md-icon>list</md-icon>',
                        'url' => $this->Url->build(array(
                            'controller' => 'sentences',
                            'action' => 'show',
                            'random'
                        ))
                    )
                );
                ?>
            </div>
        </div>
        <?php
    }
    ?>
    
    <md-progress-linear ng-if="vm.inProgress"></md-progress-linear>

    <md-content ng-cloak>
    <div class="sortBy" id="sortBy">
     <strong><?php echo __("Sort by:") ?> </strong>
            <?php
            echo $this->Paginator->sort('created', __('date added to list'));
            echo ' | ';
            echo $this->Paginator->sort('sentence_id', __('date created'));
    ?>
    </div>
    
    <?php $this->Pagination->display(); ?>

    <?php
    echo $this->element('sentences_lists/sentence_in_list', [
        'sentenceAndTranslationsParams' => [
            'sentenceData' => 'sentence',
            'duplicateWarning' => __('The sentence you tried to create already exists. The existing sentence was added to your list instead.')
        ],
        'ngRepeat' => 'sentence in vm.sentences'
    ]);

    foreach ($sentencesInList as $item) {
        $sentence = $item->sentence;
        echo $this->element('sentences_lists/sentence_in_list', [
            'sentenceAndTranslationsParams' => [
                'sentence' => $sentence,
                'translations' => $sentence->translations,
                'user' => $sentence->user
            ],
            'sentenceId' => $sentence->id
        ]);
    }
    ?>
    
    <?php $this->Pagination->display(); ?>

    </md-content>
</div>
