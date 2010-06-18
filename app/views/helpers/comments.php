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

    public $helpers = array('Languages', 'Form', 'Date', 'Html', 'ClickableLinks');
    
    /**
     * Display a sentence comment block.
     *
     * @param array $comment         Comment to display.
     * @param bool  $displayAsThread If set to true it will display the "view" button
     *                               and the sentence in relation to the comment.
     * @param array $permissions     Array contaning what one can do with this
     *                               comment.
     * 
     * @return void
     */
    public function displaySentenceComment(
        $comment,
        $displayAsThread = false,
        $permissions = array()
    ) {
        // TODO explode this functions in smaller one
        // and make parameters more explicit 
        // (we don't see with the protype that we need sentences inside comment 
        // it should be a entire parameter, stop using universe-variable
        $user = $comment['User'];
        $userName = $user["username"];

        $sentenceComment = $comment;
        if (isset($comment['SentenceComment'])) { 
            $sentenceComment = $comment['SentenceComment'];
        }
        
        echo '<li>';
        
        echo '<ul class="meta">';
        
        // profile picture
        $image = 'unknown-avatar.png';
        if (!empty($user['image'])) {
            $image = $user['image'];
        }

        
        // view button
        if ($displayAsThread) {
            echo '<li class="action">';
            echo $this->Html->link(
                $this->Html->image(
                    'view.png',
                    array(
                        "title" => __(
                            'View all comments on the related sentence',
                            true
                        ),
                        "width" => 24,
                        "height" => 24
                    )
                ),
                array(
                    "controller" => "sentence_comments", 
                    "action" => "show", 
                    $sentenceComment['sentence_id'].'#comments'
                ),
                array("escape" => false)
            );
            echo '</li>';
        }
        
        // delete button
        if (isset($permissions['canDelete']) && $permissions['canDelete'] == true) {
            echo '<li class="action">';
            echo $this->Html->link(
                $this->Html->image(
                    'delete_comment.png',
                    array(
                        "title" => __(
                            'Delete this comment',
                            true
                        )
                    )
                ),
                array(
                    "controller" => "sentence_comments", 
                    "action" => "delete_comment", 
                    $sentenceComment['id']
                ),
                array("escape" => false),
                __('Are you sure?', true)
            );
            echo '</li>';
        }
        /* * * * * * * * * * * * * * * * */
        
        // user avatar
        echo '<li class="image">';
        echo $this->Html->link(
            $this->Html->image(
                'profiles_36/'.$image, 
                array("title" => __('View this user\'s profile', true))
            ),
            array(
                "controller" => "user",
                "action" => "profile",
                $user['username']
            ),
            array("escape" => false)
        );
        echo '</li>';
        
        // author
        echo '<li class="author">';
        echo $this->Html->link(
            $userName,
            array(
                "controller" => "user", 
                "action" => "profile", 
                $userName
            ),
            array("title" => __('View this user\'s profile', true))
        );
        echo '</li>';
        
        // date
        echo '<li class="date">';
        echo $this->Date->ago($sentenceComment['created']);
        echo '</li>';
        
        echo '</ul>';

        
        echo '<div class="body">';
        // sentence
        if ($displayAsThread && isset($comment['Sentence'])) {

            $sentence = $comment['Sentence'];
            $sentenceText = $sentence['text'];
            $sentenceId = $sentence['id'];
            
            $sentenceLang = null;
            if (!empty($sentence['lang'])) {
                $sentenceLang = $sentence['lang'];
            }
            $dir = $this->Languages->getLanguageDirection($sentenceLang);
            
            echo '<div class="sentence">';
            if (isset($sentenceText)) {
                echo $this->Html->link(
                    $sentenceText,
                    array(
                        "controller"=>"sentences",
                        "action"=>"show",
                        $sentenceId.'#comments'
                    ),
                    array(
                        'dir' => $dir
                    )
                );
            } else {
                echo '<em>'.__('sentence deleted', true).'</em>';
            }
            echo '</div>';
        }
        
        // comment text
        $commentText = $this->ClickableLinks->clickableURL(
            htmlentities(
                $sentenceComment['text'],
                ENT_QUOTES,
                'UTF-8'    
            )
        );
        echo nl2br($commentText);
        echo '</div>';
        
        echo '</li>';
    }
    

    
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
        echo $this->Form->create('SentenceComment', array("action"=>"save"));
        
        echo '<p>';
        echo $this->Form->hidden('sentence_id', array("value"=>$sentenceId));
        echo '</p>';
        
        // Text of the sentence is used for notification email, to avoid doing
        // another query.
        echo '<p>';
        echo $this->Form->hidden('sentence_text', array("value"=>$sentenceText));
        echo '</p>';
        
        echo $this->Form->input(
            'text',
            array(
                "label"=> "",
                "cols"=>"64", "rows"=>"6"
            )
        );
        
        echo $this->Form->end('Submit');
    }

}
?>
