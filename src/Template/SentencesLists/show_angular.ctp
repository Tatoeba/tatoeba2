<?php
use App\Model\CurrentUser;
use App\Model\Entity\SentencesList;

$this->Html->script('/js/sentences_lists/show.ctrl.js', ['block' => 'scriptBottom']);

$listId = $list['id'];
$listVisibility = $list['visibility'];
$listName = h($list['name']);

$listData = [
    'id' => $list['id'],
    'name' => $list['name']
];
$listJSON = h(json_encode($listData));
$listJSON = str_replace('{{', '\{\{', $listJSON); // avoid interpolation by AngularJS
$this->set('title_for_layout', $this->Pages->formatTitle($listName));
?>

<div id="annexe_content">
    <?php $this->Lists->displayFilterByLangDropdown($listId, $filterLanguage, $translationsLang); ?>
    <?php
    $this->Lists->displayTranslationsDropdown($listId,$filterLanguage, $translationsLang);
    ?>
    <?php
    if ($permissions['canEdit']) {
        ?>
        <div class="section md-whiteframe-1dp" ng-controller="optionsCtrl">
            <?php /* @translators: header text in the side bar of a list page (noun) */ ?>
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
                $total,
                true
            ),
            array('n' => $this->Number->format($total))
        );
        echo $this->Html->tag('p', $numberOfSentencesMsg);
        ?>
    </div>

    <?php $this->Lists->displayListsLinks(); ?>

</div>

<div id="main_content">

<section class="md-whiteframe-1dp" ng-controller="SentencesListsShowController as vm" ng-init="vm.initList(<?= $listJSON ?>)">
    <md-toolbar class="md-hue-2">
        <div class="md-toolbar-tools">
            <h2 ng-cloak flex>{{vm.list.currentName}}</h2>

            <?= $this->element('sentences/expand_all_menus_button'); ?>

            <?php
                $downloadUrl = $this->Url->build([
                    'controller' => 'sentences_lists',
                    'action' => 'download',
                ]);
            ?>
            <md-button class="md-icon-button" ng-cloak
                       ng-href="<?= $downloadUrl ?>/{{vm.list.id}}">
                <md-icon>get_app
                    <md-tooltip><?= __('Download this list') ?></md-tooltip>
                </md-icon>
            </md-button>

            <?php
                if ($permissions['canEdit']) {
                    $deleteUrl = $this->Url->build([
                        'controller' => 'sentences_lists',
                        'action' => 'delete',
                    ]);
                ?>
                <md-button class="md-icon-button" ng-cloak
                           ng-href="<?= $deleteUrl; ?>/{{vm.list.id}}"
                           onclick="return confirm('<?= __('Are you sure?') ?>');">
                    <md-icon>delete
                        <md-tooltip><?= __('Delete this list') ?></md-tooltip>
                    </md-icon>
                </md-button>
            <?php } ?>

            <?php if ($permissions['canEdit']) { ?>
            <md-button class="md-icon-button" ng-cloak ng-click="vm.editName()">
                <md-icon>edit
                    <md-tooltip><?= __('Edit name') ?></md-tooltip>
                </md-icon>
            </md-button>
            <?php } ?>

            <?php if ($permissions['canAddSentences']) { ?>
            <md-button class="md-icon-button" ng-click="vm.showForm = true" ng-cloak>
                <md-icon>add
                    <md-tooltip><?= __('Add sentences'); ?></md-tooltip>
                </md-icon>
            </md-button>
            <?php } ?>

            <?php 
                $options = array(
                    /* @translators: sort option in a list page */
                    array('param' => 'created', 'direction' => 'desc', 'label' => __('Most recently added')),
                    /* @translators: sort option in a list page */
                    array('param' => 'created', 'direction' => 'asc', 'label' => __('Least recently added')),
                    /* @translators: sort option in a list page */
                    array('param' => 'sentence_id', 'direction' => 'desc', 'label' => __('Newest sentences')),
                    /* @translators: sort option in a list page */
                    array('param' => 'sentence_id', 'direction' => 'asc', 'label' => __('Oldest sentences'))
                );
                echo $this->element('sort_menu', array('options' => $options));
            ?>

        </div>
    </md-toolbar>

    <?php if ($permissions['canEdit']) { ?>
        <form id="list-name-form" layout="column" ng-if="vm.showEditNameForm" ng-cloak>
            <md-input-container>
                <label><?= __('List name'); ?></label>
                <input id="edit-name-input" ng-model="vm.list.name" ng-enter="vm.saveListName()" ng-escape="vm.showEditNameForm = false"></input>
            </md-input-container>
            
            <div layout="row" layout-align="end">
                <md-button class="md-raised" ng-click="vm.showEditNameForm = false">
                    <?php /* @translators: cancel button of list name edition form (verb) */ ?>
                    <?= __('Cancel') ?>
                </md-button>
                <md-button class="md-raised md-primary" ng-click="vm.saveListName()">
                    <?php /* @translators: submit button of list name edition form (verb) */ ?>
                    <?= __('Save') ?>
                </md-button>
            </div>
        </form>
    <?php } ?>

    <?php
    if ($permissions['canAddSentences']) {
        ?>
        <div ng-if="vm.showForm" ng-cloak>
            <?php echo $this->element('sentences/add_sentence_form', [
                'withCloseButton' => true
            ]); ?>
        </div>
        <?php
    }
    ?>
    
    <md-progress-linear ng-if="vm.inProgress"></md-progress-linear>

    <md-content ng-cloak>
    <?php
    if (count($sentencesInList) == 0) {
        ?>
        <div class="no-sentence-info" ng-if="!vm.showForm && vm.sentences.length === 0">
            <p>
                <?php
                    echo format($filterLanguage=='und'?
                    __(
                        'This list is empty.'
                    ):__(
                        'This list does not contain any sentences in {language}.'
                    )
                        ,
                        ['language'=> $this->Languages->codeToNameAlone($filterLanguage)]
                    );
                ?>
            </p>
            <?php
            if ($permissions['canAddSentences']){
                ?>
                <div class="hint">
                    <?php
                        echo format(
                            __(
                                'You can create new sentences directly in this list by clicking on the '.
                                '{addSentenceButton} icon in the header of this section.', true
                            ),
                            ['addSentenceButton' => '<md-icon>add</md-icon>']
                        );
                    ?>
                </div>
                <div class="hint">
                    <?php
                        echo format(
                            __('You can also add existing sentences to this list, from other pages, by clicking on '.
                                'the {addToListButton} icon in the menu of the sentence.', true
                            ),
                            ['addToListButton' => '<md-icon>list</md-icon>']
                        );
                    ?>
                </div>
                <?php
            }?>
        </div>
        <?php
    }

    $this->Pagination->warnLimitedResults($totalLimit, $total);

    $this->Pagination->display();

    if ($permissions['canAddSentences']) {
        echo $this->element('sentences_lists/sentence_in_list', [
            'sentenceAndTranslationsParams' => [
                'sentenceData' => 'sentence',
                'directTranslationsData' => 'sentence.translations[0]',
                'indirectTranslationsData' => 'sentence.translations[1]',
                'duplicateWarning' => __('The sentence you tried to create already exists. The existing sentence was added to your list instead.')
            ],
            'ngRepeat' => 'sentence in vm.sentences',
            'canRemove' => $permissions['canRemoveSentences']
        ]);
    }

    foreach ($sentencesInList as $item) {
        $sentence = $item->sentence;
        echo $this->element('sentences_lists/sentence_in_list', [
            'sentenceAndTranslationsParams' => [
                'sentence' => $sentence,
                'translations' => $sentence->translations,
                'translationLang' => $translationsLang,
                'user' => $sentence->user
            ],
            'sentenceId' => $sentence->id,
            'canRemove' => CurrentUser::isMember() && $permissions['canRemoveSentences']
        ]);
    }
    ?>
    
    <?php $this->Pagination->display(); ?>

    </md-content>
</div>
