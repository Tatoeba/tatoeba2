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
$this->pageTitle = __('Private messages', true) . ' - ' . __($folder, true);

echo $this->element('pmmenu');
?>
<div id="main_content">
	<div class="module pm_module">
        <h2>
        <?php
        if ($folder == 'Inbox') {
            __('Inbox');
        } elseif ($folder == 'Sent') {
            __('Sent');
        } elseif ($folder == 'Trash') {
            __('Trash');
        }
        ?>
		</h2>
        
		<?php echo $this->element('pmtoolbox'); ?>
		<table class="pm_folder">
		<?php
        echo '<tr><th>'.__('Date', true).'</th>';

        if ($folder == 'Sent') {
            echo '<th>'.__('to', true).'</th>';
        } else {
            echo '<th>'.__('from', true).'</th>';
        }
        echo '<th>'.__('Subject', true).'</th><th></th></tr>';

        foreach ($content as $msg) {
            if ($msg['PrivateMessage']['isnonread'] == 1) {
                 echo '<tr class="pm_folder_line unread">';
            } else {
                 echo '<tr class="pm_folder_line">';
            }
            echo '<td>' ;
                echo $html->link(
                    $date->ago($msg['PrivateMessage']['date']),
                    array('
                        action' => 'show',
                        $msg['PrivateMessage']['id']
                    )
                );
            echo '</td>';

            /* Used to display properly the name of the sender, or receiver
             * while we are in Sent or other folder.
			 * NOTA: the caps to the word 'Sent' is IMPORTANT.
			 */
            if ($folder != 'Sent') {
                $username = $msg['Sender']['username'];
            } else {
                $username = $msg['Recipient']['username'];
            }
            echo '<td>';
            echo $html->link($username, array('action' => 'write', $username));
            echo '</td>';

            if ($msg['PrivateMessage']['title'] == '') {
                $messageTitle = __('[no subject]', true);
            } else {
                $messageTitle = $msg['PrivateMessage']['title'];
            }

            echo '<td>' .
                $html->link(
                    $messageTitle,
                    array(
                        'action' => 'show',
                        $msg['PrivateMessage']['id']
                    )
                )
                .'</td>';
            echo '<td><span class="action_link">';

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

            if ($msg['PrivateMessage']['isnonread'] == 1) {
                $label = __('Mark as read', true);
            } else {
                $label = __('Mark as unread', true);
            }
            
            echo ' - ' .
                $html->link(
                    $label,
                    array(
                        'action' => 'mark',
                        $folder, $msg['PrivateMessage']['id']
                    )
                )
            . '</span></td></tr>';
        }
        ?>
		</table>
	</div>
</div>