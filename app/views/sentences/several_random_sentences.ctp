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

$this->set('title_for_layout', $pages->formatTitle(__('Random sentences', true)));

?>
<div id="annexe_content">
    <div class="module">
        <h2><?php __("For serial translators"); ?></h2>
        <?php 
        echo '<p>';
        __(
            'Translating sentences one by one is too slow for you? '.
            'You want to increase your rank in Tatoeba, or the rank of '.
            'your language, at the speed of light? So this is for you!'
        );
        echo '</p>';
        
        echo '<p>';
        __("Just keep in mind that our server is not as fast as you ;-)");
        echo '</p>';

        $numberOfSentencesWanted = array (5 => 5 , 10 => 10 , 15 => 15);
        $selectedLanguage = $session->read('random_lang_selected');
        
        echo $form->create(
            'Sentence', 
            array(
                "action" => "several_random_sentences",
                "type" => "post"
            )
        );

        echo '<fieldset class="select">';
        echo '<label>' . __('Quantity', true) . '</label> ';
        echo $form->select(
            'numberWanted', 
            $numberOfSentencesWanted, 
            $lastNumberChosen,
            array(
                'empty'=>false
            )
        );
        echo '</fieldset>';


        echo '<fieldset class="select">';
        echo '<label>' . __('Language', true) . '</label> ';
        echo $form->select(
            'into', 
            $languages->languagesArrayAlone(), 
            $selectedLanguage,
            array(
                'class' => 'language-selector',
                "empty" => false
            ),
            false
        );
        echo '</fieldset>';

        echo '<fieldset class="submit">';
        echo '<input type="submit" value="'.__('show random sentences', true).'"/>';
        echo '</fieldset>';
        echo $form->end();
        ?>
    </div>
</div>    
    
    
<div id="main_content">
    <div class="module">
    <?php 
    if(!isset($searchProblem)) { 
        $this->Html->tag('h2', 'Random sentences', null);
            foreach ($allSentences as $index=>$sentence) {
                $sentences->displaySentencesGroup(
                    $sentence['Sentence'],
                    $sentence['Transcription'],
                    $sentence['Translations'],
                    $sentence['User'],
                    $sentence['IndirectTranslations']
                );
            }
    } else if($searchProblem == 'error') {
        echo $this->Html->tag('h2', 'There was an error while performing this function.', null);
    } else if($searchProblem == 'disabled') {
        echo $this->Html->tag('h2', 'This feature is temporarily disabled. Please try later', null);
    }
    ?>

    </div>
</div>


