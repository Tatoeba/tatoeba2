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

 
$this->set('title_for_layout', $pages->formatTitle($listName));
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
                $listId,
                'show',
                $isListPublic
            );
        }
        ?>
    </ul>
    </div>


    <div class="module">
    <h2><?php __('Download'); ?></h2>
    <?php
    if ($canDownload) {
        $lists->displayDownloadLink($listId);
    } else {
        echo $downloadMessage;
    }
    ?>
    </div>
    
</div>

<div id="main_content">
    <div class="module">
    
    <h2 id="l<?php echo $listId; ?>">
    <?php echo $listName; ?>
    </h2>
    
    <?php
    $url = array($listId, $translationsLang);
    $pagination->display($url);
    ?>
    
    <div class="sentencesList" id="sentencesList">
    <?php
    foreach ($sentencesInList as $item) {
        $sentence = $item['Sentence'];
        $transcrs = $sentence['Transcription'];
        $translations = array();
        if (!empty($sentence['Translation'])) {
            $translations = $sentence['Translation'];
        }
        $canUserEdit = false;
        $lists->displaySentence(
            $sentence,
            $transcrs,
            $translations,
            $canUserEdit
        );
    }
    ?>
    </div>
    
    <?php
    $pagination->display($url);
    ?>
    
    </div>
</div>
