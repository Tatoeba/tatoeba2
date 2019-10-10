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
    $folderName = __('Inbox');
} elseif ($folder == 'Sent') {
    $folderName = __('Sent');
} elseif ($folder == 'Trash') {
    $folderName = __('Trash');
} elseif ($folder == 'Drafts') {
    $folderName = __('Drafts');
}

$this->set('title_for_layout', $this->Pages->formatTitle(
    /* @translators: this is used as a title. The folderName can be
       whatever you translated "Inbox", "Sent" or "Trash" as. */
    format(__('Private messages - {folderName}'), compact('folderName'))
));

echo $this->element('pmmenu');
?>
<div id="main_content">
    <div class="section md-whiteframe-1dp pm_module">
        <h2>
            <?php 
            $n = $this->Paginator->param('count');
            echo format(__n('{folderName} ({n}&nbsp;message)',
                            '{folderName} ({n}&nbsp;messages)',
                            $n, true),
                        compact('folderName', 'n'));
            ?>
        </h2>
        
        <?php
        $this->Pagination->display();
        ?>
        
        <table class="pm_folder">
        <?php

        foreach ($content as $msg) {
            if ($msg->isnonread == 1) {
                 echo '<tr class="messageHeader unread">';
            } else {
                 echo '<tr class="messageHeader">';
            }

            list($user, $label) = $this->Messages->getUserAndLabel($msg, $folder);

            echo '<td class="senderImage">';
            if ($user) {
                $this->Messages->displayAvatar($user);
            } else {
                $this->Messages->displayUnknownAvatar('Recipient not set.');
            }
            echo '</td>';

            if ($msg->title == '') {
                $messageTitle = __('[no subject]');
            } else {
                $messageTitle = $msg->title;
            }

            echo '<td>';
                if ($folder == 'Drafts') {
                    $url = $this->Url->build(
                        array(
                            'action' => 'write',
                            'none',
                            $msg->id
                        )
                    );
                } else {
                    $url = $this->Url->build(
                        array(
                            'action' => 'show',
                            $msg->id
                        )
                    );
                }
                // Title
                echo '<a class="linkToMessage" href="'.$url.'">';
                echo $this->Languages->tagWithLang(
                    'span', '', $messageTitle,
                    array('class' => 'title')
                );
                
                // User and date
                echo '<span class="userAndDate">';
                echo $label;
                echo ', ';
                echo $this->Date->ago($msg->date);
                echo '</span>';
                echo '</a>';
            echo '</td>';
            
            // Restore
            if ($folder == 'Trash') {
                echo '<td>';
                echo $this->Html->link(
                    __('restore'),
                    array(
                        'action' => 'restore',
                        $msg->id
                     )
                );
                echo '</td>';

                $deleteConfirmation = array(
                    'confirm' => __('Are you sure?')
                );
                $deleteLabel = __('permanently delete');
            } else {
                $deleteConfirmation = [];
                $deleteLabel = __('delete');
            }

            // Delete
            echo '<td>';
            echo $this->Html->link(
                $deleteLabel,
                [
                    'action' => 'delete',
                    $msg->id
                ],
                $deleteConfirmation
            );
           echo '</td>';
           
           echo '</tr>';
        }
        ?>
        </table>
        
        <?php
        $this->Pagination->display();
        ?>
    </div>
</div>
