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
}

$this->set('title_for_layout', __('Private messages', true) . ' - ' . $folderName);

echo $this->element('pmmenu');
?>
<div id="main_content">
	<div class="module pm_module">
        <h2>
            <?php 
            echo $folderName;
            echo ' ';
            echo $paginator->counter(
                array(
                    'format' => __('(total %count%)', true)
                )
            ); 
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

            /* Used to display properly the name of the sender, or receiver
             * while we are in Sent or other folder.
			 * NOTA: the caps to the word 'Sent' is IMPORTANT.
			 */
            if ($folder != 'Sent') {
                $username = $msg['Sender']['username'];
                $userImage = $msg['Sender']['image'];
                $label = sprintf(__('from %s', true), $username);
            } else {
                $username = $msg['Recipient']['username'];
                $userImage = $msg['Recipient']['image'];
                $label = sprintf(__('to %s', true), $username);
            }
            echo '<td class="senderImage">';
            $wall->displayMessagePosterImage($username, $userImage);
            echo '</td>';

            if ($msg['PrivateMessage']['title'] == '') {
                $messageTitle = __('[no subject]', true);
            } else {
                $messageTitle = $msg['PrivateMessage']['title'];
            }
            
            echo '<td>';
                $url = $html->url(
                    array(
                        'action' => 'show',
                        $msg['PrivateMessage']['id']
                    )
                );
                // Title
                echo '<a class="linkToMessage" href="'.$url.'">';
                echo '<span class="title">';
                echo $messageTitle;
                echo '</span>';
                
                // User and date
                echo '<span class="userAndDate">';
                echo $label;
                echo ', ';
                echo $date->ago($msg['PrivateMessage']['date']);
                echo '</span>';
                echo '</a>';
            echo '</td>';
            
            // Delete
            echo '<td>';
            if ($folder == 'Trash') {
                echo $html->link(
                    __('Restore', true),
                    array(
                        'action' => 'restore',
                        $msg['PrivateMessage']['id']
                     )
                );
            } else {
                echo $html->link(
                    __('Delete', true),
                    array(
                        'action' => 'delete',
                        $folder, $msg['PrivateMessage']['id']
                    )
                );
            }
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