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

/* 
 * Menu items for sentences.
 */

class MenuHelper extends AppHelper {

	var $helpers = array('Html');
	
	function translateButton(){
		echo '<li class="option translateLink">';
		echo '<a>' . $this->Html->image(
			'translate.png', 
			array(
				'alt'=>__('Translate',true), 
				'title'=>__('Translate',true)
			)
		) . '</a>';
		echo '</li>';
	}
	
	function adoptButton($sentenceId){
		echo '<li class="option">';
		echo $this->Html->link(
			$this->Html->image(
				'adopt.png',
				array(
					'alt'=>__('Adopt',true), 
					'title'=>__('Adopt',true)
				)
			),
			array(
				"controller" => "sentences",
				"action" => "adopt",
				$sentenceId
			),
			array('escape' => false));
		echo '</li>';
	}
	
	function letGoButton($sentenceId){
		echo '<li class="option">';
		echo $this->Html->link(
			$this->Html->image(
				'let_go.png',
				array(
					'alt'=>__('Let go',true), 
					'title'=>__('Let go',true)
				)
			),
			array(
				"controller" => "sentences",
				"action" => "let_go",
				$sentenceId
			),
			array('escape' => false));
		echo '</li>';
	}
	
	function commentsButton($sentenceId){
		echo '<li class="option">';
		echo $this->Html->link(
			$this->Html->image(
				'comments.png',
				array(
					'alt'=>__('Comments',true), 
					'title'=>__('Comments',true)
				)
			),
			array(
				"controller" => "sentence_comments",
				"action" => "show",
				$sentenceId
			),
			array('escape' => false));
		echo '</li>';	
	}
	
	function favoriteButton($sentenceId){
		echo '<li class="option favorite add" id="favorite_'.$sentenceId.'">';
		echo '<a>'.$this->Html->image(
			'favorite.png',
			array(
				'alt'=>__('Add to favorites',true), 
				'title'=>__('Add to favorites',true)
			)).'</a>';
		echo '</li>';
	}
	
	function unfavoriteButton($sentenceId){
		echo '<li class="option favorite remove" id="favorite_'.$sentenceId.'">';
		echo '<a>'.$this->Html->image(
			'unfavorite.png',
			array(
				'alt'=>__('Remove from favorites',true), 
				'title'=>__('Remove from favorites',true)
			)).'</a>';
		echo '</li>';
	}
	
	function addToListButton(){
		echo '<li class="option addToList">';
		echo '<a>' . $this->Html->Image(
			'add_to_list.png',
			array(
				'alt'=>__('Add to list',true), 
				'title'=>__('Add to list',true)
			)).'</a>';
		echo '</li>';
	}
	
	function deleteButton($sentenceId){
		echo '<li class="option delete">';
		echo $this->Html->link(
			$this->Html->image(
				'delete.png',
				array(
					'alt'=>__('Delete',true), 
					'title'=>__('Delete',true)
				)
			),
			array(
				"controller" => "sentences",
				"action" => "delete",
				$sentenceId
			), 
			array('escape' => false), 
			'Are you sure?');
		echo '</li>';
	}
}
?>