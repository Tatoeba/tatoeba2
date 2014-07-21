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
        <h2><?php __('Single sentences'); ?></h2>
        
        <?php 
        echo $form->create(
            null,
            array(
                'url' => array(
                    'controller' => 'imports',
                    'action' => 'import_single_sentences'
                ),
                'type' => 'file',
            ) 
        ); 
        ?>
        
        <div>
        <?php __('Language of the sentences:');
          echo $form->select('sentences_lang', $langArray); ?>
        </div>
        
        <div>
        <?php __('File:'); echo $form->file('file'); ?>
        </div>
 
        <div>  
        <?php echo $form->input('user_id',
           array('label' => __('Numeric user id: ', true))); ?>
        </div>

        <?php echo $form->end('import',
           array('label' => __('import', true))); ?>
    </div>
    
    
    
    <div class="module">
        <h2><?php __('Sentences and translations'); ?></h2>
        <?php 
        echo $form->create(
            null,    
            array(
                'url' => array(
                    'controller' => 'imports',
                    'action' => 'import_sentences_with_translation'
                ),
                'type' => 'file',
            )
        ); 
        ?>
        
        <div>
        <?php __('Language of the sentences:');
        echo $form->select('sentences_lang', $langArray); ?>
        </div>
        
        <div>
        <?php __('Language of the translations:');
        echo $form->select('translations_lang', $langArray); ?>
        </div>
        
        <div>
        <?php __('File:'); echo $form->file('file'); ?>
        </div>

        <div>
        <?php echo $form->input('user_id',
           array('label' => __('Numeric user id: ', true))); ?>
        </div>
        
        <?php echo $form->end('import',
        array('label' => __('import', true))); ?>
    </div>
    
    
</div>
