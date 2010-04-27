<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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

$javascript->link('sentences_lists.remove_sentence_from_list.js', false);
$javascript->link('sentences_lists.edit_name.js', false);
$javascript->link('sentences_lists.add_new_sentence_to_list.js', false);
$javascript->link('jquery.jeditable.js', false);

$listId = $list['SentencesList']['id'];
$listName = $list['SentencesList']['name'];
$listOwner = $list['SentencesList']['user_id'];
$isListPublic = ($list['SentencesList']['is_public'] == 1);

$this->pageTitle = 'Tatoeba - ' . $listName;
?>

<div id="annexe_content">
    <div class="module">
    <h2><?php __('Actions'); ?></h2>
    <ul class="sentencesListActions">
        <?php
        $lists->displayPublicActions(
            $listId, $translationsLang, 'edit'
        );
        
        if ($session->read('Auth.User.id') == $listOwner) {
            $lists->displayRestrictedActions(
                $listId, $isListPublic
            );
        }
        ?>
    </ul>
    </div>

    <div class="module">
    <h2><?php __('Printable versions'); ?></h2>
    <?php
    $lists->displayLinksToPrintableVersions($listId, $translationsLang);
    ?>
    </div>


    <div class="module">
    <h2><?php __('Tips'); ?></h2>
    <?php
    if ($session->read('Auth.User.id') == $listOwner) {
        ?>
        <p>
        <?php __('You can change the name of the list by clicking on it.'); ?>
        </p>
        <?php
    }
    ?>
    
    <p>
    <?php
    __('You can remove a sentence from the list by clicking on the X icon.'); 
    ?>
    </p>
    
    <p>
    <?php
    __(
        'Removing a sentence will not delete it. '.
        'The sentence will just not be part of the list anymore.'
    );
    ?>
     </p>
    </div>
</div>



<div id="main_content">
    <div class="module">
    <?php
    $class = '';
    if ($session->read('Auth.User.id') == $listOwner) {
        $class = 'editable editableSentencesListName';
    }
    ?>
    <h2 id="l<?php echo $listId; ?>" class="<?php echo $class; ?>">
        <?php $list['SentencesList']['name']; ?>
    </h2>
    
    <div id="newSentenceInList">
    <?php
    echo $form->input(
        'text',
        array(
            "label" => __('Add a sentence to this list : ', true)
        )
    );
    echo $form->button(
        'OK', array(
            "id" => "submitNewSentenceToList"
        )
    );
    ?>
    </div>

    <p>
    <?php
    echo sprintf(
        __(
            'NOTE : You can also add existing sentences with this icon %s '.
            '(while <a href="%s">browsing</a> for instance).', true
        ),
        $html->image('add_to_list.png'),
        $html->url(array("controller"=>"sentences", "action"=>"show", "random"))
    );
    ?>
    </p>


    <div class="sentencesListLoading" style="display:none">
    <?php echo $html->image('loading.gif'); ?>
    </div>
    
    <?php
    // TODO Use jQuery.data
    ?>
    <span class="sentencesListId" id="_<?php $listId; ?>" />
    
    <ul class="sentencesList editMode">
    <?php
    if (count($list['Sentence']) > 0) {
        foreach ($list['Sentence'] as $sentence) {
            ?>
            <li id="sentence<?php echo $sentence['id']; ?>">
           <?php
            // delete button
            echo '<span class="options">';
            echo '<a id="_'.$sentence['id'].'" class="removeFromListButton">';
            echo $html->image('close.png');
            echo '</a>';
            echo '</span>';

            // display sentence
            if ($translationsLang != 'und') {
                $sentences->displaySentenceInList($sentence, $translationsLang);
            } else {
                $sentences->displaySentenceInList($sentence);
            }
            ?>
            </li>
            <?php
        }
    }
    ?>
    </ul>
    
    </div>
</div>
