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

$user = CurrentUser::get('User');
$username = $user['username'];

$newMessages = TableRegistry::get('PrivateMessages')->numberOfUnreadMessages(
    CurrentUser::get('id')
);
$emailIcon = $newMessages > 0 ? 'email' : 'mail_outline';
$uiLanguage = $this->Languages->getInterfaceLanguage();
if (!isset($htmlDir)) {
    $htmlDir = null;
}
$menuPositionMode = $htmlDir == 'rtl' ? 'target target' : 'target-right target';
?>

<div layout="row" layout-align="center center">
    <div class="private-messages">
        <md-button class="md-icon-button" href="<?= $this->Url->build(['controller' => 'private_messages', 'action' => 'folder', 'Inbox']) ?>">
            <md-icon><?= $emailIcon ?></md-icon>
            <md-tooltip><?php echo __('Inbox'); ?></md-tooltip>
        </md-button>
        <?php if ($newMessages > 0) { ?>
        <span class="unread">
            <?= $this->Number->format($newMessages) ?>
        </span>
        <?php } ?>
    </div>

    <div id="user-menu" class="dropdown">
        <div class="label">
            <?= $this->Members->image($user, ['width' => 24, 'height' => 24]); ?>
            <span><?= $username ?></span>
        </div>
        <div class="dropdown-content">
            <div class="item">
                <a href="<?= $this->Url->build(['controller' => 'user', 'action' => 'profile', $username]) ?>">
                    <md-icon md-colors="::{color: 'grey'}">person</md-icon>
                    <span>
                    <?php
                    /* @ŧranslators: top-right user menu item to open your profile page */
                    echo __('My profile');
                    ?>
                    </span>
                </a>
            </div>
            <div class="item">
                <a ng-click="showInterfaceLanguageSelection()">
                    <md-icon md-colors="::{color: 'grey'}">language</md-icon>
                    <span>
                    <?= format(__('Language: {lang}'), ['lang' => $uiLanguage]) ?>
                    </span>
                </a>
            </div>
            <div class="item">
                <a href="<?= $this->Url->build(['controller' => 'user', 'action' => 'settings']) ?>">
                    <md-icon md-colors="::{color: 'grey'}">settings</md-icon>
                    <span>
                    <?php
                    /* @ŧranslators: top-right user menu item to open your settings */
                    echo __('Settings');
                    ?>
                    </span>
                </a>
            </div>
            <div class="item">
                <a href="<?= $this->Url->build(['controller' => 'users', 'action' => 'logout']) ?>">
                    <md-icon md-colors="::{color: 'grey'}">power_settings_new</md-icon>
                    <span>
                    <?= __('Log out') ?>
                    </span>
                </a>
            </div>

            <md-divider></md-divider>

            <div class="item">
                <a href="<?= $this->Url->build(['controller' => 'sentences', 'action' => 'of_user', $username]) ?>">
                    <span>
                    <?php
                    /* @ŧranslators: top-right user menu item to list your sentences */
                    echo __('My sentences');
                    ?>
                    </span>
                </a>
            </div>
            <div class="item">
                <a href="<?= $this->Url->build(['controller' => 'vocabulary', 'action' => 'of', $username]) ?>">
                    <span>
                    <?php
                    /* @ŧranslators: top-right user menu item to list your vocabulary */
                    echo __('My vocabulary');
                    ?>
                    </span>
                </a>
            </div>
            <div class="item">
                <a href="<?= $this->Url->build(['controller' => 'reviews', 'action' => 'of', $username, 'all']) ?>">
                    <span>
                    <?php
                    /* @ŧranslators: top-right user menu item to list your reviews */
                    echo __('My reviews');
                    ?>
                    </span>
                </a>
            </div>
            <div class="item">
                <a href="<?= $this->Url->build(['controller' => 'sentences_lists', 'action' => 'of_user', $username]) ?>">
                    <span>
                    <?php
                    /* @ŧranslators: top-right user menu item to list your lists */
                    echo __('My lists');
                    ?>
                    </span>
                </a>
            </div>
            <div class="item">
                <a href="<?= $this->Url->build(['controller' => 'favorites', 'action' => 'of_user', $username]) ?>">
                    <span>
                    <?php
                    /* @ŧranslators: top-right user menu item to list your favorites */
                    echo __('My favorites');
                    ?>
                    </span>
                </a>
            </div>
            <div class="item">
                <a href="<?= $this->Url->build(['controller' => 'sentence_comments', 'action' => 'of_user', $username]) ?>">
                    <span>
                    <?php
                    /* @ŧranslators: top-right user menu item to list your comments */
                    echo __('My comments');
                    ?>
                    </span>
                </a>
            </div>
            <div class="item">
                <a href="<?= $this->Url->build(['controller' => 'sentence_comments', 'action' => 'on_sentences_of_user', $username]) ?>">
                    <span>
                    <?php
                    /* @ŧranslators: top-right user menu item to list comments made on your sentences */
                    echo __("Comments on my sentences");
                    ?>
                    </span>
                </a>
            </div>
            <div class="item">
                <a href="<?= $this->Url->build(['controller' => 'wall', 'action' => 'messages_of_user', $username]) ?>">
                    <span>
                    <?php
                    /* @ŧranslators: top-right user menu item to list your wall posts */
                    echo __('My Wall messages');
                    ?>
                    </span>
                </a>
            </div>
            <div class="item">
                <a href="<?= $this->Url->build(['controller' => 'contributions', 'action' => 'of_user', $username]) ?>">
                    <span>
                    <?php
                    /* @ŧranslators: top-right user menu item to list your contributions */
                    echo __('My latest contributions');
                    ?>
                    </span>
                </a>
            </div>
        </div>
        </div>
 </div>
