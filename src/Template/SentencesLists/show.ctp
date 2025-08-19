<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
use App\Model\CurrentUser;
use App\Model\Entity\SentencesList;

// Just to make sure jQuery is loaded before the rest of the lists JS scripts
$this->Sentences->javascriptForAJAXSentencesGroup();

$this->Html->script(
    JS_PATH . 'sentences_lists.remove_sentence_from_list.js', array('block' => 'scriptBottom')
);

$listId = $list['id'];
$listVisibility = $list['visibility'];
$listName = h($list['name']);

$this->set('title_for_layout', $this->Pages->formatTitle($listName));
?>

<div id="annexe_content">
    <?php $this->Lists->displayFilterByLangDropdown($listId, $filterLanguage, $translationsLang); ?>
    <?php
    $this->Lists->displayTranslationsDropdown($listId, $filterLanguage, $translationsLang);
    ?>
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
                $total,
                true
            ),
            array('n' => $this->Number->format($total))
        );
        echo $this->Html->tag('p', $numberOfSentencesMsg);
        ?>
    </div>


    <?php
    if ($permissions['canEdit']) {
        ?>
        <div class="section md-whiteframe-1dp" ng-controller="optionsCtrl">
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
    <div class="section">
    <?php
    $class = '';
    if ($permissions['canEdit']) {
        $this->Html->script('sentences_lists.edit_name.js', ['block' => 'scriptBottom']);        

        $class = 'editable-list-name';

        $editImage = $this->Images->svgIcon(
            'edit',
            array(
                /* @translators: edit button for list name (verb) */
                'alt'=> __('Edit'),
                'title'=> __('Edit name'),
                'width' => 15,
                'height' => 15,
                'class' => 'edit-icon'
            )
        );
    }

    echo $this->Html->tag(
        'h2',
        $this->safeForAngular($listName),
        [
            'id'    => "l$listId",
            'class' => $class,
            /* @translators: submit button of list name edition form */
            'data-submit'  => __('OK'),
            /* @translators: cancel button of list name edition form (verb) */
            'data-cancel'  => __('Cancel'),
            'data-tooltip' => __('Click to edit...'),
        ]
    );

    if ($permissions['canAddSentences']) {
        if($permissions['canEdit']){
            echo $this->Html->div('edit-list-name', $editImage);
        }
        $this->Lists->displayAddSentenceForm($listId);
    }
    ?>

    <div class="sortBy" id="sortBy">
     <strong><?php echo __("Sort by:") ?> </strong>
            <?php
            echo $this->Paginator->sort('id', __('date added to list'));
            echo ' | ';
            echo $this->Paginator->sort('sentence_id', __('date created'));
    ?>

    </div>
    <div class="sentencesList" id="sentencesList"
         data-list-id="<?php echo $listId; ?>">
    <?php
    $this->Pagination->warnLimitedResults($totalLimit, $total);

    $this->Pagination->display();

    foreach ($sentencesInList as $item) {
        $sentence = $item->sentence;
        $this->Lists->displaySentence(
            $sentence, $permissions['canRemoveSentences']
        );
    }
    ?>
    </div>

    <?php
    $this->Pagination->display();
    ?>

    </div>
</div>
