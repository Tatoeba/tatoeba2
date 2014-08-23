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

$this->set('title_for_layout', 'Tatoeba - ' . __('Download list: ', true) . $listName);
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
            <td><?php __('Insert id (optional)'); ?></td>
            <td>
            <?php 
            echo $form->checkbox('insertId');
            ?>
            </td>
            <td>
            <?php 
            __('Checking this box will add the ids of the sentences in the file.'); 
            ?>
            </td>
        </tr>
    
        <tr>
            <td><?php __('Translations (optional)'); ?></td>
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
                    'You can also have translations in a specific language '.
                    '(if they exist). You can then use the file in %s.', true
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
            <?php 
                __(
                    'You can simply click "Download" if you only want the '.
                    'sentences, and nothing else.'
                );
            ?>
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
            'If you choose all options (id + translations), the structure will '.
            'be the following:'
        );
    ?>
    </p>
    <p>
    <span class="param">sentence_id</span>
    <span class="symbol">[tab]</span>
    <span class="param">sentence_text</span>
    <span class="symbol">[tab]</span>
    <span class="param">translation_text</span>
    </p>
    
    <p>
    <?php __("And if you omit an option, the corresponding field won't appear."); ?>
    </p>

    </div>

</div>
