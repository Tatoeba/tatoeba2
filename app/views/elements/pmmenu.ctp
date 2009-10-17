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

?>
<div id="second_modules">
	<div class="module">
		<ul>
			<li><?php echo $html->link(__('Inbox', true), array('action' => 'folder', 'Inbox')); ?></li>
			<li><?php echo $html->link(__('Sent', true), array('action' => 'folder', 'Sent')); ?></li>
			<li><?php echo $html->link(__('Trash', true), array('action' => 'folder', 'Trash')); ?></li>
		</ul>
	</div>
</div>
