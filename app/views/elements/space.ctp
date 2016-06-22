<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  Etienne Deparis <etienne.deparis@umaneti.net>
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
 * @author   Etienne Deparis <etienne.deparis@umaneti.net>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

$lang = 'eng';
if (isset($this->params['lang'])) {
    Configure::write('Config.language', $this->params['lang']);
    $lang = $this->params['lang'];
}
?>


<ul>
    <li id="profile">
    <?php
    $username = $session->read('Auth.User.username');
    $profileIcon = $html->image(
        IMG_PATH . 'profile.svg',
        array(
            "alt" => __('Profile', true),
            "width" => 14,
            "height" => 14
        )
    );
    echo $html->link(
        $profileIcon . $username,
        array(
            'controller' => 'user',
            'action' => 'profile',
            $username
        ),
        array(
            'escape' => false,
            'class' => 'menuSection'
        )
    );
    ?>
    <ul class='sub-menu'>
        <li class="item">
            <?php
            echo $html->link(
                __('My profile', true),
                array(
                    'controller' => 'user',
                    'action' => 'profile',
                    $username
                )
            );
            ?>
        </li>

        <li class="item">
            <?php
            echo $html->link(
                __('My sentences', true),
                array(
                    'controller' => 'sentences',
                    'action' => 'of_user',
                    $username
                )
            );
            ?>
        </li>

        <li class="item">
            <?php
            echo $html->link(
                __('My vocabulary', true),
                array(
                    'controller' => 'vocabulary',
                    'action' => 'of',
                    $username
                )
            );
            ?>
        </li>

        <li class="item">
            <?php
            echo $html->link(
                __('My collection', true),
                array(
                    'controller' => 'collections',
                    'action' => 'of',
                    $username
                )
            );
            ?>
        </li>

        <li class="item">
            <?php
            echo $html->link(
                __('My lists', true),
                array(
                    'controller' => 'sentences_lists',
                    'action' => 'of_user',
                    $username
                )
            );
            ?>
        </li>

        <li class="item">
            <?php
            echo $html->link(
                __('My favorites', true),
                array(
                    'controller' => 'favorites',
                    'action' => 'of_user',
                    $username
                )
            );
            ?>
        </li>

        <li class="item">
            <?php
            echo $html->link(
                __('My comments', true),
                array(
                    'controller' => 'sentence_comments',
                    'action' => 'of_user',
                    $username
                )
            );
            ?>
        </li>

        <li class="item">
            <?php
            echo $html->link(
                __("Comments on my sentences", true),
                array(
                    'controller' => 'sentence_comments',
                    'action' => 'on_sentences_of_user',
                    $username
                )
            );
            ?>
        </li>

        <li class="item">
            <?php
            echo $html->link(
                __('My Wall messages', true),
                array(
                    'controller' => 'wall',
                    'action' => 'messages_of_user',
                    $username
                )
            );
            ?>
        </li>

        <li class="item">
            <?php
            echo $html->link(
                __('My sentence logs', true),
                array(
                    'controller' => 'contributions',
                    'action' => 'of_user',
                    $username
                )
            );
            ?>
        </li>

        <li class="settings">
            <?php
            echo $html->link(
                __('Settings', true),
                array(
                    'controller' => 'user',
                    'action' => 'settings',
                )
            );
            ?>
        </li>
    </ul>
    </li>



    <?php
    $newMessages = ClassRegistry::init('PrivateMessage')->numberOfUnreadMessages(
        CurrentUser::get('id')
    );
    $class = '';
    $imageName = 'no_mail.svg';
    if ($newMessages > 0) {
        $class = 'class="newMessage"';
        $imageName = 'mail.svg';
    }
    ?>
    <li id="inbox" <?php echo $class; ?> title="<?php __('Inbox'); ?>">
    <?php

    $mailIcon = $html->image(
        IMG_PATH . $imageName,
        array(
            "alt" => __('Inbox', true),
            "width" => 16,
            "height" => 16
        )
    );


    echo $html->link(
        $mailIcon .' '. $newMessages,
        array(
            'controller' => 'private_messages',
            'action' => 'folder',
            'Inbox'
        ),
        array(
            'escape' => false,
            'class' => 'menuSection'
        )
    );
    ?>
    </li>

    <li id="log_out">
    <?php
    $logOutIcon = $html->image(
        IMG_PATH . 'log_out.svg',
        array(
            "alt" => __('Log out', true),
            "title" => __('Log out', true),
            "width" => 14,
            "height" => 14
        )
    );
    echo $html->link(
        $logOutIcon,
        array(
            'controller' => 'users',
            'action' => 'logout'
        ),
        array(
            'escape' => false,
            'class' => 'menuSection'
        )
    );
    ?>
    </li>
</ul>
