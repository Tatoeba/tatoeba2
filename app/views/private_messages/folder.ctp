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
 * @link     http://tatoeba.org
 */
 
$folderName = '';
if ($folder == 'Inbox') {
    $folderName = __('Inbox', true);
} elseif ($folder == 'Sent') {
    $folderName = __('Sent', true);
} elseif ($folder == 'Trash') {
    $folderName = __('Trash', true);
} elseif ($folder == 'Drafts') {
    $folderName = __('Drafts', true);
}

$this->set('title_for_layout', $pages->formatTitle(
    /* @translators: this is used as a title. The folderName can be
       whatever you translated "Inbox", "Sent" or "Trash" as. */
    format(__('Private messages - {folderName}', true), compact('folderName'))
));

echo $this->element('pmmenu');
?>
<div id="main_content">
    <div class="module pm_module">
        <h2>
            <?php 
            $n = $paginator->counter(array('format' => '%count%'));
            echo format(__n('{folderName} ({n}&nbsp;message)',
                            '{folderName} ({n}&nbsp;messages)',
                            $n, true),
                        compact('folderName', 'n'));
            ?>
        </h2>
        
        <?php
        $pagination->display(array($folder));
        ?>
        
        <table class="pm_folder">
        <?php

        foreach ($content as $msg) {
            if ($msg['PrivateMessage']['isnonread'] == 1) {
                 echo '<tr class="messageHeader unread">';
            } else {
                 echo '<tr class="messageHeader">';
            }

            list($user, $label) = $messages->getUserAndLabel($msg, $folder);

            echo '<td class="senderImage">';
            if ($user) {
                $messages->displayAvatar($user);
            } else {
                $messages->displayUnknownAvatar('Recipient not set.');
            }
            echo '</td>';

            if ($msg['PrivateMessage']['title'] == '') {
                $messageTitle = __('[no subject]', true);
            } else {
                $messageTitle = $msg['PrivateMessage']['title'];
            }

            echo '<td>';
                if ($folder == 'Drafts') {
                    $url = $html->url(
                        array(
                            'action' => 'write',
                            'none',
                            $msg['PrivateMessage']['id']
                        )
                    );
                } else {
                    $url = $html->url(
                        array(
                            'action' => 'show',
                            $msg['PrivateMessage']['id']
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
                echo $date->ago($msg['PrivateMessage']['date']);
                echo '</span>';
                echo '</a>';
            echo '</td>';
            
            // Restore
            if ($folder == 'Trash') {
                echo '<td>';
                echo $html->link(
                    __('restore', true),
                    array(
                        'action' => 'restore',
                        $msg['PrivateMessage']['id']
                     )
                );
                echo '</td>';

                $deleteConfirmation = array(
                    'confirm' => __('Are you sure?', true)
                );
                $deleteLabel = __('permanently delete', true);
            } else {
                $deleteConfirmation = null;
                $deleteLabel = __('delete', true);
            }

            // Delete
            echo '<td>';
            echo $html->link(
                $deleteLabel,
                array(
                    'action' => 'delete',
                    $msg['PrivateMessage']['id']
                ),
                $deleteConfirmation
            );
           echo '</td>';
           
           echo '</tr>';
        }
        ?>
        </table>
        
        <?php
        $pagination->display(array($folder));
        ?>
    </div>
</div>
