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

class ListsHelper extends AppHelper {

	var $helpers = array('Html');
	
	function displayItem($list){
		echo '<li>';
		echo '<span id="_'.$list['SentencesList']['id'].'" class="listName">';
		$name = '('.__('unnamed list', true).')';
		if(rtrim($list['SentencesList']['name']) != ''){
			$name = $list['SentencesList']['name'];
		}
		echo $this->Html->link(
			$name,
			array("controller" => "sentences_lists", "action" => "edit", $list['SentencesList']['id'])
		);
		echo '</span><span class="listInfo"> - ';
		echo sprintf(
			__('created by <a href="%s">%s</a>', true)
			, $this->Html->url(array("controller"=>"user", "action"=>"profile", $list['User']['username']))
			, $list['User']['username']
		);
		if($list['SentencesList']['is_public']){
			echo ' <span class="publicList">' . __('(public list)', true) . '</span>';
		}
		echo '</span>';
		echo '</li>';
	}
	
}
?>