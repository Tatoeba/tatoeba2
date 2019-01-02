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
 * @link     http://tatoeba.org
 */
use App\Model\CurrentUser;
use App\Model\Entity\SentencesList;

$this->Sentences->javascriptForAJAXSentencesGroup(false);

$listCount = $this->Paginator->counter("{{count}}");
$listId = $list['id'];
$listVisibility = $list['visibility'];
$listName = $list['name'];
$maxCountForDownload = SentencesList::MAX_COUNT_FOR_DOWNLOAD;

$this->set('title_for_layout', $this->Pages->formatTitle($listName));
?>

<div id="annexe_content">
    <?php $this->Lists->displayListsLinks(); ?>

    <div class="module">
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
            array('n' => $listCount)
        );
        echo $this->Html->tag('p', $numberOfSentencesMsg);
        ?>
    </div>


    <?php
    if ($permissions['canEdit']) {
        ?>
        <div class="module">
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

    <div class="module">
    <h2><?php echo __('Actions'); ?></h2>
    <?php
    $this->Lists->displayTranslationsDropdown($listId, $translationsLang);
    ?>
    <div layout="column" layout-align="end center" layout-padding>
        <?php
        if ($permissions['canEdit']) {
            $this->Lists->displayDeleteButton($listId);
        }

        if ($permissions['canDownload']) {
            $this->Lists->displayDownloadLink($listId);
        } else {
            $firstSentence = __n('The download feature has been disabled for '.
                'this list because it contains a sentence.',
                'The download feature has been disabled for '.
                'this list because it contains {n}&nbsp;sentences.',
                $listCount, true);

            $secondSentence = __n('Only lists containing one sentence or fewer can be '.
                'downloaded. If you can edit the list, you may want '.
                'to split it into multiple lists.',
                'Only lists containing {max} or fewer sentences can be '.
                'downloaded. If you can edit the list, you may want '.
                'to split it into multiple lists.',
                $maxCountForDownload, true);

            echo $this->Html->tag(
                'div', format($firstSentence, array('n' => $listCount))
            );
            echo $this->Html->tag(
                'div', format($secondSentence, array('max' => $maxCountForDownload))
            );
        }
        ?>
    </div>
    </div>

</div>

<div id="main_content">
    <div class="section">
    <?php
    $class = '';
    if ($permissions['canEdit']) {
        $this->Html->script(JS_PATH . 'jquery.jeditable.js', ['block' => true]);
        $this->Html->script(JS_PATH . 'sentences_lists.edit_name.js', ['block' => true]);

        $class = 'editable-list-name';

        $editImage = $this->Images->svgIcon(
            'edit',
            array(
                'alt'=> __('Edit'),
                'title'=> __('Edit name'),
                'width' => 15,
                'height' => 15,
                'class' => 'edit-icon'
            )
        );
    }

    echo $this->Html->tag('h2', $listName, [
        'id'    => "l$listId",
        'class' => $class,
        'data-submit'  => __('OK'),
        'data-cancel'  => __('Cancel'),
        'data-tooltip' => __('Click to edit...'),
    ]);

    if ($permissions['canAddSentences']) {
        echo $this->Html->div('edit-list-name', $editImage);
        $this->Lists->displayAddSentenceForm($listId);
    }

    $this->Pagination->display();

    ?>

    <div class="sortBy" id="sortBy">
     <strong><?php echo __("Sort by:") ?> </strong>
            <?php
            echo $this->Paginator->sort('created', __('date added to list'));
            echo ' | ';
            echo $this->Paginator->sort('sentence_id', __('date created'));
    ?>

    </div>
    <div class="sentencesList" id="sentencesList"
         data-list-id="<?php echo $listId; ?>">
    <?php
    if (!CurrentUser::isMember() || CurrentUser::getSetting('use_new_design')) {
        foreach ($sentencesInList as $item) {
            $sentence = $item->sentence;
            $translations = $item->translations;
            echo $this->element(
                'sentences/sentence_and_translations',
                array(
                    'sentence' => $sentence,
                    'translations' => $translations,
                    'user' => $sentence->user
                )
            );
        }
    } else {
        foreach ($sentencesInList as $sentence) {
            $this->Lists->displaySentence(
                $sentence, $permissions['canRemoveSentences']
            );
        }
    }
    ?>
    </div>

    <?php
    $this->Pagination->display();
    ?>

    </div>
</div>
