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
 
$langArray = $languages->onlyLanguagesArray();
?>

<div id="main_content">

    <div class="module">
        <h2>Single sentences</h2>
        
        <?php 
        echo $form->create(
            'SingleSentences',
            array('action' => 'toto') // TODO Rename with correct action
        ); 
        ?>
        
        <div>
        Language of the sentences: 
        <?php echo $form->select('sentences_lang', $langArray); ?>
        </div>
        
        <div>
        File: <?php echo $form->file('file'); ?>
        </div>
        
        <?php echo $form->input('user_id'); ?>
        
        <?php echo $form->end('import'); ?>
    </div>
    
    
    
    <div class="module">
        <h2>Sentences and translations</h2>
        <?php 
        echo $form->create(
            'SentencesAndSentences',
            array('action' => 'toto') // TODO Rename with correct action
        ); 
        ?>
        
        <div>
        Language of the sentences:
        <?php echo $form->select('sentences_lang', $langArray); ?>
        </div>
        
        <div>
        Language of the translations:
        <?php echo $form->select('translations_lang', $langArray); ?>
        </div>
        
        <div>
        File: <?php echo $form->file('file'); ?>
        </div>
        
        <?php echo $form->input('user_id'); ?>
        
        <?php echo $form->end('import'); ?>
    </div>
    
    
</div>