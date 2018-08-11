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

$this->Sentences->javascriptForAJAXSentencesGroup(false);
$this->Html->script('jquery.scrollTo.min.js', false);

if (!isset($searchProblem)) {
if (isset($sentence)) {
    $sentenceId = $sentence['Sentence']['id'];
    $sentenceLang = $sentence['Sentence']['lang'];
    $sentenceText = $sentence['Sentence']['text'];
    $sentenceCorrectness = $sentence['Sentence']['correctness'];

    $languageName = $this->Languages->codeToNameToFormat($sentenceLang);
    $title = format(__('{language} example sentence: {sentence}'),
                    array('language' => $languageName, 'sentence' => $sentenceText));
    $this->set('title_for_layout', $this->Pages->formatTitle($title));

    $this->Html->meta(
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
} else {
    // Case where the sentence has been deleted
    $this->set('title_for_layout', $this->Pages->formatTitle(
        __('Sentence does not exist: ') . $this->request->params['pass'][0]
    ));
}


// navigation (previous, random, next)
$this->Navigation->displaySentenceNavigation(
    $sentenceId,
    $nextSentence,
    $prevSentence
);
?>

<div id="annexe_content">
    <?php
    if (CurrentUser::get('settings.users_collections_ratings')) {
        echo '<div class="module correctness-info">';

        echo $this->Html->tag('h2', __('Reviewed by'));
        foreach($correctnessArray as $correctness) {
            echo '<div>';
            echo $this->Images->correctnessIcon(
                $correctness['UsersSentences']['correctness']
            );
            echo $this->Html->link(
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
            if ($correctness['UsersSentences']['dirty'] != 0) {
                echo ' <span class="info">'. __('(outdated)'). '</span>';
            }
            echo '</div>';
        }

        echo '</div>';
    }
    ?>

    <?php
    if (isset($sentence)){
        $this->Tags->displayTagsModule($tagsArray, $sentenceId, $sentenceLang);

        $this->Lists->displayListsModule($listsArray);

        echo $this->element(
            'sentences/license',
            array(
                'sentenceId' => $sentenceId,
                'license' => $sentence['Sentence']['license'],
                'canEdit' => CurrentUser::get('id') == $sentence['Sentence']['user_id'],
            )
        );

        echo $this->element(
            'sentences/audio',
            array(
                'sentenceId' => $sentenceId,
                'audios' => $sentence['Audio']
            )
        );

        if (CurrentUser::isAdmin()) {
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

    <div class="section">
        <?php
        echo '<h2>';
        echo __('Logs');
        echo '</h2>';

        if (isset($sentence)) {
            echo $this->Sentences->originText($sentence);
        }

        if (!empty($contributions)) {
            echo '<md-list id="logs">';
            foreach ($contributions as $contribution) {
                echo $this->element(
                    'logs/log_entry_annexe',
                    array('log' => $contribution)
                );
            }
            echo '</md-list>';
        } else {
            echo '<em>'. __('There is no log for this sentence') .'</em>';
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
            echo format(__('Sentence #{number}'), array('number' => $sentenceId));
            ?>
            </h2>

            <?php
            // display sentence and translations
            $this->Sentences->displaySentencesGroup($sentence);

        } else {

            echo '<h2>' .
                format(__('Sentence #{number}'),
                       array('number' => $this->request->params['pass'][0])) .
                '</h2>';

            echo '<div class="error">';
                echo format(
                    __(
                        'There is no sentence with id {number}',
                        true
                    ),
                    array('number' => $this->request->params['pass'][0])
                );
            echo '</div>';
        }
        ?>
    </div>

    <div class="section">
        <h2><? echo __('Comments'); ?></h2>
        <?php
        if (!empty($sentenceComments)) {
            echo '<div class="comments">';
            foreach ($sentenceComments as $i=>$comment) {
                $commentId = $comment['SentenceComment']['id'];
                $menu = $this->Comments->getMenuForComment(
                    $comment['SentenceComment'],
                    $commentsPermissions[$i],
                    false
                );

                echo '<a id="comment-'.$commentId.'"></a>';

                echo $this->element(
                    'messages/comment',
                    array(
                        'comment' => $comment,
                        'menu' => $menu,
                        'replyIcon' => false
                    )
                );
            }
            echo '</div>';
        } else {
            echo '<em>' . __('There are no comments for now.') .'</em>';
        }

        if ($canComment) {
            if(!isset($sentence['Sentence'])) {
                $sentenceText = __('Sentence deleted');
            }
            $this->Comments->displayCommentForm(
                $sentenceId,
                $sentenceText
            );
        }
        ?>
    </div>
</div>
<?php
} else {
?>
    <div id="main_content">
        <div class="module">
            <?php
            echo $this->Html->tag('h2', __('Random Sentence'));
            if($searchProblem == 'disabled') {
                echo $this->Html->tag('p', __('The random sentence feature is currently disabled, please try again later.'));
            } else if ($searchProblem == 'error') {
                echo $this->Html->tag('p', format(__('An error occurred while fetching random sentences. '.
                                               'If this persists, please <a href="{}">let us know</a>.', true),
                                     $this->Html->url(array("controller"=>"pages", "action" => "contact"))
                ));
            }
            ?>
        </div>
    </div>
<?php } ?>
