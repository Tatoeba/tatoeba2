<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009 DEPARIS Étienne <etienne.deparis@umaneti.net>
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
 * @author   DEPARIS Étienne <etienne.deparis@umaneti.net>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

$folderName = '';
if ($folder == 'Inbox') {
    if ($status == 'unread') {
        /* @translators: folder name in private messages (noun) */
        $folderName = __('Unread');
    } else {
        /* @translators: folder name in private messages (noun) */
        $folderName = __('Inbox');
    }
} elseif ($folder == 'Sent') {
    /* @translators: folder name in private messages (noun) */
    $folderName = __('Sent');
} elseif ($folder == 'Trash') {
    /* @translators: folder name in private messages (noun) */
    $folderName = __('Trash');
} elseif ($folder == 'Drafts') {
    /* @translators: folder name in private messages (noun) */
    $folderName = __('Drafts');
}

$this->set('title_for_layout', $this->Pages->formatTitle(
    /* @translators: this is used as a title. The folderName can be
       whatever you translated "Inbox", "Sent" or "Trash" as. */
    format(__('Private messages - {folderName}'), compact('folderName'))
));
?>
<md-toolbar class="md-hue-2">
    <div class="md-toolbar-tools">
        <h2 flex><?= __('Private messages') ?></h2>
    </div>
</md-toolbar>

<section layout="row" flex ng-cloak>
    <?= $this->element('pmmenu'); ?>

    <md-content class="md-whiteframe-1dp" flex>
        <md-toolbar class="md-hue-1">
            <div class="md-toolbar-tools">
                <h2 flex>
                    <?php
                    $n = $this->Paginator->param('count');
                    echo format(__n('{folderName} ({n}&nbsp;message)',
                                    '{folderName} ({n}&nbsp;messages)',
                                    $n, true),
                                ['folderName'=> $folderName, 'n'=> $this->Number->format($n)]
                                );
                    ?>
                </h2>

                <?php if ($folder == 'Trash') {
                    $url = $this->Url->build(['empty_folder', 'Trash']);
                    $msg = __('Are you sure?');
                    ?>
                    <md-button href="<?= $url ?>" onclick="return confirm('<?= $msg ?>')">
                        <md-icon>delete_forever</md-icon>
                        <?= __('Empty trash') ?>
                    </md-button>
                <?php } ?>
            </div>
        </md-toolbar>

        <?php
        $this->Pagination->display();
        ?>
        <md-list id="pm-list" ng-cloak>
            <?php
            foreach ($content as $msg) {
                list($user, $label) = $this->PrivateMessages->getUserAndLabel($msg, $folder);

                $unread = $msg->isnonread == 1 ? 'unread' : '';

                if ($msg->title == '') {
                    $messageTitle = __('[no subject]');
                } else {
                    $messageTitle = $msg->title;
                }

                if ($folder == 'Drafts') {
                    $url = $this->Url->build([
                        'action' => 'write',
                        'none',
                        $msg->id
                    ]);
                } else {
                    $url = $this->Url->build([
                        'action' => 'show',
                        $msg->id
                    ]);
                }

                if ($folder == 'Trash') {
                    $restoreUrl = $this->Url->build([
                        'action' => 'restore',
                        $msg->id
                    ]);
                    $deleteConfirmation = 'onclick="return confirm(\''.__('Are you sure?').'\');"';
                    $deleteLabel = __('Permanently delete');
                    $deleteIcon = 'delete_forever';
                } else {
                    $deleteConfirmation = '';
                    /* @translators: delete button on private message (verb) */
                    $deleteLabel = __('Delete');
                    $deleteIcon = 'delete';
                }

                $deleteUrl = $this->Url->build([
                    'action' => 'delete',
                    $msg->id
                ]);
                ?>
                <md-list-item class="md-2-line <?= $unread ?>" href="<?= $url ?>">
                    <?= $this->Members->image($user, array('class' => 'md-avatar')); ?>
                    <div class="md-list-item-text" layout="column">
                        <h3><?= h($this->safeForAngular($messageTitle)) ?></h3>
                        <p>
                        <?php
                        echo $label;
                        echo ', ';
                        echo $this->Date->ago($msg->date);
                        echo '</span>';
                        ?>
                        </p>
                    </div>

                    <?php if ($folder == 'Trash') { ?>
                    <md-button class="md-icon-button" href="<?= $restoreUrl ?>">
                        <md-icon>restore</md-icon>
                        <?php /* @translators: button to restore a private message that has been put to trash (verb) */ ?>
                        <md-tooltip><?= __('Restore') ?></md-tooltip>
                    </md-button>
                    <?php } ?>

                    <md-button class="md-icon-button" href="<?= $deleteUrl ?>" <?= $deleteConfirmation ?>>
                        <md-icon><?= $deleteIcon ?></md-icon>
                        <md-tooltip><?= $deleteLabel ?></md-tooltip>
                    </md-button>
                </md-list-item>
                <?php
            }
            ?>
        </md-list>
        <?php
        $this->Pagination->display();
        ?>
    </div>
</section>
