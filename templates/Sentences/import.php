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
 * @link     https://tatoeba.org
 */
 
$this->set('title_for_layout', $this->Pages->formatTitle(__d('admin', 'Import sentences')));

$langArray = $this->Languages->onlyLanguagesArray();
?>

<div id="main_content">

    <div class="module">
        <h2><?php echo __d('admin', 'Single sentences'); ?></h2>
        
        <?php 
        echo $this->Form->create(
            null,
            array(
                'url' => array(
                    'action' => 'import_single_sentences'
                ),
                'type' => 'file',
            ) 
        ); 
        ?>
        
        <div>
        <?php echo __d('admin', 'Language of the sentences:');
          echo $this->Form->select('sentences_lang', $langArray); ?>
        </div>
        
        <div>
        <?php echo __d('admin', 'File:'); echo $this->Form->file('file'); ?>
        </div>
 
        <div>  
        <?php echo $this->Form->control('user_id',
           array(
               'type' => 'text',
               'label' => __d('admin', 'Numeric user id: ')
           )
        ); ?>
        </div>

        <?php echo $this->Form->submit(__d('admin', 'import')); ?>
        <?php echo $this->Form->end(); ?>
    </div>
    
    
    
    <div class="module">
        <h2><?php echo __d('admin', 'Sentences and translations'); ?></h2>
        <?php 
        echo $this->Form->create(
            null,
            array(
                'url' => array(
                    'action' => 'import_sentences_with_translation'
                ),
                'type' => 'file',
            )
        ); 
        ?>
        
        <div>
        <?php echo __d('admin', 'Language of the sentences:');
        echo $this->Form->select('sentences_lang', $langArray); ?>
        </div>
        
        <div>
        <?php echo __d('admin', 'Language of the translations:');
        echo $this->Form->select('translations_lang', $langArray); ?>
        </div>
        
        <div>
        <?php echo __d('admin', 'File:'); echo $this->Form->file('file'); ?>
        </div>

        <div>
        <?php echo $this->Form->control('user_id',
           array(
               'type' => 'text',
               'label' => __d('admin', 'Numeric user id: ')
           )
        ); ?>
        </div>
        
        <?php echo $this->Form->submit(__d('admin', 'import')); ?>
        <?php echo $this->Form->end(); ?>
    </div>
    
    
</div>
