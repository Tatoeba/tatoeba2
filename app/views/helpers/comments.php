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

/**
 * Helper for contributions.
 *
 * @category SentenceComments
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class CommentsHelper extends AppHelper
{

    public $helpers = array('Form', 'Html', 'Sentences', 'Messages');


    /**
     * Display form to post a comment.
     *
     * @param int $sentenceId   Id of the sentence.
     * @param int $sentenceText Text of the sentence.
     *
     * @return void
     */
    public function displayCommentForm($sentenceId, $sentenceText)
    {
        echo $this->Form->create(
            'SentenceComment', 
            array(
                "action" => "save",
                "class" => "message form"
            )
        );

        echo $this->Form->hidden('sentence_id', array("value"=>$sentenceId));

        // Text of the sentence is used for notification email, to avoid doing
        // another query.
        echo $this->Form->hidden('sentence_text', array("value"=>$sentenceText));
        ?>

        <div class="header">
            <div class="info">
            <?php
            $user = CurrentUser::get('User');
            $this->Messages->displayAvatar($user['User']);
            ?>
            </div>
            <div class="title">
            <?php __('Add a comment'); ?>
            </div>
        </div>

        <div class="body">
            <div class="textarea">
            <?php
            echo $this->Form->textarea(
                'text',
                array(
                    "label"=> "",
                    "lang" => "",
                    "dir" => "auto",
                )
            );
            ?>
            </div>

            <div layout="row" layout-align="end center" layout-padding>
                <md-button type="submit" class="md-raised md-primary">
                    <?php __('Submit comment'); ?>
                </md-button>
            </div>
        </div>
        <?php
        echo $this->Form->end();
        ?>


        <h3><?php __('Good practices'); ?></h3>
        <ul>
            <li>
            <?php
            __(
                'Say "welcome" to new users.'
            );
            ?>
            </li>

            <li>
            <?php
            __(
                'Use private messages to discuss things unrelated to the sentence.'
            );
            ?>
            </li>
        </ul>
        <?php
    }


    /**
     * Display sentence (for edit sentence_comment view)
     *
     * @param $sentence       Sentence to display.
     * @param $transcriptions Transcriptions of the sentence.
     *
     * @return void
     */
    public function displaySentence($sentence, $transcriptions)
    {
        $this->Sentences->displaySimpleSentencesGroup(
            $sentence,
            $transcriptions,
            array()
        );
    }


    /**
     *
     *
     *
     */
    public function displayCommentEditForm($message, $author) {
        $created = $message['created'];
        $modified = null;
        if (isset($message['modified'])) {
            $modified = $message['modified'];
        }
        
        $content = $message['text'];
        $authorId = $author['id'];

        // Hack. This was the only way I knew to get the proper
        // action value for this form
        // The form in users/edit also has the same problem
        echo $this->Form->create(
            false,
            array(
                "url" => array(
                    "controller" => "sentence_comments",
                    "action" => "edit",
                    $message['id']
                ),
                "class" => "message form"
            )
        );

        echo $this->Form->hidden('SentenceComment.id');
        echo $this->Form->hidden('SentenceComment.sentence_id');

        $this->Messages->displayHeader($author, $created, $modified, null);
        ?>

        <div class="body">
            <div class="textarea">
            <?php
            echo $this->Form->textarea('SentenceComment.text');
            ?>
            </div>

            <?php
            $cancelUrl = $this->Html->url(
                array(
                    "controller" => "sentences",
                    "action" => "show",
                    $message['sentence_id']."#comment-".$message['id']
                )
            );
            ?>
            <div layout="row" layout-align="end center" layout-padding>
                <md-button class="md-raised" href="<?= $cancelUrl; ?>">
                    <?php __('Cancel'); ?>
                </md-button>

                <md-button type="submit" class="md-raised md-primary">
                    <?php __('Save changes'); ?>
                </md-button>
            </div>
        </div>
        <?php
        echo $this->Form->end();
    }


    /**
     *
     *
     *
     */
    public function getMenuForComment($comment, $user, $permissions)
    {
        $menu = array(); 
        $commentId = $comment['id'];
        
        // hide
        if ($permissions['canHide']) {
            $hidden = $comment['hidden'];

            if ($hidden) {
                $hiddenLinkText = __('unhide', true);
                $hiddenLinkAction = 'unhide_message';
            } else {
                $hiddenLinkText = __('hide', true);
                $hiddenLinkAction = 'hide_message';
            }

            $menu[] = array(
                'text' => $hiddenLinkText,
                'url' => array(
                    "controller" => "sentence_comments",
                    "action" => $hiddenLinkAction,
                    $commentId
                )
            );
        }

        // delete
        if ($permissions['canDelete']) {
            $menu[] = array(
                'text' => __('delete', true),
                'url' => array(
                    "controller" => "sentence_comments",
                    "action" => "delete_comment",
                    $commentId
                ),
                'confirm' => __('Are you sure?', true)
            );
        }

        // edit
        if ($permissions['canEdit']) {
            $menu[] = array(
                'text' => __('edit', true),
                'url' => array(
                    "controller" => "sentence_comments",
                    "action" => "edit",
                    $commentId
                )
            );
        }
        
        // view
        $sentenceId = $comment['sentence_id'];
        $menu[] = array(
            'text' => '#',
            'url' => array(
                "controller" => "sentences",
                "action" => "show",
                $sentenceId."#comment-".$commentId
            )
        );

        return $menu;
    }

}
?>
