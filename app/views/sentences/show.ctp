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

$javascript->link('jquery.scrollTo.min.js', false);
$javascript->link('sentences.logs.js', false);

if (isset($sentence)) {
    $sentenceId = $sentence['Sentence']['id'];
    $sentenceLang = $sentence['Sentence']['lang'];
    $sentenceText = $sentence['Sentence']['text'];
    $sentenceCorrectness = $sentence['Sentence']['correctness'];
    $sentenceHasAudio = $sentence['Sentence']['hasaudio'];
    
    $languageName = $languages->codeToNameToFormat($sentenceLang);
    $title = format(__('{language} example sentence: {sentence}', true),
                    array('language' => $languageName, 'sentence' => $sentenceText));
    $this->set('title_for_layout', $pages->formatTitle($title));

    $html->meta(
        'description', 
        format(
            __(
                "Browse translated example sentences. ".
                "This page shows translations and information about the sentence: {sentenceText}"
                , true
            ),
            compact('sentenceText')
        ), 
        array('inline' => false)
    );
} else if (isset($searchProblem)) {
    if($searchProblem == 'error') {
        $this->set('title_for_layout', $pages->formatTitle(
            __('An error occurred', true)
        ));
    } else if ($searchProblem == 'disabled') {
        $this->set('title_for_layout', $pages->formatTitle(
            __('Search is currently disabled', true)
        ));
    }
} else {
    // Case where the sentence has been deleted
    $this->set('title_for_layout', $pages->formatTitle(
        __('Sentence does not exist: ', true) . $this->params['pass'][0]
    ));
}

if (!isset($searchProblem)) {
    // navigation (previous, random, next)
    $navigation->displaySentenceNavigation(
        $sentenceId,
        $nextSentence,
        $prevSentence
    );
}
?>

<div id="annexe_content">
    <?php
    if (CurrentUser::get('settings.users_collections_ratings')) {
        echo '<div class="module correctness-info">';

        echo $html->tag('h2', __('Reviewed by', true));
        foreach($correctnessArray as $correctness) {
            echo '<div>';
            echo $images->correctnessIcon(
                $correctness['UsersSentences']['correctness']
            );
            echo $html->link(
                $correctness['User']['username'],
                array(
                    'controller' => 'user',
                    'action' => 'profile',
                    $correctness['User']['username']
                ),
                array(
                    'class' => 'username',
                    'title' => $correctness['UsersSentences']['modified']
                )
            );
            echo '</div>';
        }

        echo '</div>';
    }
    ?>

    <?php 
    if (isset($sentence)){
        $tags->displayTagsModule($tagsArray, $sentenceId);

        $lists->displayListsModule($listsArray);

        if (CurrentUser::isAdmin()) {
            echo $this->element(
                'sentences/audio',
                array(
                    'sentenceId' => $sentenceId,
                    'hasaudio' => $sentenceHasAudio
                )
            ); 

            // TODO For the beginning we'll restrict this to admins.
            // Later we'll want CurrentUser::isModerator();
            echo $this->element(
                'sentences/correctness',
                array(
                    'sentenceId' => $sentenceId,
                    'sentenceCorrectness' => $sentenceCorrectness
                )
            ); 
        }
    }
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
</div>

<div id="main_content">
    <div class="module">
        <?php
        if (isset($sentence)) {
        ?>
            <h2>
            <?php 
            echo format(__('Sentence #{number}', true), array('number' => $sentenceId));
            ?>
            </h2>            
            
            <?php
            // display sentence and translations
            $sentences->displaySentencesGroup(
                $sentence['Sentence'],
                $sentence['Transcription'],
                $translations,
                $sentence['User'],
                $indirectTranslations
            );
        } else if (isset($searchProblem)) {
            if($searchProblem == 'error') {
                echo $this->Html->tag('h2', 'There was an error while performing this function.');
            } else if ($searchProblem == 'disabled') {
                echo $this->Html->tag('h2', 'This feature is temporarily disabled. Please try later.');
            } 

        } else {
            
            echo '<h2>' .
                format(__('Sentence #{number}', true),
                       array('number' => $this->params['pass'][0])) .
                '</h2>';

            echo '<div class="error">';
                echo format(
                    __(
                        'There is no sentence with id {number}',
                        true
                    ), 
                    array('number' => $this->params['pass'][0])
                );
            echo '</div>';
        }
        ?>
    </div>

    <?php
    if (!isset($searchProblem)) {
        echo '<div class="module">';

        echo '<h2>';
        __('Comments');
        echo '</h2>';

        if (!empty($sentenceComments)) {
            echo '<div class="comments">';
            foreach ($sentenceComments as $i=>$comment) {
                $commentId = $comment['SentenceComment']['id'];
                $menu = $comments->getMenuForComment(
                    $comment['SentenceComment'],
                    $comment['User'],
                    $commentsPermissions[$i]
                );

                echo '<a id="comment-'.$commentId.'"></a>';

                $messages->displayMessage(
                    $comment['SentenceComment'],
                    $comment['User'],
                    null,
                    $menu
                );
            }
            echo '</div>';
        } else {
            echo '<em>' . __('There are no comments for now.', true) .'</em>';
        }

        if ($canComment) {
            if(!isset($sentence['Sentence'])) {
                $sentenceText = __('Sentence deleted', true);
            }
            $comments->displayCommentForm(
                $sentenceId,
                $sentenceText
            );
        }

        echo '</div>';
    }
    ?>
</div>
