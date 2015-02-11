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
 * Helper for messages.
 *
 * @category SentenceComments
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */


class MessagesHelper extends AppHelper
{
    public $helpers = array('Date', 'Html', 'ClickableLinks', 'Languages', 'Form');

    /**
     *
     * 
     *
     */
    public function displayMessage($message, $author, $sentence, $menu) {
        $created = null;
        if (isset($message['created'])) {
            $created = $message['created'];    
        } else if (isset($message['date'])) {
            $created = $message['date'];
        }
        
        $modified = null;
        if (isset($message['modified'])) {
            $modified = $message['modified'];
        }

        $hidden = false;
        if (isset($message['hidden'])) {
            $hidden = $message['hidden'];   
        }

        $hiddenClass = "";
        $authorId = null;
        if ($hidden) {
            $hiddenClass = " inappropriate";
            $authorId = $author['id'];
        }
        
        $content = null;
        if (isset($message['text'])) {
            $content = $message['text'];
        } else if (isset($message['content'])) {
            $content = $message['content'];
        }

        echo "<div class='message ${hiddenClass}'>";
        $this->displayHeader($author, $created, $modified, $menu);
        $this->_displayBody($content, $sentence, $hidden, $authorId);
        echo "</div>";
    }


    /**
     *
     * 
     *
     */
    public function displayHeader($author, $created, $modified, $menu)
    {
        ?>
        <div class="header">
        <?php
            $this->_displayInfo($author, $created, $modified);

            if (!empty($menu)) {
                $this->_displayMenu($menu);    
            }
        ?>
        </div>
        <?php
    }



    /**
     *
     * 
     *
     */
    public function displayFormHeader($title)
    {
        ?>
        <div class="header">
            <div class="info">
            <?php
            $user = CurrentUser::get('User');
            $this->displayAvatar($user['User']);
            ?>
            </div>
            <div class="title">
            <?php echo $title ?>
            </div>
        </div>
        <?php
    }


    /**
     * Author name, author avatar and date of the message.
     * 
     *
     */
    private function _displayInfo($author, $created, $modified)
    {
        ?>
        <div class="info">
            <?php $this->displayAvatar($author); ?><!--
            --><div class="other">

                <div class="username">
                <?php 
                if (!$author['username']) {
                    echo $this->Html->tag('i', __('Former member', true));
                } else {
                    echo $this->Html->link(
                        $author['username'],
                        array(
                            'controller' => 'user',
                            'action' => 'profile',
                            $author['username']
                        )
                    );
                }
                ?>
                </div>

                <?php
                $displayPM = CurrentUser::isMember() 
                    && $author['username']
                    && CurrentUser::get('username') != $author['username'];
                if ($displayPM) {
                    ?><div class="pm"><?php
                    echo $this->Html->link(
                        '',
                        array(
                            "controller" => "private_messages",
                            "action" => "write",
                            $author['username']
                        ),
                        array(
                            "escape" => false,
                            'title' => __("Send private message", true)
                        )
                    );
                    ?></div><?php
                }
                ?>

                <div class="date">
                <?php
                echo $this->Date->ago($created);

                if (!empty($modified)) {
                    $date1 = new DateTime($created);
                    $date2 = new DateTime($modified);
                    if ($date1 != $date2) {
                        echo " - ";
                        __("edited");
                        echo " {$this->Date->ago($modified)}"; 
                    }
                }
                ?>
                </div>

            </div>
        </div>
        <?php
    }


    /**
     * Author avatar.
     * 
     *
     */
    public function displayAvatar($author)
    {
        $image = $author['image'];
        $username = $author['username'];
        if (empty($image)) {
            $image = 'unknown-avatar.png';
        }

        ?><div class="avatar"><?php
        if ($username) {
            echo $this->Html->link(
                $this->Html->image(
                    IMG_PATH . 'profiles_36/'. $image,
                    array(
                        'alt' => $username,
                        'title' => __("View this user's profile", true),
                        'width' => 36,
                        'height' => 36
                    )
                ),
                array(
                    'controller' => 'user',
                    'action' => 'profile',
                    $username
                ),
                array('escape' => false)
            );
        } else {
            echo $this->Html->image(
                IMG_PATH . 'profiles_36/'. $image,
                array(
                    'alt' => __('Deleted user', true),
                    'width' => 36,
                    'height' => 36,
                )
            );
        }
        ?></div><?php
    }

    /**
     * Message menu (show, edit, delete, etc).
     * 
     *
     */
    private function _displayMenu($menu)
    {
        ?>
        <ul class="menu">
        <?php
        foreach ($menu as $item) {
            ?>
            <li>
            <?php
            $url = null;
            if (!empty($item['url'])) {
                $url = $item['url'];
            }
            $options = array();
            if (!empty($item['id'])) {
                $options['id'] = $item['id'];
            }
            if (!empty($item['class'])) {
                $options['class'] = $item['class'];
            }
            $confirm = null;
            if (!empty($item['confirm'])) {
                $confirm = $item['confirm'];
            }

            if (empty($url)) {
                // Custom HTML in case url is null.
                // If we use html helper it won't remove the href.
                echo '<a id="'.$options['id'].'" class="'.$options['class'].'">'.
                     $item['text'].
                     '</a>';
            } else {
                echo $this->Html->link(
                    $item['text'], 
                    $url,
                    $options,
                    $confirm
                );    
            }
            
            ?>
            </li>
            <?php
        }
        ?>
        </ul>
        <?php
    }


    /**
     *
     * 
     *
     */
    private function _displayBody($content, $sentence, $hidden, $authorId)
    {
        ?><div class="body"><?php
        if (!empty($sentence)) {
            $this->_displaySentence($sentence);    
        }

        if ($hidden) {
            $this->_displayWarning();
        }

        $canViewContent = CurrentUser::isAdmin()
            || CurrentUser::get('id') == $authorId;
        if ($canViewContent) {
            ?><div class="separator"></div><?php
        }

        if (!$hidden || $canViewContent) {
            echo $this->Languages->tagWithLang(
                'div', '', $this->formatedContent($content),
                array('class' => 'content', 'escape' => false)
            );
        }

        ?></div><?php
    }


    /**
     *
     * 
     *
     */
    private function _displaySentence($sentence)
    {
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
        if (isset($sentence['text'])) {
            $sentenceText = $sentence['text'];
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
                    "controller" => "sentences",
                    "action" => "show",
                    $sentenceId
                ),
                array(
                    'dir' => $dir,
                    'lang' => $this->Languages->langAttribute($sentenceLang),
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
     *
     *
     */
    private function _displayWarning()
    {
        ?><div class='warningInfo'><?php
        echo format(
            __(
                'The content of this message goes against '.
                '<a href="{}">our rules</a> and was therefore hidden. '.
                'It is displayed only to admins '.
                'and to the author of the message.',
                true
            ),
            'http://en.wiki.tatoeba.org/articles/show/rules-against-bad-behavior'
        );
        ?></div><?php
    }


    /**
     * @param string $content     Text of the comment.
     *
     * @return string The comment body formatted for HTML display.
     */
    public function formatedContent($content) {
        $content = Sanitize::html($content);

        // Convert sentence mentions to links
        $content = $this->ClickableLinks->clickableSentence($content);

        // Make URLs clickable
        $content = $this->ClickableLinks->clickableURL($content);

        // Convert linebreaks to <br/>
        $content = nl2br($content);

        return $content;
    }


    /**
     * Displays the preview (first X characters) of a message. 
     * If possible, it will not cut the message in the middle of a word or a link.
     * 
     * @param  String  $content     The whole content.
     * @param  integer $length      Number of characters of the preview.
     * @param  integer $extraLength Tells how far we should search for a "space"
     *                              character, when trying to not cut the text
     *                              in the middle of a word/link.
     * 
     * @return String               Preview text
     */
    public function preview($content, $length = 200, $extraLength = 100)
    {
        $contentBefore = mb_substr($content, 0, $length);
        $contentAfter = mb_substr($content, $length);

        $spaceAfter = mb_strpos($contentAfter, " ");
        $newLineAfter = mb_strpos($contentAfter, PHP_EOL);
        if (!$spaceAfter || $newLineAfter < $spaceAfter) {
            $spaceAfter = $newLineAfter;
        }

        $hasLink = $this->ClickableLinks->hasClickableLink($content);

        $formatContent = true;

        if ($spaceAfter && $spaceAfter < $extraLength) {
            
            // We want to display 200 + a few more charafters. The few more
            // characters are the ones that are before the 1st "space" that we find
            // after the 200 characters.
            $lengthToCut = $length + $spaceAfter;
            $previewContent = mb_substr($content, 0, $lengthToCut);
            $displayElipsis = mb_strlen($content) > $lengthToCut;

        } else if ($hasLink && mb_strlen($content) <= $length + $extraLength) {

            // Normally, if fall in this case, then we're either trying to cut
            // a text in a language that has no space, or we're cutting the text
            // in a middle of an URL. In this case, if the message is not too long
            // we display it entirely.
            $previewContent = $content;
            $displayElipsis = false;

        } else {

            // If we can't do a "soft" truncation, then we just hard truncate.
            // In case of hard truncation, we don't format the text.
            $previewContent = mb_substr($content, 0, $length);
            $displayElipsis = mb_strlen($content) > $length;
            $formatContent = false;

        }

        if ($formatContent) {
            $previewContent = $this->formatedContent($previewContent);
        } else {
            $previewContent = nl2br($previewContent);
        }

        if ($displayElipsis) {
            $previewContent .= ' [...]';
        }

        return $previewContent;
    }

}
