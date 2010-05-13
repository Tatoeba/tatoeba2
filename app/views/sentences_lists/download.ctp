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
    
    <h3><?php __('Download'); ?></h3>
    
    <p>
    NOTE: You can just click "Download" if you simply want the sentences, and 
    nothing else.
    </p>
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
            <td><?php __('Download with id'); ?></td>
            <td>
            <?php 
            echo $form->checkbox('insertId');
            ?>
            </td>
        </tr>
    
        <tr>
            <td><?php __('Translations language'); ?></td>
            <td>
            <?php
            $langArray = $languages->languagesArrayWithNone();
            echo $form->select(
                'TranslationsLang',
                $langArray,
                null,
                null,
                false
            );
            ?>
            </td>
        </tr>
    </table>
    <?php
    echo $form->end(__('Download',true));
    // -------------------------------------------
    ?>
    
    
    
    <h3>Description</h3>
    <p>
    You can download lists to use them in Anki.
    </p>
    <div><a href="http://www.ichi2.net/anki/">
    <img alt="Anki" src="http://ichi2.net/anki/anki-logo2.png"/></a>
    </div>
    
    <p>
    We are also using this feature in our process of
    <a href="http://blog.tatoeba.org/2010/04/reliability-of-sentences-how-will-we.html">
    checking and correcting sentences</a> massively.
    </p>
        
        
    <h3>Fields and structure</h3>
    <p>
    If you choose all options (id + translations), the structure will be the 
    following:
    </p>
    <p>
    <span class="param">sentence_id</span>
    <span class="symbol">[tab]</span>
    <span class="param">sentence_text</span>
    <span class="symbol">[tab]</span>
    <span class="param">translation_text</span>
    </p>
    
    <p>
    And if you omit an option, the corresponding field won't appear.
    </p>

    </div>

</div>