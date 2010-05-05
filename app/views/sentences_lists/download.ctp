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
 * Page for people to export lists.
 *
 * @category Wall
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */ 

$listName = 'toto';  // TODO Remove me
$this->pageTitle = 'Tatoeba - ' . __('Download list: ', true) . $listName;
?>
<div id="annexe_content">
    <div class="module">
    <h2><?php __('Actions'); ?></h2>
    <ul class="sentencesListActions">
    <?php 
        $lists->displayBackToIndexLink(); 
        
        $lists->displayBackToListLink($listId); 
    ?>
    </ul>
    </div>
</div>

<div id="main_content">
    <div class="module">
    <h2><?php echo $listName; ?></h2>
    
    <h3>Simple download</h3>
    <dl>
        <dt><?php __('Download'); ?></dt>
        <dd>
            <?php
            $simpleFileName = 'simple_'.$listName.'.csv';
            echo $html->link(
                $simpleFileName,
                array(
                    'controller' => 'sentences_lists',
                    'action' => 'test', // TODO Rename accordingly
                    $simpleFileName
                )
            );
            ?>
        </dd>
        
        <dt>Fields and structure</dt>
        <dd>
            <span class="param">sentence_id</span>
            <span class="symbol">[tab]</span>
            <span class="param">text</span>
        </dd>
        
        <dt>Description</dt>
        <dd>
            This is simply a file with all the sentences in the list. We have 
            integrated this feature mostly in order to start checking sentences 
            massively.
        </dd>
    </dl>    
    
    <h3>Download for Anki</h3>
    <dl>
        <dt><?php __('Download'); ?></dt>
        <dd>
            <?php
            $ankiFileName = 'anki_'.$listName.'.csv';
            echo $html->link(
                $ankiFileName,
                array(
                    'controller' => 'sentences_lists',
                    'action' => 'test', // TODO Rename accordingly
                    $ankiFileName
                )
            );
            ?>
        </dd>
        
        <dt>Fields and structure</dt>
        <dd>
            <span class="param">sentence_text</span>
            <span class="symbol">[tab]</span>
            <span class="param">translation_text</span>
        </dd>
        
        <dt>Description</dt>
        <dd>
            This file is formatted in a way that you can import it into Anki.
        </dd>
    </dl>
    </div>

</div>