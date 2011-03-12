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

if (isset($sentence)) {
    $sentenceId = $sentence['Sentence']['id'];
    $sentenceLang = $sentence['Sentence']['lang'];
    $sentenceText = Sanitize::html($sentence['Sentence']['text']);
    
    $languageName = $languages->codeToName($sentenceLang);
    $title = sprintf(__('%s example sentence: ', true), $languageName);
    $this->pageTitle = $title . $sentenceText;

    $html->meta(
        'description', 
        sprintf(
            __(
                "Browse translated example sentences. ".
                "This page shows translations and information about the sentence: %s"
                , true
            ),
            $sentenceText
        ), 
        array(), 
        false
    );
} else {
    // Case where the sentence has been deleted
    $this->pageTitle = __('Sentence does not exist: ', true).$this->params['pass'][0];
}


// navigation (previous, random, next)
$navigation->displaySentenceNavigation(
    $sentenceId,
    $nextSentence,
    $prevSentence
);
?>

<div id="annexe_content">
    <?php $tags->displayTagsModule($tagsArray, $sentenceId); ?>
    
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
    
    <div class="module">
        <h2><?php __('Remember please'); ?> </h2>
        <p>
        <?php
        __(
            'No off-topic, no trolling, no personal attacks. '.
            'You have private messages for that. Thank you.'
        );
        ?>
        </p>
    </div>
</div>

<div id="main_content">
    <div class="module">
        <?php
        if (isset($sentence)) {
        ?>
            <h2>
            <?php 
            echo sprintf(__('Sentence nº%s', true), $sentenceId); 
            ?>
            </h2>            
            
            <?php
            // display sentence and translations
            $sentences->displaySentencesGroup(
                $sentence['Sentence'],
                $translations,
                $sentence['User'],
                $indirectTranslations
            );
            
        } else {
            
            echo '<h2>' .
                sprintf(__('Sentence nº%s', true), $this->params['pass'][0]) .
                '</h2>';

            echo '<div class="error">';
                echo sprintf(
                    __(
                        'There is no sentence with id %s', 
                        true
                    ), 
                    $this->params['pass'][0]
                );
            echo '</div>';
        }
        ?>
    </div>

    <div class="module">
        <?php
        echo '<h2>';
        __('Comments');
        echo '</h2>';
        
        if (!empty($sentenceComments)) {
            $sentenceInfo = array('id' => $sentenceId);
            echo '<ol class="comments">';
            foreach ($sentenceComments as $i=>$comment) {
                $comments->displaySentenceComment(
                    $comment['SentenceComment'],
                    $comment['User'],
                    $sentenceInfo,
                    $commentsPermissions[$i]
                );
            }
            echo '</ol>';
        
        } else {
            echo '<em>' . __('There are no comments for now.', true) .'</em>';
        }
        ?>
        
        
    </div>
    
    <?php
    if (isset($sentence)) {
        ?>
        <div class="module">
        
        <h2><?php __('Add a comment'); ?></h2>
        
        <?php
        if ($session->read('Auth.User.id')) {
            $comments->displayCommentForm(
                $sentence['Sentence']['id'], 
                $sentence['Sentence']['text']
            );
        } else {
            echo '<p>';
            echo sprintf(
                __(
                    'You need to be logged in to add a comment. If you are '.
                    'not registered, you can <a href="%s">register here</a>.', 
                    true
                ),
                $html->url(array("controller"=>"users", "action"=>"register"))
            );
            echo '</p>';
        }
        ?>
        </div>
        <?php
    }
    ?>
</div>

