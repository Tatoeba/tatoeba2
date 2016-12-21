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
    echo $html->link(
        __('Profile', true),
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
        __('Sentences', true),
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
            __('Vocabulary', true),
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
        __('Collection', true),
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
        __('Transcriptions', true),
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
    echo $html->link(
        __('Lists', true),
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
        __('Favorites', true),
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
        __('Audios', true),
        array(
            'controller' => 'audios',
            'action' => 'of',
            $username
        )
    );
    ?>
    </li>
    
    <li class="item">
    <?php
    echo $html->link(
        __('Comments', true),
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
        format(__("Comments on {user}'s sentences", true), array('user' => $username)),
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
        __('Wall messages', true),
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
        __('Logs', true),
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
    $translateIcon = $images->svgIcon(
        'translate', array('width' => 20, 'height' => 20)
    );
    $translateText = $html->tag('span', format(
        __("Translate {user}'s sentences", true), array('user' => $username)
    ));
    echo $html->link(
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
        $contactIcon = $images->svgIcon(
            'pm', array('width' => 20, 'height' => 20)
        );
        $contactText = $html->tag('span', format(
            __('Contact {user}', true), array('user' => $username)
        ));
        echo $html->link(
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
