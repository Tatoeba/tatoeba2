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


$javascript->link('sentences.contribute.js', false);
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
                    "id" => "newSentenceText"
                )
            );
            $langArray = $languages->translationsArray();
            $preSelectedLang = $session->read('contribute_lang');

            if (empty($preSelectedLang)) {
                $preSelectedLang = 'auto';
            }
            echo $form->select(
                'contributionLang',
                $langArray,
                $preSelectedLang,
                array("class"=>"translationLang"),
                false
            );

            echo $form->button('OK', array("id" => "submitNewSentence"));
            ?>
            </div>
        </div>
    </div>
    
    <div class="module">
        <?php
        echo '<h2>';
        __('Sentences added');
        echo '</h2>';
        
        echo '<div class="sentencesAddedloading" style="display:none">';
        echo $html->image('loading.gif');
        echo '</div>';
        
        echo '<div id="sentencesAdded">';
        if (isset($sentence)) {
                echo '<div class="sentences_set">';
                // sentence menu (translate, edit, comment, etc)
                $sentences->displayMenu(
                    $sentence['Sentence']['id'],
                    $sentence['Sentence']['lang'],
                    $specialOptions,
                    null,
                    $sentence['Sentence']['script']
                );

                // sentence and translations
                $translation = array();
            if ( isset($sentence['Translation'])) {
                    $translation = $sentence['Translation'];
            }
                //pr($specialOptions);
                // TODO set up a better mechanism
                $sentence['User']['canEdit'] = $specialOptions['canEdit']; 
                $sentences->displayGroup(
                    $sentence['Sentence'],
                    $translation,
                    $sentence['User']
                );
                echo '</div>';
        }
        echo '</div>';
        ?>
    </div>
</div>

