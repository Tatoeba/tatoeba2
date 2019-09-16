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
use App\Model\CurrentUser;

$menu = [
    [
        'label' => __('Profile'),
        'url' => [
            'controller' => 'user',
            'action' => 'profile',
            $username
        ]
    ],
    [
        'label' => __('Sentences'),
        'url' => [
            'controller' => 'sentences',
            'action' => 'of_user',
            $username
        ]
    ],
    [
        'label' => __('Vocabulary'),
        'url' => [
            'controller' => 'vocabulary',
            'action' => 'of',
            $username
        ]
    ],
    [
        'label' => __('Collection'),
        'url' => [
            'controller' => 'collections',
            'action' => 'of',
            $username
        ]
    ],
    [
        'label' => __('Lists'),
        'url' => [
            'controller' => 'sentences_lists',
            'action' => 'of_user',
            $username
        ]
    ],
    [
        'label' => __('Favorites'),
        'url' => [
            'controller' => 'favorites',
            'action' => 'of_user',
            $username
        ]
    ],
    [
        'label' => __('Comments'),
        'url' => [
            'controller' => 'sentence_comments',
            'action' => 'of_user',
            $username
        ]
    ],
    [
        'label' => format(__("Comments on {user}'s sentences"), ['user' => $username]),
        'url' => [
            'controller' => 'sentence_comments',
            'action' => 'on_sentences_of_user',
            $username
        ]
    ],
    [
        'label' => __('Wall messages'),
        'url' => [
            'controller' => 'wall',
            'action' => 'messages_of_user',
            $username
        ]
    ],
    [
        'label' => __('Logs'),
        'url' => [
            'controller' => 'contributions',
            'action' => 'of_user',
            $username
        ]
    ],
    [
        'separator'
    ],
    [
        'label' => __('Audio'),
        'url' => [
            'controller' => 'audio',
            'action' => 'of',
            $username
        ]
    ],
    [
        'label' => __('Transcriptions'),
        'url' => [
            'controller' => 'transcriptions',
            'action' => 'of',
            $username
        ]
    ],
    [
        'separator'
    ],
    [
        'icon' => 'translate',
        'label' => format(__("Translate {user}'s sentences"), ['user' => $username]),
        'url' => [
            'controller' => 'activities',
            'action' => 'translate_sentences_of',
            $username
        ]
    ],
    [
        'icon' => 'email',
        'label' => format(__('Contact {user}'), ['user' => $username]),
        'url' => [
            'controller' => 'private_messages',
            'action' => 'write',
            $username
        ]
    ],
]
?>

<md-list class="annexe-menu md-whiteframe-1dp" ng-cloak>
    <md-subheader><?= $username ?></md-subheader>

    <?php foreach($menu as $item) { 
        if (isset($item['label']) && isset($item['url'])) {
            $url = $this->Url->build($item['url']);
            ?>
            <md-list-item href="<?= $url ?>">
                <md-icon>
                    <?= isset($item['icon']) ? $item['icon'] : 'keyboard_arrow_right' ?>
                </md-icon>
                <p><?= $item['label'] ?></p>
            </md-list-item>
        <?php } else { ?>
            <md-divider></md-divider>
        <?php } ?>    
    <?php } ?>
</md-list>

<br>
