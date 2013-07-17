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
     * @param array $comment     Comment array.
     * @param array $user        Author array.
     * @param bool  $sentence    Related sentence array.
     * @param array $permissions Permissions array.
     * 
     * @return void
     */
    public function displaySentenceComment(
        $comment, $user, $sentence, $permissions = array()
    ) {
        $userName = $user["username"];
        $userImage = $user['image'];
        
        $commentId = $comment['id'];
        $commentText = $comment['text'];
        
        $date = $comment['created'];
        
        $hidden = $comment['hidden'];
        $authorId = $comment['user_id'];
        ?>
        <li>
        <a id="comment-<?php echo $commentId; ?>" />
        <?php
        $this->_displayActions(
            $permissions, $commentId, $comment['sentence_id'], $userName, $hidden
        );
        $this->_displayMeta($userName, $userImage, $date);
        $this->_displayBody($commentText, $sentence, $hidden, $authorId);
        ?>
        </li>
        <?php
    }
    
    
    /**
     * Display meta information.
     *
     * @param string $userName  Username of the author of the comment.
     * @param string $userImage Profile picture of the author of the comment.
     * @param string $date      Date of the comment.
     * 
     * @return void
     */
    private function _displayMeta($userName, $userImage, $date)
    {
        ?>
        <div class="meta">
        <?php
        $this->_displayAuthorImage($userName, $userImage);
        $this->_displayAuthor($userName);
        $this->_displayDate($date);
        ?>
        </div>
        <?php
    }
    
    
    /**
     * Display profile picture of the author of the comment.
     *
     * @param $string $username  User name.
     * @param $string $imageName Image name.
     *
     * @return void
     */
    public function _displayAuthorImage($userName, $imageName)
    {
        if (empty($imageName)) {
            $imageName = 'unknown-avatar.png';
        }
        ?>
        <div class="image">
        <?php
        echo $this->Html->link(
            $this->Html->image(
                IMG_PATH . 'profiles_36/'.$imageName, 
                array(
                    "title" => __('View this user\'s profile', true),
                    "width" => 36,
                    "height" => 36
                )
            ),
            array(
                "controller" => "user",
                "action" => "profile",
                $userName
            ),
            array("escape" => false)
        );
        ?>
        </div>
        <?php
    }
    
    
    /**
     * Display author's username.
     *
     * @param string $userName Author's username.
     *
     * @return void
     */
    private function _displayAuthor($userName)
    {
        ?>
        <div class="author">
        <?php
        echo $this->Html->link(
            $userName,
            array(
                "controller" => "user", 
                "action" => "profile", 
                $userName
            ),
            array("title" => __('View this user\'s profile', true))
        );
        ?>
        </div>
        <?php
    }
    
    
    /**
     * Display date.
     *
     * @param string $date Date.
     * 
     * @return void
     */
    private function _displayDate($date)
    {
        ?>
        <div class="date" title="<?php echo $date; ?>">
        <?php echo $this->Date->ago($date); ?>
        </div>
        <?php
    }
    
    
    /**
     * Display view and delete buttons.
     *
     * @param array  $permissions Permissions.
     * @param int    $commentId   Id of the comment.
     * @param int    $sentenceId  Id of the related sentence.
     * @param string $username    Username of the author of the comment.
     * @param bool   $hidden      'true' if comment is hidden, 'false' otherwise.
     *
     * @return void
     */
    private function _displayActions(
        $permissions, $commentId, $sentenceId, $username, $hidden        
    ) {
        ?>
        <div class="actions">
        <?php
        if (CurrentUser::isAdmin())
        {
            $this->_displayHideButton($commentId, $hidden);
        }
        
        $this->_displayViewButton($commentId, $sentenceId);
        
        if (CurrentUser::isMember()) {
            $this->_displayPmButton($username);
        }
        
        if (isset($permissions['canDelete']) && $permissions['canDelete'] == true) {
            $this->_displayDeleteButton($commentId);
        }
        ?>
        </div>
        <?php
    }
    
    
    /**
     * Display "private message" button, to write a private message to the author
     * of the message.
     *
     * @param string $username Username.
     *
     * @return void
     */
    private function _displayPmButton($username)
    {
        ?>
        <div class="action">
        <?php
        echo $this->Html->link(
            $this->Html->image(
                IMG_PATH . 'send_pm.png',
                array(
                    "title" => __(
                        'Send private message',
                        true
                    ),
                    "width" => 24,
                    "height" => 24
                )
            ),
            array(
                "controller" => "private_messages", 
                "action" => "write", 
                $username,
            ),
            array(
                "escape" => false
            )
        );
        ?>
        </div>
        <?php
    }
    
    
    /**
     * Display "view" button.
     *
     * @param int $commentId  Id of the comment.
     * @param int $sentenceId Id of the sentence.
     *
     * @return void
     */
    private function _displayViewButton($commentId, $sentenceId)
    {
        ?>
        <div class="action">
        <?php
        echo $this->Html->link(
            $this->Html->image(
                IMG_PATH . 'view.png',
                array(
                    "title" => __(
                        'View comment',
                        true
                    ),
                    "width" => 24,
                    "height" => 24
                )
            ),
            array(
                "controller" => "sentences", 
                "action" => "show", 
                $sentenceId,
                "#" => "comment-".$commentId
            ),
            array(
                "escape" => false
            )
        );
        ?>
        </div>
        <?php
    }
    
    
    /**
     * Delete button.
     *
     * @param int $commentId Id of the comment.
     *
     * @return void
     */
    private function _displayDeleteButton($commentId)
    {
        ?>
        <div class="action">
        <?php
        echo $this->Html->link(
            $this->Html->image(
                IMG_PATH . 'delete_comment.png',
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
                $commentId
            ),
            array("escape" => false),
            __('Are you sure?', true)
        );
        ?>
        </div>
        <?php
    }
    
    
    /**
     * Hide button.
     *
     * @param int $commentId Id of the comment.
     *
     * @return void
     */
    private function _displayHideButton($commentId, $hidden)
    {
        ?>
        <div class="action">
        <?php
        if ($hidden) {
            $hiddenLinkText = __('unhide', true);
            $hiddenLinkAction = 'unhide_message';
        } else {
            $hiddenLinkText = __('hide', true);
            $hiddenLinkAction = 'hide_message';
        }
        
        // hide/unhide link, for when people start acting like kids and stuff
        echo $this->Html->link(
            $hiddenLinkText,
            array(
                "controller" => "sentence_comments", 
                "action" => $hiddenLinkAction,
                $commentId
            )
        );
        ?>
        </div>
        <?php
    }
    
    
    /**
     * Display body.
     *
     * @param string $commentText Text of the comment.
     * @param array  $sentence    Related sentence.
     * @param bool   $hidden      'true' if the comment is hidden because it is
     *                            considered inappropriate. 'false' otherwise.
     * @param int    $authorId    Id of the author of the comment.
     *
     * @return void
     */
    private function _displayBody($commentText, $sentence, $hidden, $authorId)
    {
        ?>
        <div class="body">
            <?php
            if (isset($sentence['text'])) {
                $this->_displayRelatedSentence($sentence);
            }
            ?>
                
            <div class="commentText">   
            <?php
            if ($hidden) {
                echo "<div class='hidden'>";
                echo sprintf(
                    __(
                        'The content of this message goes against '.
                        '<a href="%s">our rules</a> and was therefore hidden. '.
                        'It is displayed only to admins '.
                        'and to the author of the message.',
                        true
                    ),
                    'http://en.wiki.tatoeba.org/articles/show/rules-against-bad-behavior'
                );
                echo "</div>";
            }
            
            $isDisplayedToCurrentUser = !$hidden 
                || CurrentUser::isAdmin() 
                || CurrentUser::get('id') == $authorId;
                
            if ($isDisplayedToCurrentUser)
            {
                // re #373 change the message style to be more clear to the reader of the message 
                if($hidden){
                    echo "<br />";
                    echo "<div class='hiddenUserMessage'>\"";
                }

                $this->_displayCommentText($commentText);

                if($hidden){
                    echo "\"</div>";
                }
            }
            ?>
            </div>
        </div>
        <?php
    }


    /**
     * @param string $commentText     Text of the comment.
     *
     * @return string The comment body formatted for HTML display.
     */
    public function formatComment($commentText) {
      $commentText = htmlentities($commentText, ENT_QUOTES, 'UTF-8');

      // Convert sentence mentions to links
      $self = $this;
      $commentText = preg_replace_callback('/\[#(\d+)\]/', function ($m) use ($self) {
          return $self->Html->link('#' . $m[1], array(
            'controller' => 'sentences',
            'action' => 'show',
            $m[1]
          ));
        }, $commentText);

      // Make URLs clickable
      $commentText = $this->ClickableLinks->clickableURL($commentText);

      // Convert linebreaks to <br/>
      $commentText = nl2br($commentText);

      return $commentText;
    }


    /**
     * Display comment text.
     *
     * @param string $commentText Text of the comment.
     *
     * @return void
     */
    private function _displayCommentText($commentText)
    {
        echo $this->formatComment($commentText);
    }
    
    
    /**
     * Display related sentence.
     *
     * @param array $sentence Sentence info.
     *
     * @return void
     */
    private function _displayRelatedSentence($sentence)
    {
        $sentenceText = $sentence['text'];
        $sentenceId = $sentence['id'];
        $ownerName = null;
        if (isset($sentence['User']['username'])) {
            $ownerName = $sentence['User']['username'];
        }
        
        $sentenceLang = null;
        if (!empty($sentence['lang'])) {
            $sentenceLang = $sentence['lang'];
        }
        $dir = $this->Languages->getLanguageDirection($sentenceLang);
        ?>
        <div class="sentence">
        <?php
        if (isset($sentenceText)) {
            echo $this->Languages->icon(
                $sentenceLang, 
                array(
                    "class" => "langIcon",
                    "width" => 20
                )
            );
            
            echo $this->Html->link(
                $sentenceText,
                array(
                    "controller"=>"sentences",
                    "action"=>"show",
                    $sentenceId.'#comments'
                ),
                array(
                    'dir' => $dir,
                    'class' => 'sentenceText'
                )
            );
            
            if (!empty($ownerName)) {
                echo $this->Html->link(
                    '['.$ownerName.']',
                    array(
                        "controller" => "user",
                        "action" => "profile",
                        $ownerName
                    ),
                    array(
                        "class" => "ownerName"
                    )
                );
            } 
        } else {
            echo '<em>'.__('sentence deleted', true).'</em>';
        }
        ?>
        </div>
        <?php
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
        
        echo '<div>';
        echo $this->Form->hidden('sentence_id', array("value"=>$sentenceId));
        // Text of the sentence is used for notification email, to avoid doing
        // another query.
        echo $this->Form->hidden('sentence_text', array("value"=>$sentenceText));
        echo '</div>';
        
        echo $this->Form->input(
            'text',
            array(
                "label"=> "",
                "cols"=>"64", "rows"=>"6"
            )
        );
        
        echo $this->Form->end(__('Submit comment', true));
        
        ?>
        <p>
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
        </p>        
        <?php
    }

}
?>
