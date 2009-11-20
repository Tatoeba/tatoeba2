<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009 Etienne Deparis <etienne.deparis@umaneti.net>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
echo $this->element('pmmenu');
?>
<div id="main_content">
	<div class="module pm_module">
		<h2><?php echo __($folder, true); ?></h2>
		<?php echo $this->element('pmtoolbox'); ?>
		<table class="pm_folder">
		<?php
		echo '<tr><th>'.__('Date', true).'</th>';

		if($folder == 'Sent')
			echo '<th>'.__('To', true).'</th>';
		else
			echo '<th>'.__('From', true).'</th>';

		echo '<th>'.__('Subject', true).'</th><th></th></tr>';

		foreach($content as $msg){
			if($msg['isnonread'] == 1)
                 echo '<tr class="pm_folder_line unread">';
			else
                 echo '<tr class="pm_folder_line">';
                 
			echo '<td>' . $html->link($date->ago($msg['date']), array('action' => 'show', $msg['id'])) . '</td>';

            if($folder == 'sent'){
			    echo '<td>'.$html->link($msg['from'], array('action' => 'create', $msg['from'])).'</td>';
            } else {
                // maybe it will be better to link to the rcpt profile, but as I don't know what "create" is supposed to do
			    echo '<td>'.$html->link($msg['to'], array('action' => 'create', $msg['to'])).'</td>';
            }

			echo '<td>' . $html->link($msg['title'], array('action' => 'show', $msg['id'])) . '</td>';
            echo '<td><span class="action_link">';

			if($folder == 'Trash')
                 echo $html->link(__('Restore', true), array('action' => 'restore', $msg['id']));
			else
                 echo $html->link(__('Delete', true), array('action' => 'delete', $folder, $msg['id']));

			if($msg['isnonread'] == 1)
                 $label = __('Mark as read', true);
			else
                 $label = __('Mark as unread', true);

			echo ' - ' . $html->link($label, array('action' => 'mark', $folder, $msg['id'])) . '</span></td></tr>';
		} ?>
		</table>
	</div>
</div>
