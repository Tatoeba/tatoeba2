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
 * Helper to display things related to sentence annotations.
 *
 * @category SentenceAnnotations
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class SentenceAnnotationsHelper extends AppHelper
{
    public $helpers = array('Form');
    
    /**
     * Displays the form that lets you search annotations of a specific sentence.
     *
     * @return void
     */
    public function displayGoToBox()
    {
        ?>
        <div class="module">
        <h2>Go to...</h2>
        <?php
            echo $this->Form->create('SentenceAnnotation', array("action" => "show"));
            echo $this->Form->input(
                'sentence_id', 
                array("label" => "Sentence nº")
            );
            echo $this->Form->end('OK');
        ?>
        </div>
        <?php
    }
    
    
    /**
     * Displays the form that lets you search annotations containing a certain
     * string.
     *
     * @return void
     */
    public function displaySearchBox()
    {
        ?>
        <div class="module">
        <h2>Search</h2>
        <?php
            echo $this->Form->create('SentenceAnnotation', array("action" => "search"));
            echo $this->Form->input(
                'text', 
                array(
                    "label" => "",
                    "type" => "text"
                )
            );
            echo $this->Form->end('OK');
        ?>
        </div>
        <?php
    }
    
    
    /**
     * Displays the form that lets you add a new annotation.
     *
     * @return void
     */
    public function displayNewIndexBox()
    {
        ?>
        <div class="module">
        <h2>Add new index</h2>
        <?php
            if(isset($sentence)){
                echo $this->Form->create(
                    'SentenceAnnotation', array("action" => "save")
                );
                echo $this->Form->hidden(
                    'SentenceAnnotation.sentence_id',
                    array("value" => $sentence['Sentence']['id'])
                );
                echo $this->Form->input('meaning_id');			
                echo $this->Form->textarea(
                    'text', 
                    array(
                        "label" => '',
                        "cols" => 24,
                        "rows" => 3
                    )
                );
                echo $this->Form->end('save');
            }
        ?>
        </div>
        <?php
    }
}
?>