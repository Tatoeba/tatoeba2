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
$isListPublic = ($list['SentencesList']['is_public'] == 1);
$belongsToUser = $session->read('Auth.User.id') == $listOwnerId;
$canUserEdit = $isListPublic || $belongsToUser;

 
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
        
        if ($canUserEdit) {
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
    if ($session->read('Auth.User.id') == $listOwnerId) {
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
    ?>
    <h2 id="l<?php echo $listId; ?>" class="<?php echo $class; ?>">
    <?php echo $listName ?>
    </h2>

    <?php
    if ($canUserEdit) {
        $lists->displayAddSentenceForm();
    }
    ?>
    
    
    <?php
    if (!empty($list['Sentence'])) {
        ?>
        <ul class="sentencesList">
        <?php
        if ($translationsLang == 'und') {
            $translationsLang = null;
        }
        foreach ($list['Sentence'] as $sentence) {
            $lists->displaySentence($sentence, $translationsLang, $canUserEdit);
        }
        ?>
        </ul>
        <?php
    } else {
        __('This list does not have any sentence');
    }
    ?>
    
    </div>
</div>
