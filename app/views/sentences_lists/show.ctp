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
 
$listId = $list['SentencesList']['id'];
$listName = $list['SentencesList']['name'];
$listOwnerId = $list['SentencesList']['user_id'];
$isAuthenticated = $session->read('Auth.User.id');
$isListPublic = ($list['SentencesList']['is_public'] == 1);
$belongsToUser = $session->read('Auth.User.id') == $listOwnerId;
$canUserEdit = $isAuthenticated && ($isListPublic || $belongsToUser);

 
$this->pageTitle = 'Tatoeba - ' . $listName;
?>

<div id="annexe_content">
    <div class="module">
    <h2><?php __('Actions'); ?></h2>
    <ul class="sentencesListActions">
        <?php
        $lists->displayPublicActions(
            $listId, $translationsLang, 'show'
        );
        
        if ($belongsToUser) {
            $lists->displayRestrictedActions(
                $listId, $isListPublic
            );
        }
        ?>
    </ul>
    </div>


    <div class="module">
    <h2><?php __('Download'); ?></h2>
    <?php
    $lists->displayDownloadLink($listId);
    ?>
    </div>
    
    
    <?php
    if ($canUserEdit) {
        ?>
        <div class="module">
        
        <h2><?php __('Tips'); ?></h2>
    
        <p>
        <?php __('You can change the name of the list by clicking on it.'); ?>
        </p>
    
    
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
        <?php
    }
    ?>
    
</div>

<div id="main_content">
    <div class="module">
    <?php
    $class = '';
    if ($canUserEdit) {
        $javascript->link('sentences_lists.remove_sentence_from_list.js', false);
        $javascript->link('sentences_lists.jEditable.js', false);
        $javascript->link('sentences_lists.edit_name.js', false);
        
        $class = 'editable editableSentencesListName';
        ?>
        <script type='text/javascript'>
        $(document).ready(function() {
            $('#sentencesList').data(
                'id', <?php echo $listId; ?>
            );
        });
        </script>
        <?php
    }
    
    echo '<h2 id="l'.$listId.'" class="'.$class.'">';
    echo $listName;
    echo '</h2>';

    if ($canUserEdit) {
        $lists->displayAddSentenceForm($listId);
    }
    ?>
    
    <div class="sentencesList" id="sentencesList">
    <?php
    foreach ($list['Sentence'] as $sentence) {
        $translations = array();
        if (!empty($sentence['Translation'])) {
            $translations = $sentence['Translation'];
        }
        $lists->displaySentence($sentence, $translations, $canUserEdit);
    }
    ?>
    </div>
    
    </div>
</div>
