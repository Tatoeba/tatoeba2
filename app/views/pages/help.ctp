<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

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
$this->pageTitle = 'Tatoeba : ' . __('Help',true);
?>

<div id="annexe_content">
	<div class="module">
		<h2><?php __('Table of contents'); ?></h2>
		<ul>
			<li><a href="#adding"><?php __('Adding new sentences'); ?></a></li>
			<li><a href="#translating"><?php __('Translating sentences'); ?></a></li>
			<li><a href="#correcting"><?php __('Correcting mistakes'); ?></a></li>
			<li><a href="#adopting"><?php __('Adopting sentences'); ?></a></li>
		</ul>
	</div>
	
	<div class="module">
		<h2><?php __('Need more help?'); ?></h2>
		<p><?php __('If you cannot find the answer to your question, do not hesitate to contact us.'); ?></p>
		<p class="more_link"><?php echo $html->link('Contact us', array("controller"=>"pages", "action"=>"contact")); ?></p>
	</div>
</div>

<div id="main_content">
	<a name="adding"></a>
	<div class="module">
		<h2><?php __('Adding new sentences'); ?></h2>
		<p><?php __(''); ?></p>
		<p><?php __(''); ?></p>
	</div>
	
	<a name="translating"></a>
	<div class="module">
		<h2><?php __('Translating sentences'); ?></h2>
		<p><?php __(''); ?></p>
		<p><?php __(''); ?></p>
	</div>
	
	<a name="correcting"></a>
	<div class="module">
		<h2><?php __('Correcting mistakes'); ?></h2>
		<p><?php __(''); ?></p>
		<p><?php __(''); ?></p>
	</div>
	
	<a name="adopting"></a>
	<div class="module">
		<h2><?php __('Adopting sentences'); ?></h2>
		<p><?php __(''); ?></p>
		<p><?php __(''); ?></p>
	</div>
</div>

