<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  TATOEBA Project(should be changed)

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
if (isset($this->params['lang'])) { 
	Configure::write('Config.language',  $this->params['lang']); 
}
$lang = (isset($this->params['lang']))? $this->params['lang'].'/' : '';

// array containing the elements of the menu : $title => $route
$menuElements = array(
	 __('Home',true) 		=> '/'.$lang
	,__('Browse',true) 		=> array("controller" => "sentences", "action" => "show", "random")
	,__('Search',true) 		=> array("controller" => "sentences", "action" => "search")
	,__('Contribute',true) 	=> array("controller" => "pages", "action" => "contribute")
	,__('Comments',true) 	=> array("controller" => "sentence_comments", "action" => "index")
	,__('Members',true)		=> array("controller" => "users", "action" => "all")
	,__('What\'s new',true)	=> array("controller" => "pages", "action" => "whats_new")
);
//	echo $this->element('sentences_statistics', array('cache' => 
//			array(
//				'time' => '+6 hours', 
//				'key' => $key
//			)
//		)
//	); 
//echo $this->element('sentences_statistics');
?>

<div id="header">
	<a href="/"><?php echo $html->image('logo_header.png'); ?></a>
</div>
