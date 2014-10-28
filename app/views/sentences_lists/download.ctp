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

$this->set('title_for_layout', $pages->formatTitle(__('Download list: ', true) . $listName));
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
    
    
    <h3><?php __('Download'); ?></h3>
    <?php
    // ------------- DOWNLOAD FORM -------------
    echo $form->create(
        'SentencesList',
        array(
            'action' => 'export_to_csv',
            'class' => 'downloadForm'
        )
    );
    ?>
    
    <div>
    <?php
    echo $form->hidden(
        'id',
        array('value' => $listId)
    );
    ?>
    </div>
    
    <table>
        <tr>
            <td><?php __('Id (optional)'); ?></td>
            <td>
            <?php 
            echo $form->checkbox('insertId');
            ?>
            </td>
            <td>
            <?php 
            __('If you check this box, the id of each sentence will be written to the output.'); 
            ?>
            </td>
        </tr>
    
        <tr>
            <td><?php __('Translation (optional)'); ?></td>
            <td>
            <?php
            $langArray = $languages->languagesArrayWithNone();
            echo $form->select(
                'TranslationsLang',
                $langArray,
                null,
                array(
                    'class' => 'language-selector',
                    "empty" => false
                ),
                false
            );
            ?>
            </td>
            <td>
            <?php
            $image = $html->image(
                'anki-logo.png',
                array(
                    'alt' => 'Anki',
                    'title' => 'Anki'
                )
            );
            $link = $html->link(
                $image,
                'http://www.ichi2.net/anki/',
                array(
                    "escape" => false
                )
            );
            echo sprintf(
                __(
                    'If you select a language, the translation of each sentence into that language '.
                    '(if it exists) will be written to your output. '.
                    'You can then import the file to produce a deck of flash cards, using the %s program.', true
                ), 
                $link
            );
            ?>
            </td>
        </tr>
        
        <tr>
            <td></td>
            
            <td>
            <?php
            echo $form->submit(__('Download',true));
            ?>
            </td>
            
            <td>
            </td>
        </tr>
    </table>
    <?php
    echo $form->end();
    // -------------------------------------------
    ?>
    
    
    <h3><?php __('Fields and structure'); ?></h3>
    <p>
    <?php
        __(
            'Fields will be written out in the following sequence:'
        );
    ?>
    </p>
    <p>
    <span class="param"><em>sentence_id</em></span>
    <span class="symbol"><em>[tab]</em></span>
    <span class="param">sentence_text</span>
    <span class="symbol"><em>[tab]</em></span>
    <span class="param"><em>translation_text</em></span>
    </p>
    
    <p>
    <?php __("Optional fields that are not selected above will not be written to the output."); ?>
    </p>

    </div>

</div>
