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

$this->pageTitle = 'Tatoeba - ' . __('Contribute', true);

echo $javascript->link(JS_PATH . 'sentences.show_another.js', false);
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
        "If you feel there are several possible translations, note that for a ".
        "same sentence, you can add several translations in the same language."
    );
    ?>
    </p>
    </div>

    <div class="module">
        <h2><?php __('Important to read'); ?></h2>
        <ol>
            <li>
            <a href="http://blog.tatoeba.org/2010/02/how-to-be-good-contributor-in-tatoeba.html">
            <?php __('Guide of the good contributor'); ?>
            </a>
            </li>
            
            <li>
            <a href="http://blog.tatoeba.org/2010/05/moderators-in-tatoeba.html">
            <?php __('Role of moderators'); ?>
            </a>
            </li>
            
            <li>
            <a href="http://blog.tatoeba.org/2010/04/reliability-of-sentences-how-will-we.html">
            <?php __('Reliability of the sentences'); ?>
            </a>
            </li>
        </ol>
    </div>

</div>

<div id="main_content">    
    
    <div class="module">
        <h2><?php __('Add new sentences'); ?></h2>
        <?php
        echo $form->create(
            'Sentence', array("action" => "add", "id" => "newSentence")
        );
        echo $form->input('text', array("label" => __('Sentence : ', true)));

        // permit users to directly specify in which language they contribute
        $langArray = $languages->translationsArray();
        $preSelectedLang = $session->read('contribute_lang');

        if (empty($preSelectedLang)) {
            $preSelectedLang = 'auto';
        }
        
        echo '<div class="languageSelection">';
        echo $form->select(
            'contributionLang',
            $langArray,
            $preSelectedLang,
            array("class"=>"translationLang"),
            false
        );
        echo '</div>';
        
        echo $form->end('OK');
        ?>
        
        <p>
        <?php
        __(
            "You can add sentences that you do not know how to translate. ".
            "Perhaps someone else will know!"
        );
        ?>
        </p>
    </div>
    
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
       
        //TODO : it's a hack need to find a better way to produce
        // a cakephp url with a normal form 
        echo $form->create(
            'Sentence',
            array("action" => "show_all_in/none/none/none/indifferent", "type" => "post")
        );

        echo '<fieldset class="select">';
        echo '<label>' . __('Language', true) . '</label>';
        echo $form->select(
            'into',
            $languages->languagesArrayWithNone(),
            $preSelectedLang,
            null,
            false
        );
        echo '</fieldset>';

        echo '<fieldset class="submit">';
        echo '<input type="submit" value="'.
            __('show sentences', true).'"/>';
        echo '</fieldset>';


        echo $form->end();
        ?>
    </div>
    
    
    <div class="module">
        <h2><?php __('Check and correct sentences'); ?></h2>
        <p>
        <?php
        __(
            'We want to check and correct in priority sentences that do not belong '.
            'to anyone. Our current strategy is to use the "adoption" system. '.
            'Adopting is a way to vote "this sentence is correct".'
        );
        ?>
        </p>
        
        <p>
        <?php
        echo sprintf(
            __(
                'So if you want to help us check and correct sentences, then adopt '.
                '(%s) any "orphan" sentence you see in your <strong>native '.
                'language</strong>, and correct it if necessary. '.
                'Read <a href="%s">this</a> for further explanations.', true
            ),
            $html->image('adopt.png'),
            'http://blog.tatoeba.org/2010/04/reliability-of-sentences-how-will-we.html'
        )
        ?>
        </p>
    </div>
    
</div>
