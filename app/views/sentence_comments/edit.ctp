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

$this->pageTitle = 'Tatoeba - ' . __('Edit Comment', true);


?>

<div id="annexe_content">
    
    <?php 
    $tags->displayTagsModule(
        $tagsArray,
        $sentenceComment['SentenceComment']['sentence_id']
    );
    ?>
    
    <div class="module">
        <?php
        echo '<h2>';
        __('Logs');
        echo '</h2>';
        
        //$contributions = $sentence['Contribution'];
        if (!empty($contributions)) {
            echo '<div id="logs">';
            foreach ($contributions as $contribution) {
                $logs->annexeEntry(
                    $contribution['Contribution'], 
                    $contribution['User']
                );
            }
            echo '</div>';
        } else {
            echo '<em>'. __('There is no log for this sentence', true) .'</em>';
        }
        ?>
    </div> 
    
    <div class="module">
        <h2><?php __('Report mistakes'); ?> </h2>
        <p>
            <?php
            __('Do not hesitate to post a comment if you see a mistake!');
            ?>
        </p>
        <p>
            <?php
            __(
                'NOTE : If the sentence does not belong to anyone and you know how '.
                'to correct the mistake, feel free to correct it without posting '.
                'any comment. You will have to adopt the sentence '.
                'before you can edit it.'
            );
            ?>
        </p>
    </div>
</div>

<div id="main_content">
    

<div class="module"> 
    <?php
    echo "<h2>";
    echo sprintf(__('Sentence nº%s', true), $sentenceComment['Sentence']['id']);
    echo "</h2>";
    ?>
    <div class="sentences_set">
    <?php
    $sentences->displayMainSentence(
        $sentenceComment['Sentence'],
        $sentenceComment['Sentence']['user_id'],
        "mainSentence"
    );
    ?>
    </div>
</div>
    
<div class="module">
    <?php
    echo '<h2>';
    __('Edit Comment');
    echo '</h2>';
    
    
    echo '<ol class="comments">';
    $comments->displaySentenceCommentEditForm(
        $sentenceComment['SentenceComment'],
        $sentenceComment['User'],
        $sentenceComment['Sentence'],
        $commentPermissions
    );
    echo '</ol>';
    ?>
</div>
    
</div>