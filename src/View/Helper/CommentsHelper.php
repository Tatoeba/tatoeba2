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
namespace App\View\Helper;


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
                "url" => array("action" => "save"),
                "class" => "message form"
            )
        );

        echo $this->Form->hidden('sentence_id', array("value"=>$sentenceId));
        ?>

        <div class="header">
            <div class="info">
            <?php
            $user = CurrentUser::get('User');
            $this->Messages->displayAvatar($user['User']);
            ?>
            </div>
            <div class="title">
            <?php echo __('Add a comment'); ?>
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
                    <?php echo __('Submit comment'); ?>
                </md-button>
            </div>
        </div>
        <?php
        echo $this->Form->end();
        ?>


        <h3><?php echo __('Good practices'); ?></h3>
        <ul>
            <li>
            <?php
            echo __(
                'Say "welcome" to new users.'
            );
            ?>
            </li>

            <li>
            <?php
            echo __(
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
     *
     * @return void
     */
    public function displaySentence($sentence)
    {
        $sentence['Translation'] = array();
        $this->Sentences->displaySimpleSentencesGroup($sentence);
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

        echo $this->Form->create(array(
            'class' => 'message form',
        ));

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
                    $message['sentence_id'],
                    "#" => "comment-".$message['id'],
                )
            );
            ?>
            <div layout="row" layout-align="end center" layout-padding>
                <md-button class="md-raised" href="<?= $cancelUrl; ?>">
                    <?php echo __('Cancel'); ?>
                </md-button>

                <md-button type="submit" class="md-raised md-primary">
                    <?php echo __('Save changes'); ?>
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
    public function getMenuForComment($comment, $permissions, $replyIcon)
    {
        $menu = array();
        $commentId = $comment['id'];

        // hide
        if ($permissions['canHide']) {
            $hidden = $comment['hidden'];

            if ($hidden) {
                $hiddenLinkText = __('unhide');
                $hiddenLinkAction = 'unhide_message';
            } else {
                $hiddenLinkText = __('hide');
                $hiddenLinkAction = 'hide_message';
            }

            $menu[] = array(
                'text' => $hiddenLinkText,
                'icon' => 'visibility_off',
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
                'text' => __('delete'),
                'icon' => 'delete',
                'url' => array(
                    "controller" => "sentence_comments",
                    "action" => "delete_comment",
                    $commentId
                ),
                'confirm' => __('Are you sure?')
            );
        }

        // edit
        if ($permissions['canEdit']) {
            $menu[] = array(
                'text' => __('edit'),
                'icon' => 'edit',
                'url' => array(
                    "controller" => "sentence_comments",
                    "action" => "edit",
                    $commentId
                )
            );
        }

        // view
        $sentenceId = $comment['sentence_id'];
        $viewIcon = $replyIcon ? 'reply' : 'link';
        $menu[] = array(
            'text' => '#',
            'icon' => $viewIcon,
            'url' => array(
                "controller" => "sentences",
                "action" => "show",
                '#' => 'comment-'.$commentId,
                $sentenceId
            )
        );

        return $menu;
    }

}
?>
