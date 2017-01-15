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

$this->set('title_for_layout', $this->Pages->formatTitle($listName));
?>

<div id="annexe_content">
    <?php $this->Lists->displayListsLinks(); ?>

    <div class="module">
        <h2><?php echo __('About this list'); ?></h2>
        <?php
        $linkToAuthorProfile = $this->Html->link(
            $list['User']['username'],
            array(
                'controller' => 'user',
                'action' => 'profile',
                $list['User']['username']
            )
        );
        $createdBy = format(
            __('created by {listAuthor}'),
            array('listAuthor' => $linkToAuthorProfile)
        );
        $createdDate = $this->Date->ago($list['SentencesList']['created']);
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
    if ($belongsToUser) {
        ?>
        <div class="module">
            <h2><?php echo __('Options'); ?></h2>
            <ul class="sentencesListActions">
                <?php
                echo '<p>';
                $this->Lists->displayVisibilityOption($listId, $listVisibility);
                echo '</p>';
                echo '<p>';
                $this->Lists->displayEditableByOptions($listId, $editableBy);
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
        if ($belongsToUser) {
            $this->Lists->displayDeleteButton($listId);
        }

        if ($canDownload) {
            $this->Lists->displayDownloadLink($listId);
        } else {
            echo $downloadMessage;
        }
        ?>
    </div>
    </div>
    
</div>

<div id="main_content">
    <div class="section">
    <?php
    $class = '';
    if ($belongsToUser) {
        $this->Js->link(JS_PATH . 'jquery.jeditable.js', false);
        $this->Js->link(JS_PATH . 'sentences_lists.edit_name.js', false);

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

    echo $this->Html->tag('h2', $listName, array(
        'id'    => "l$listId",
        'class' => $class,
        'data-submit'  => __('OK'),
        'data-cancel'  => __('Cancel'),
        'data-tooltip' => __('Click to edit...'),
    ));

    if ($belongsToUser && $editableBy !== 'no_one') {
        echo $this->Html->div('edit-list-name', $editImage);
        $this->Lists->displayAddSentenceForm($listId);
    }

    $url = array($listId, $translationsLang);
    $this->Pagination->display($url);

    ?>
    
    <div class="sortBy" id="sortBy">
     <strong><?php echo __("Sort by:") ?> </strong>
            <?php 
            echo $this->Paginator->sort(__("date added"), 'created');
            echo " | ";
            echo $this->Paginator->sort(__("sentence id"), 'sentence_id');
    ?>
   
    </div>
    <div class="sentencesList" id="sentencesList"
         data-list-id="<?php echo $listId; ?>">
    <?php
    if (!CurrentUser::isMember() || CurrentUser::getSetting('use_new_design')) {
        foreach ($sentencesInList as $sentence) {
            $translations = isset($sentence['Sentence']['Translation']) ?
                $sentence['Sentence']['Translation'] :
                array();
            echo $this->element(
                'sentences/sentence_and_translations',
                array(
                    'sentence' => $sentence['Sentence'],
                    'translations' => $translations,
                    'user' => $sentence['Sentence']['User']
                )
            );
        }
    } else {
        foreach ($sentencesInList as $sentence) {
            $this->Lists->displaySentence($sentence['Sentence'], $canRemoveSentence);
        }
    }
    ?>
    </div>
    
    <?php
    $this->Pagination->display($url);
    ?>
    
    </div>
</div>
