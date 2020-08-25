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
 * @link     https://tatoeba.org
 */
use App\Model\CurrentUser;

$this->Html->script('elements/user-menu.ctrl.js', ['block' => 'scriptBottom']);
$menu = [
    [
        /* @translators: link to the user's profile in the sidebar */
        'label' => __('Profile'),
        'url' => [
            'controller' => 'user',
            'action' => 'profile',
            $username
        ]
    ],
    [
        /* @translators: link to the user's sentences on profile page sidebar */
        'label' => __('Sentences'),
        'url' => [
            'controller' => 'sentences',
            'action' => 'of_user',
            $username
        ]
    ],
    [
        /* @translators: link to the user's vocabulary on profile page sidebar */
        'label' => __('Vocabulary'),
        'url' => [
            'controller' => 'vocabulary',
            'action' => 'of',
            $username
        ]
    ],
    [
        /* @translators: link to the user's reviews on profile page sidebar */
        'label' => __('Reviews'),
        'url' => [
            'controller' => 'reviews',
            'action' => 'of',
            $username,
            'all',
        ]
    ],
    [
        /* @translators: link to the user's lists on profile page sidebar */
        'label' => __('Lists'),
        'url' => [
            'controller' => 'sentences_lists',
            'action' => 'of_user',
            $username
        ]
    ],
    [
        /* @translators: link to the user's favorite sentences on profile page sidebar */
        'label' => __('Favorites'),
        'url' => [
            'controller' => 'favorites',
            'action' => 'of_user',
            $username
        ]
    ],
    [
        /* @translators: link to the user's sentence comments on profile page sidebar */
        'label' => __('Comments'),
        'url' => [
            'controller' => 'sentence_comments',
            'action' => 'of_user',
            $username
        ]
    ],
    [
        /* @translators: link on profile page sidebar */
        'label' => format(__("Comments on {user}'s sentences"), ['user' => $username]),
        'url' => [
            'controller' => 'sentence_comments',
            'action' => 'on_sentences_of_user',
            $username
        ]
    ],
    [
        /* @translators: link to the user's wall posts on profile page sidebar */
        'label' => __('Wall messages'),
        'url' => [
            'controller' => 'wall',
            'action' => 'messages_of_user',
            $username
        ]
    ],
    [
        /* @translators: link to the user's contributions on profile page sidebar */
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
        /* @translators: link to the list of audio on profile page sidebar */
        'label' => __('Audio'),
        'url' => [
            'controller' => 'audio',
            'action' => 'of',
            $username
        ]
    ],
    [
        /* @translators: link to the list of transcriptions on profile page sidebar */
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
        /* @translators: link text on profile page sidebar */
        'label' => format(__("Translate {user}'s sentences"), ['user' => $username]),
        'url' => [
            'controller' => 'activities',
            'action' => 'translate_sentences_of',
            $username
        ]
    ],
]
?>

<md-list class="annexe-menu md-whiteframe-1dp" ng-cloak ng-controller="UserMenuController">
    <md-subheader ng-click="toggle()">
        <?= $username ?>
        <md-icon>{{ icon }}</md-icon>
    </md-subheader>

    <?php foreach($menu as $item) {
        if (isset($item['label']) && isset($item['url'])) {
            $url = $this->Url->build($item['url']);
            ?>
            <md-list-item ng-show="expanded" href="<?= $url ?>">
                <md-icon>
                    <?= isset($item['icon']) ? $item['icon'] : 'keyboard_arrow_right' ?>
                </md-icon>
                <p><?= $item['label'] ?></p>
            </md-list-item>
        <?php } else { ?>
            <md-divider ng-show="expanded"></md-divider>
        <?php } ?>
    <?php } ?>
</md-list>
