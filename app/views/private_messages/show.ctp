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
<div id="main_modules">
	<div class="module">
	<h2><?php echo $content['title']; ?></h2>
	<p class="pm_head">
		<?php echo __('On ', true) . $content['date'] .
		$html->link($content['from'], array('action' => 'write', $content['fromid'])) . __(' has written:', true); ?>
	</p>
	<p class="pm_content"><?php echo $content['content']; ?></p>
	</div>
</div>
