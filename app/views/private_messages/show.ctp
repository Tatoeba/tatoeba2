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
	<div class="module">
	<h2><?php echo $content['title']; ?></h2>

	<?php
	if($content['folder'] == 'Trash') $delOrRestLink = $html->link(__('Restore', true), array('action' => 'restore', $content['id']));
			else $delOrRestLink = $html->link(__('Delete', true), array('action' => 'delete', $content['folder'], $content['id']));

	echo $this->element('pmtoolbox', array(
						'extralink' => '<li>' . $html->link(__('Reply', true), array('action' => 'write', $content['from'], $content['id'])) . '</li>
		<li>' . $delOrRestLink . '</li>
		<li>' . $html->link(__('Mark unread', true), array('action' => 'mark', 'Inbox', $content['id'])) . '</li>'
	)); ?>

	<p class="pm_head">
		<?php echo $date->ago($content['date']) . ' ' .
		$html->link($content['from'], array('action' => 'write', $content['from'])) . __(' has written:', true); ?>
	</p>
	<p class="pm_content"><?php echo $content['content']; ?></p>
	</div>
</div>
