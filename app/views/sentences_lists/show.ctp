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

$this->set('title_for_layout', $pages->formatTitle($listName));
?>

<div id="annexe_content">
    <?php $lists->displayListsLinks(); ?>

    <div class="module">
        <h2><?php __('About this list'); ?></h2>
        <?php
        $linkToAuthorProfile = $html->link(
            $list['User']['username'],
            array(
                'controller' => 'user',
                'action' => 'profile',
                $list['User']['username']
            )
        );
        $createdBy = format(
            __('created by {listAuthor}', true),
            array('listAuthor' => $linkToAuthorProfile)
        );
        $createdDate = $date->ago($list['SentencesList']['created']);
        echo $html->tag('p', $createdBy);
        echo $html->tag('p', $createdDate);
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
        echo $html->tag('p', $numberOfSentencesMsg);
        ?>
    </div>


    <?php
    if ($belongsToUser) {
        ?>
        <div class="module">
            <h2><?php __('Options'); ?></h2>
            <ul class="sentencesListActions">
                <?php
                echo '<p>';
                $lists->displayVisibilityOption($listId, $listVisibility);
                echo '</p>';
                echo '<p>';
                $lists->displayEditableByOptions($listId, $editableBy);
                echo '</p>';
                ?>
            </ul>
        </div>
        <?php
    }
    ?>

    <div class="module">
    <h2><?php __('Actions'); ?></h2>
    <?php
    $lists->displayTranslationsDropdown($listId, $translationsLang);
    ?>
    <div layout="column" layout-align="end center" layout-padding>
        <?php
        if ($belongsToUser) {
            $lists->displayDeleteButton($listId);
        }

        if ($canDownload) {
            $lists->displayDownloadLink($listId);
        } else {
            echo $downloadMessage;
        }
        ?>
    </div>
    </div>
    
</div>

<div id="main_content">
    <div class="module">
    <?php
    $class = '';
    if ($belongsToUser) {
        $javascript->link(JS_PATH . 'jquery.jeditable.js', false);
        $javascript->link(JS_PATH . 'sentences_lists.edit_name.js', false);

        $class = 'editable-list-name';

        $editImage = $this->Images->svgIcon(
            'edit',
            array(
                'alt'=> __('Edit', true),
                'title'=> __('Edit name', true),
                'width' => 15,
                'height' => 15,
                'class' => 'edit-icon'
            )
        );
    }

    echo $html->tag('h2', $listName, array(
        'id'    => "l$listId",
        'class' => $class,
        'data-submit'  => __('OK', true),
        'data-cancel'  => __('Cancel', true),
        'data-tooltip' => __('Click to edit...', true),
    ));

    if ($belongsToUser && $editableBy !== 'no_one') {
        echo $html->div('edit-list-name', $editImage);
        $lists->displayAddSentenceForm($listId);
    }

    $url = array($listId, $translationsLang);
    $pagination->display($url);

    ?>
    
    <div class="sentencesList" id="sentencesList"
         data-list-id="<?php echo $listId; ?>">
    <?php
    foreach ($sentencesInList as $sentence) {
        $lists->displaySentence($sentence['Sentence'], $canRemoveSentence);
    }
    ?>
    </div>
    
    <?php
    $pagination->display($url);
    ?>
    
    </div>
</div>
