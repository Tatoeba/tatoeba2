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
 * Helper for sentences lists.
 *
 * @category Default
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class ListsHelper extends AppHelper
{
    public $helpers = array('Html');
    
    /** 
     * Display item of a list of lists.
     *
     * @param int     $listId          Id of the list to display.
     * @param string  $listName        Name of the list.
     * @param string  $listCreatorName Name of the list's creator.
     * @param boolean $isPublic        If the list is public or not.
     * @param int     $count           Number of sentences in the list.
     *
     * @return void
     */
    public function displayItem(
        $listId,
        $listName,
        $listCreatorName,
        $isPublic,
        $count = 0
    ) {
        echo '<li>';
        echo '<span id="_'.$listId.'" class="listName">';
        $name = '('.__('unnamed list', true).')';
        if (rtrim($listName) != '') {
            $name = $listName;
        }
        echo $this->Html->link(
            $name,
            array(
                "controller" => "sentences_lists",
                "action" => "edit",
                $listId
            )
        );
        echo '</span><span class="listInfo"> - ';
        echo sprintf(
            __('created by <a href="%s">%s</a>', true),
            $this->Html->url(
                array(
                    "controller"=>"user",
                    "action"=>"profile",
                    $listCreatorName
                )
            ),
            $listCreatorName
        );
        if ($isPublic) {
            echo ' <span class="publicList">'.__('(public list)', true) .'</span>';
        }
        echo '</span>';
        echo '</li>';
    }
    
    /**
     * display an array of lists in an HTML table
     *
     * @param array $arrayOfLists Terrible array of lists with array of array in it
     *
     * @return void
     */

    public function displayListTable($arrayOfLists)
    {
        ?>
        <table>
            <tr>
                <th><?php __('Name'); ?></th>
                <th><?php __('Created by'); ?></th>
                <th><?php __('Number of sentences'); ?></th>
            </tr>
        <?php
        foreach ($arrayOfLists as $list) {
            $this->displayRow(
                $list['SentencesList']['id'],
                $list['SentencesList']['name'],
                $list['User']['username'],
                $list['SentencesList']['is_public'],
                $list['SentencesList']['numberOfSentences']
            );
        }
        ?>
        </table>
        <?php
    }


    /** 
     * Display row of a list of lists.
     *
     * @param int     $listId          Id of the list to display.
     * @param string  $listName        Name of the list.
     * @param string  $listCreatorName Name of the list's creator.
     * @param boolean $isPublic        If the list is public or not.
     * @param int     $count           Number of sentences in the list.
     *
     * @return void
     */

    public function displayRow(
        $listId,
        $listName,
        $listCreatorName,
        $isPublic,
        $count = 0
    ) {

        // list name

        echo '<tr>';
        echo '<td>';
        echo '<span id="_'.$listId.'" class="listName">';
        $name = '('.__('unnamed list', true).')';
        if (rtrim($listName) != '') {
            $name = $listName;
        }

        //  list id  

        echo $this->Html->link(
            $name,
            array(
                "controller" => "sentences_lists",
                "action" => "edit",
                $listId
            )
        );
        echo '</span>';
        echo '</td>';
       
        // creator link 
         
        echo '<td>';
        echo '<span class="listInfo">';
        echo sprintf(
            __('<a href="%s">%s</a>', true),
            $this->Html->url(
                array(
                    "controller"=>"user",
                    "action"=>"profile",
                    $listCreatorName
                )
            ),
            $listCreatorName
        );
        echo '</span>';
        echo '</td>';
       
        // number of sentences in the list
        
        echo "<td>$count</td>";
        echo '</tr>';
    }
}
?>
