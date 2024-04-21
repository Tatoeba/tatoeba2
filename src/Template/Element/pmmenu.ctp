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
$newMessageUrl = $this->Url->build(['action' => 'write']);
$inboxUrl = $this->Url->build(['action' => 'folder', 'Inbox']);
$unreadUrl = $this->Url->build(['action' => 'folder', 'Inbox', 'unread']);
$dratftsUrl = $this->Url->build(['action' => 'folder', 'Drafts']);
$sentUrl = $this->Url->build(['action' => 'folder', 'Sent']);
$trashUrl = $this->Url->build(['action' => 'folder', 'Trash']);

$isTrashFolder = $this->request->getParam('action') == 'folder'
    && $this->request->getParam('pass')
    && $this->request->getParam('pass')[0] == 'Trash';
?>

<md-sidenav class="md-sidenav-left md-whiteframe-1dp"
            md-component-id="left"
            md-is-locked-open="true">
    <div layout="column" layout-margin>
        <md-button class="md-raised md-primary" href="<?= $newMessageUrl ?>">
            <md-icon>email</md-icon>
            <?php /* @translators: button to compose a new private message (verb) */ ?>
            <?= __('Compose') ?>
        </md-button>
    </div>

    <md-list class="annexe-menu" ng-cloak>
        <md-list-item href="<?= $inboxUrl ?>">
            <md-icon>keyboard_arrow_right</md-icon>
            <p><?= __('Inbox') ?></p>
        </md-list-item>

        <md-list-item href="<?= $unreadUrl ?>">
            <md-icon>keyboard_arrow_right</md-icon>
            <p><?= __('Unread') ?></p>
        </md-list-item>
            
        <md-list-item href="<?= $dratftsUrl ?>">
            <md-icon>keyboard_arrow_right</md-icon>
            <p><?= __('Drafts') ?></p>
        </md-list-item>

        <md-list-item href="<?= $sentUrl ?>">
            <md-icon>keyboard_arrow_right</md-icon>
            <p><?= __('Sent') ?></p>
        </md-list-item>

        <md-list-item href="<?= $trashUrl ?>">
            <md-icon>keyboard_arrow_right</md-icon>
            <p><?= __('Trash') ?></p>
        </md-list-item>
    </md-list>
</md-sidenav>
