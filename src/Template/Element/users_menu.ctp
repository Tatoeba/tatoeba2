<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2011  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
?>

<div class="section" md-whiteframe="1">
    <h2><?php echo $username; ?></h2>

    <ul class="annexeMenu">
        <li class="item">
        <?php
        echo $this->Html->link(
            __('Profile'),
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
            __('Sentences'),
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
                __('Vocabulary'),
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
            __('Collection'),
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
            __('Transcriptions'),
            array(
                'controller' => 'transcriptions',
                'action' => 'of',
                $username
            )
        );
        ?>
        </li>

        <li class="item">
        <?php
        echo $this->Html->link(
            __('Lists'),
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
            __('Favorites'),
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
            __('Audio'),
            array(
                'controller' => 'audio',
                'action' => 'of',
                $username
            )
        );
        ?>
        </li>

        <li class="item">
        <?php
        echo $this->Html->link(
            __('Comments'),
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
            format(__("Comments on {user}'s sentences"), array('user' => $username)),
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
            __('Wall messages'),
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
            __('Logs'),
            array(
                'controller' => 'contributions',
                'action' => 'of_user',
                $username
            )
        );
        ?>
        </li>
    </ul>

    <div class="profile-actions">
    <?php
    $translateIcon = $this->Images->svgIcon(
        'translate', array('width' => 20, 'height' => 20)
    );
    $translateText = $this->Html->tag('span', format(
        __("Translate {user}'s sentences"), array('user' => $username)
    ));
    echo $this->Html->link(
        $translateIcon . $translateText,
        array(
            'controller' => 'activities',
            'action' => 'translate_sentences_of',
            $username
        ),
        array(
            'escape' => false,
            'class' => 'profile-action-item'
        )
    );


    if ($username != CurrentUser::get('username')) {
        $contactIcon = $this->Images->svgIcon(
            'pm', array('width' => 20, 'height' => 20)
        );
        $contactText = $this->Html->tag('span', format(
            __('Contact {user}'), array('user' => $username)
        ));
        echo $this->Html->link(
            $contactIcon . $contactText,
            array(
                'controller' => 'private_messages',
                'action' => 'write',
                $username
            ),
            array(
                'escape' => false,
                'class' => 'profile-action-item'
            )
        );
    }
    ?>
    </div>
</div>
