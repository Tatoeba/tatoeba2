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
 * @link     https://tatoeba.org
 */
use App\Model\CurrentUser;
use Cake\ORM\TableRegistry;
?>


<ul>
    <li id="profile">
    <?php
    $username = $this->request->getSession()->read('Auth.User.username');
    $profileIcon = $this->Html->image(
        IMG_PATH . 'profile.svg',
        array(
            "alt" => __('Profile'),
            "width" => 14,
            "height" => 14
        )
    );
    echo $this->Html->tag(
        'a',
        $profileIcon . $username,
        array(
            'escape' => false,
            'class' => 'menuSection'
        )
    );
    ?>
    <ul class='sub-menu'>
        <li class="item">
            <?php
            echo $this->Html->link(
                __('My profile'),
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
            echo $this->Html->link(
                __('My sentences'),
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
            echo $this->Html->link(
                __('My vocabulary'),
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
            echo $this->Html->link(
                __('My collection'),
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
            echo $this->Html->link(
                __('My lists'),
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
            echo $this->Html->link(
                __('My favorites'),
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
            echo $this->Html->link(
                __('My comments'),
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
            echo $this->Html->link(
                __("Comments on my sentences"),
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
            echo $this->Html->link(
                __('My Wall messages'),
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
            echo $this->Html->link(
                __('My sentence logs'),
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
            echo $this->Html->link(
                __('Settings'),
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
    $newMessages = TableRegistry::get('PrivateMessages')->numberOfUnreadMessages(
        CurrentUser::get('id')
    );
    $class = '';
    $imageName = 'no_mail.svg';
    if ($newMessages > 0) {
        $class = 'class="newMessage"';
        $imageName = 'mail.svg';
    }
    ?>
    <li id="inbox" <?php echo $class; ?> title="<?php echo __('Inbox'); ?>">
    <?php

    $mailIcon = $this->Html->image(
        IMG_PATH . $imageName,
        array(
            "alt" => __('Inbox'),
            "width" => 16,
            "height" => 16
        )
    );


    echo $this->Html->link(
        $mailIcon .' '. $this->Number->format($newMessages),
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
    $logOutIcon = $this->Html->image(
        IMG_PATH . 'log_out.svg',
        array(
            "alt" => __('Log out'),
            "title" => __('Log out'),
            "width" => 14,
            "height" => 14
        )
    );
    echo $this->Html->link(
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
