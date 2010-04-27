<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
    public $helpers = array('Html', 'Javascript', 'Form', 'Languages');
    
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
        <table class="listTable">
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
        echo '<td class="name">';
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
        echo '</td>';
       
        // creator link 
         
        echo '<td class="creator">';
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
        echo '</td>';
       
        // number of sentences in the list
        
        echo "<td class='count'>$count</td>";
        echo '</tr>';
    }
    
    
    /** 
     * Display actions that can be done by everyone.
     *
     * @param int    $listId           Id of the list.
     * @param string $translationsLang Language of the translations for the
     *                                 'correction version'.
     * @param string $action           Can be 'show' or 'edit'.
     *
     * @return void
     */
    public function displayPublicActions($listId, $translationsLang, $action)
    {
        ?>
        <li>
        <?php
        echo $this->Html->link(
            __('Back to all the lists', true),
            array(
                "controller"=>"sentences_lists", 
                "action"=>"index"
            )
        );
        ?>
        </li>

        <li>
        <?php
        __('Show translations :'); echo ' ';
        
        $path  = '/' . Configure::read('Config.language') . 
            '/sentences_lists/'.$action.'/'. $listId.'/';
        
        // TODO onChange should be define in a separate js file
        // TODO use of languagesArray is a hack as for the moment
        // "all languages" is always the first selected, so you're not able to
        // select
        // it would be better to do the following 
        //  1 - set the previous selected language or "none" by default 
        //      (create a specific method to in language helper)
        //  2 - "all languages" would display all translations 
        //    - "none" would display only the sentence
        echo $this->Form->select(
            "translationLangChoice",
            $this->Languages->languagesArray(),
            $translationsLang,
            array(
                "onchange" => "$(location).attr('href', '".$path."' + this.value);"
            ),
            false
        );
        ?>
        </li>
        <?php
    }
    
    /** 
     * Display actions that are restricted to the creator of the list.
     *
     * @param int $listId       Id of the list.
     * @param int $isListPublic true if list is public. false otherwise.
     *
     * @return void
     */
    public function displayRestrictedActions($listId, $isListPublic = false)
    {
        ?>
        <li>
        <label for="isPublic"><?php __('Set list as public'); ?></label>
        <?php
        $this->Javascript->link('sentences_lists.set_as_public.js', false);
        if ($isListPublic) {
            $checkboxValue = 'checked';
        } else {
            $checkboxValue = '';
        }
        
        echo $this->Form->checkbox(
            'isPublic',
            array(
                "name" => "isPublic", 
                "checked" => $checkboxValue
            )
        );
        echo $this->Html->image(
            'loading-small.gif',
            array("id"=>"inProcess", "style"=>"display:none;")
        );
        echo $this->Html->link(
            '[?]',
            array(
                "controller"=>"pages", 
                "action"=>"help#sentences_lists"
            )
        );
        ?>
        </li>
        
        <li class="deleteList">
        <?php
        echo $this->Html->link(
            __('Delete this list', true),
            array(
                "controller" => "sentences_lists",
                "action" => "delete",
                $listId
            ),
            null,
            __('Are you sure?', true)
        );
        ?>
        </li>
        <?php
    }
    
    
    /** 
     * Display links to printable versions.
     *
     * @param int    $listId           Id of the list.
     * @param string $translationsLang Language of the translations for the
     *                                 'correction version'.
     *
     * @return void
     */
    public function displayLinksToPrintableVersions($listId, $translationsLang)
    {
        ?>
        <ul class="sentencesListActions">
        <li>
            <?php
            echo $this->Html->link(
                __('Print as exercise', true),
                array(
                    "controller"=>"sentences_lists",
                    "action"=>"print_as_exercise",
                    $listId,
                    'hide_romanization'
                ),
                array(
                    "onclick" => "window.open(this.href,‘_blank’);return false;",
                    "class" => "printAsExerciseOption"
                )
            );
            ?>
        </li>
        <li>
            <?php
            if (!isset($translationsLang)) { 
                $translationsLang = 'und';
            }
            echo $this->Html->link(
                __('Print as correction', true),
                array(
                    "controller"=>"sentences_lists",
                    "action"=>"print_as_correction",
                    $listId,
                    $translationsLang,
                    'hide_romanization'
                ),
                array(
                    "onclick" => "window.open(this.href,‘_blank’);return false;",
                    "class" => "printAsCorrectionOption"
                )
            );
            ?>
        </li>
        <li>
            <?php
            $this->Javascript->link('sentences_lists.romanization_option.js', false);
            echo $this->Form->checkbox(
                'display_romanization',
                array(
                    "id" => "romanizationOption", 
                    "class" => "display"
                )
            );
            echo ' ';
            __('Check this box to display romanization in the print version');
            ?>
        </li>
        </ul>
        <?php
    }
}
?>
