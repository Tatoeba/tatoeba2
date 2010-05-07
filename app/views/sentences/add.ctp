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

$this->pageTitle = 'Tatoeba - ' . __('Add sentences', true);

echo $javascript->link('sentences.add_translation.js', true);
echo $javascript->link('sentences.contribute.js', true);
echo $javascript->link('favorites.add.js', true);
echo $javascript->link('sentences_lists.menu.js', true);
echo $javascript->link('sentences.adopt.js', true);
echo $javascript->link('jquery.jeditable.js', true);
echo $javascript->link('sentences.edit_in_place.js', true);
echo $javascript->link('sentences.play_audio.js', true);
echo $javascript->link('sentences.change_language.js', true);
?>
<div id="main_content">

    <div class="module">
        <h2><?php __('Add another sentence'); ?></h2>
        <div class="sentences_set">
            <div class="new">
            <?php
            echo $form->input(
                'text', 
                array(
                    "label" => __('Sentence : ', true),
                    "id" => "SentenceText"
                )
            );
            $langArray = $languages->translationsArray();
            $preSelectedLang = $session->read('contribute_lang');

            if (empty($preSelectedLang)) {
                $preSelectedLang = 'auto';
            }
            ?>
            
            <div class="languageSelection">
            <?php
            echo $form->select(
                'contributionLang',
                $langArray,
                $preSelectedLang,
                array("class"=>"translationLang"),
                false
            );
            ?>
            </div>
            
            <?php
            echo $form->button('OK', array("id" => "submitNewSentence"));
            ?>
            </div>
        </div>
    </div>
    
    <div class="module">
        <h2><?php __('Sentences added'); ?></h2>
        
        <div class="sentencesAddedloading" style="display:none">
        <?php echo $html->image('loading.gif'); ?>
        </div>
        
        <div id="sentencesAdded">
        <?php
        if (isset($sentence)) {
            $sentenceId = $sentence['Sentence']['id'];
            $ownerName = $sentence['User']['username']; 
            ?>
            <div class="sentences_set" 
                id="sentences_group_<?php echo $sentenceId; ?>">
                
            <?php
            // sentence menu (translate, edit, comment, etc)
            $menu->displayMenu($sentenceId, $ownerName);

            // sentence and translations
            $translation = array();
            if (isset($sentence['Translation'])) {
                $translation = $sentence['Translation'];
            }
            
            // TODO set up a better mechanism
            $sentence['User']['canEdit'] = $specialOptions['canEdit']; 
            $sentences->displayGroup(
                $sentence['Sentence'],
                $translation,
                $sentence['User']
            );
            ?>
            </div>
            <?php
        }
        ?>
        </div>
    </div>
</div>