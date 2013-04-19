<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2011  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
$this->pageTitle = 'Tatoeba - ' . __('Translate sentences', true);
?>

<div id="annexe_content">    
    <?php
    $attentionPlease->tatoebaNeedsYou();
    ?>    
    
    <div class="module">
    <h2><?php __('About translations'); ?></h2>
    
    <h4><?php __("Good translations"); ?></h4>
    <p>
    <?php __("We know it's difficult, but do NOT translate word for word!"); ?>
    </p>
    
    
    <h4><?php __("Multiple translations"); ?></h4>
    <p>
    <?php
    __(
        "If you feel there are several possible translations, ".
        "you can add several translations in the same language. "
    );
    ?>
    </p>
    </div>
</div>

<div id="main_content">    
    
    <div class="module">
    <h2><?php __('Translate sentences'); ?></h2>
        <p>
        <?php 
        echo sprintf(
            __(
                'Below you can get several random sentences in a certain language. '.
                'Once the sentences are displayed, click on %s to add '.
                'a translation.', true
            ),
            $html->image('translate.png')
        );
        ?>
        </p>
        
        <?php
       
        $numberOfSentencesWanted = array (5 => 5 , 10 => 10 , 15 => 15);
        $selectedLanguage = $session->read('random_lang_selected');
        echo $form->create(
            'Sentence',
            array("action" => "several_random_sentences", "type" => "post")
        );

        echo '<fieldset class="select">';
        echo '<label>' . __('Quantity', true) . '</label>';
        echo $form->select(
            'numberWanted',
            $numberOfSentencesWanted,
            5,
            null,
            false
        );
        echo '</fieldset>';


        echo '<fieldset class="select">';
        echo '<label>' . __('Language', true) . '</label>';
        echo $form->select(
            'into',
            $languages->languagesArray(),
            $selectedLanguage,
            null,
            false
        );
        echo '</fieldset>';

        echo '<fieldset class="submit">';
        echo '<input type="submit" value="'.
            __('show random sentences', true).'"/>';
        echo '</fieldset>';


        echo $form->end();
        ?>
    </div>
</div>