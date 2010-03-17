<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Helper to display buttons in sentences menu.
 *
 * @category Sentences
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class MenuHelper extends AppHelper
{

	public $helpers = array('Html');
	
    /** 
     * Display button to add a translation.
     *
     * @return void
     */
	function translateButton()
    {
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


    /** 
     * Display button to notify the chinese sentence is in
     * simplified script
     *
     * @return void
     */
	function simplifiedButton()
    {
		echo '<li class="option simplified">';
		echo '<a>' . $this->Html->image(
			'simplified_chinese.png', 
			array(
				'alt'=>__('This sentence is in simplified chinese',true), 
				'title'=>__('This sentence is in simplified chinese',true)
			)
		) . '</a>';
		echo '</li>';
	}

    /** 
     * Display button to notify the chinese sentence is in
     * traditional script
     *
     * @return void
     */
	function traditionalButton()
    {
		echo '<li class="option traditional">';
		echo '<a>' . $this->Html->image(
			'traditional_chinese.png', 
			array(
				'alt'=>__('This sentence is in traditional chinese',true), 
				'title'=>__('This sentence is in traditional chinese',true)
			)
		) . '</a>';
		echo '</li>';
	}



    /** 
     * Display button to adopt a sentence.
     *
     * @return void
     */
	function adoptButton($sentenceId)
    {
		echo '<li class="option adopt add" id="adopt_'.$sentenceId.'">';
		echo '<a>'.
        $this->Html->image(
			'adopt.png',
			array(
				'alt'=>__('Adopt',true), 
				'title'=>__('Adopt',true)
			)
        ).'</a>';
		echo '</li>';
	}
	
    /** 
     * Display button to let go.
     *
     * @return void
     */
	function letGoButton($sentenceId)
    {
		echo '<li class="option adopt remove" id="adopt_'.$sentenceId.'">';
		echo '<a>'.
        $this->Html->image(
			'let_go.png',
			array(
				'alt'=>__('Let go',true), 
				'title'=>__('Let go',true)
			)
        ).'</a>';
		echo '</li>';
	}
	
    /** 
     * Display button to add to favorites.
     *
     * @return void
     */
	function favoriteButton($sentenceId)
    {
		echo '<li class="option favorite add" id="favorite_'.$sentenceId.'">';
		echo '<a>'.$this->Html->image(
			'favorite.png',
			array(
				'alt'=>__('Add to favorites',true), 
				'title'=>__('Add to favorites',true)
			)).'</a>';
		echo '</li>';
	}
	
    /** 
     * Display button to remove from favorites.
     *
     * @return void
     */
	function unfavoriteButton($sentenceId)
    {
		echo '<li class="option favorite remove" id="favorite_'.$sentenceId.'">';
		echo '<a>'.$this->Html->image(
			'unfavorite.png',
			array(
				'alt'=>__('Remove from favorites',true), 
				'title'=>__('Remove from favorites',true)
			)).'</a>';
		echo '</li>';
	}
	
    /** 
     * Display button to add to list.
     *
     * @return void
     */
	function addToListButton()
    {
		echo '<li class="option addToList">';
		echo '<a>';
        echo $this->Html->Image(
			'add_to_list.png',
			array(
				'alt'=>__('Add to list',true), 
				'title'=>__('Add to list',true)
			)
        );
        echo '</a>';
		echo '</li>';
	}
	
    /** 
     * Display button to delete.
     *
     * @return void
     */
	function deleteButton($sentenceId)
    {
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
