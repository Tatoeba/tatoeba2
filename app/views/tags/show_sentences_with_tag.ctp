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

$this->pageTitle = 'Tatoeba - ' . __('Sentences with tag: ', true) . $tagName;

?> 
<div id="main_content">
    <div class="module">
    <h2><?php echo $tagName; ?></h2>
    <?php
    $url = array($tagInternalName);
    $pagination->display($url);
    ?>
        
        <div class="sentencesList" id="sentencesList">
        <?php
        foreach ($allSentences as $i=>$sentence) {
            // this should be done in the controller but this way
            // we avoid another full loop on the sentence Array
            $canUserRemove = CurrentUser::canRemoveTagFromSentence(
                $taggerIds[$i]
            );
            $tags->displaySentence(
                $sentence['Sentence'],
                $sentence['User'],
                $sentence['Translations'],
                $sentence['IndirectTranslations'],
                $canUserRemove,
                $tagId
            );
        }
        ?>
        </div>
    
    <?php
    $pagination->display($url);
    ?>
    
    </div>
</div>
