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
    public $helpers = array('Form', 'Js', 'Html', 'Date');

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
                array(
                    "type" => "text",
                    "label" => "Sentence #"
                )
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
     * Displays the form that lets you add a new annotation to a sentence.
     *
     * @param int $sentenceId Id of the sentence.
     *
     * @return void
     */
    public function displayNewIndexBox($sentenceId)
    {
        ?>
        <div class="module">
        <h2>Add new index</h2>
        <?php
        echo $this->Form->create(
            'SentenceAnnotation', array("action" => "save")
        );
        echo $this->Form->hidden(
            'SentenceAnnotation.sentence_id',
            array("value" => $sentenceId)
        );
        echo $this->Form->input('meaning_id', array('type' => 'text'));
        echo $this->Form->textarea(
            'text',
            array(
                "label" => null,
                "cols" => 24,
                "rows" => 3
            )
        );
        echo $this->Form->end('save');
        ?>
        </div>
        <?php
    }


    /**
     * Display form to replace massively.
     *
     * @param string $stringToReplace String to replace.
     *
     * @return void
     */
    public function displayReplaceBox($stringToReplace)
    {
        ?>
        <div class="module">
        <?php
            echo '<h2>Replace</h2>';

            echo $this->Js->link('sentence_annotations.preview.js', false);
            echo $this->Form->create(
                'SentenceAnnotation', array("action" => "replace")
            );
            echo '<div>';
            echo $this->Form->hidden(
                'SentenceAnnotation.textToReplace',
                array("value" => $stringToReplace)
            );
            echo '</div>';
            echo $this->Form->input(
                'SentenceAnnotation.textReplacing',
                array(
                    "label" => "Replace ". Sanitize::html($stringToReplace) ." by:"
                )
            );
            echo '<div>';
            echo $this->Form->button(
                'Preview', 
                array(
                    "id" => "previewButton",
                    "type" => "button"
                )
            );
            echo '</div>';
            echo $this->Form->end('Replace');
        ?>
        </div>
        <?php
    }


    /**
     * Display log entry.
     *
     * @param int    $sentenceId
     * @param string $text
     * @param string $username
     * @param string $date
     *
     * @return void
     */
    public function displayLogEntry($sentenceId, $text, $username, $date)
    {
        ?>
        <tr>

        <td class="sentenceId">
        <?php
        echo $this->Html->link(
            $sentenceId,
            array(
                'controller' => 'sentence_annotations',
                'action' => 'show',
                $sentenceId
            )
        );
        ?>
        </td>

        <td class="text">
        <?php echo Sanitize::html($text); ?>
        </td>

        <td class="username">
        <?php echo $username; ?>
        </td>

        <td class="date">
        <?php echo $this->Date->ago($date); ?>
        </td>

        </tr>
        <?php
    }
}
?>
