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

$isTrashFolder = $this->request->params['action'] == 'folder'
    && $this->request->params['pass']
    && $this->request->params['pass'][0] == 'Trash';
?>
<div id="annexe_content">
    <md-list class="annexe-menu md-whiteframe-1dp" ng-cloak>
        <md-subheader><?= __('Private messages') ?></md-subheader>
        
        <md-list-item href="<?= $newMessageUrl ?>">
            <md-icon>email</md-icon>
            <p><?= __('New message') ?></p>
        </md-list-item>

        <?php if ($isTrashFolder) { 
            $url = $this->Url->build(['empty_folder', 'Trash']);
            $msg = __('Are you sure?');
            ?>
            <md-list-item href="<?= $url ?>" onclick="return confirm('<?= $msg ?>')">
                <md-icon>delete_forever</md-icon>
                <p><?= __('Empty trash') ?></p>
            </md-list-item>
        <?php } ?>

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
</div>
