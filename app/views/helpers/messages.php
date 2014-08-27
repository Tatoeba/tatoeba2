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
    public $helpers = array('Date', 'Html', 'ClickableLinks');

    /**
     *
     * 
     *
     */
    public function displayMessage($message, $author, $sentence, $menu) {
        $created = $message['created'];
        $modified = null;
        if (isset($message['modified'])) {
            $modified = $message['modified'];
        }

        $hidden = false;
        if (isset($message['hidden'])) {
            $hidden = $message['hidden'];
        }
        $content = $message['text'];

        ?><div class="message"><?php
            $this->_displayHeader($author, $created, $modified, $menu);
            $this->_displayBody($content, $sentence, $hidden);
        ?></div><?php
    }


    /**
     *
     * 
     *
     */
    private function _displayHeader($author, $created, $modified, $menu)
    {
        ?>
        <div class="header">
        <?php
            $this->_displayInfo($author, $created, $modified);
            $this->_displayMenu($menu);
        ?>
        </div>
        <?php
    }


    /**
     * Author name, author avatar and date of the message.
     * 
     *
     */
    private function _displayInfo($author, $date)
    {
        ?>
        <div class="info">
            <?php $this->_displayAvatar($author); ?><!--
            --><div class="other">

                <div class="username">
                <?php 
                echo $this->Html->link(
                    $author['username'],
                    array(
                        'controller' => 'user',
                        'action' => 'profile',
                        $author['username']
                    )
                )
                ?>
                </div>

                <div class="date">
                <?php echo $this->Date->ago($date); ?>
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
    private function _displayAvatar($author)
    {
        $image = $author['image'];
        $username = $author['username'];
        if (empty($imageName)) {
            $imageName = 'unknown-avatar.png';
        }

        ?><div class="avatar"><?php
        echo $this->Html->image(
            IMG_PATH . 'profiles_36/'.$image,
            array(
                "title" => __("View this user's profile", true),
                "width" => 36,
                "height" => 36
            )
        );
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
            echo $this->Html->link($item['text'], $item['url']);
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
    private function _displayBody($content, $sentence, $hidden)
    {
        ?><div class="body"><?php
        if (!empty($sentence)) {
            $this->_displaySentence($sentence);    
        }

        if ($hidden) {
            $this->_displayWarning();
        }

        echo $this->_formatedContent($content);

        ?></div><?php
    }


    /**
     *
     * 
     *
     */
    private function _displaySentence($sentence)
    {
    }


    /**
     *
     *
     */
    private function _displayWarning()
    {
        ?><div class='warning'><?php
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
        ?></div><?php
    }


    /**
     * @param string $content     Text of the comment.
     *
     * @return string The comment body formatted for HTML display.
     */
    private function _formatedContent($content) {
        $content = htmlentities($content, ENT_QUOTES, 'UTF-8');

        // Convert sentence mentions to links
        $self = $this;
        $content = preg_replace_callback('/\[#(\d+)\]/', function ($m) use ($self) {
            return $self->Html->link('#' . $m[1], array(
                'controller' => 'sentences',
                'action' => 'show',
                $m[1]
            ));
        }, $content);

        // Make URLs clickable
        $content = $this->ClickableLinks->clickableURL($content);

        // Convert linebreaks to <br/>
        $content = nl2br($content);

        return $content;
    }
}