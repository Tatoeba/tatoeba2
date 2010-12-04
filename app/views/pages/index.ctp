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

$this->pageTitle = __('Tatoeba: Collecting example sentences', true);
$html->meta(
    'description', 
    __(
        "Search example sentences translated into many languages. ".
        "Add and translate your own sentences. ".
        "It's collaborative, open, free, and even addictive.",
        true
    ), 
    array(), 
    false
);

$selectedLanguage = $session->read('random_lang_selected');

?>
<div id="annexe_content">
    <div class="module">
        <h2><?php __('Join the community!'); ?></h2>
        <?php
        __(
            "The more contributors there are, the more useful Tatoeba will ".
            "become! Besides, by contributing, not only you will be helpful ".
            "to the rest of the world, but you will also get to learn a lot."
        );
        ?>
        <p>
            <?php
            echo $html->link(
                __('Register', true),
                array("controller" => "users", "action" => "register"),
                array("class" => "registerButton")
            );
            ?>
        </p>
    </div>
    
    <?php
    $attentionPlease->tatoebaNeedsYou();
    ?>
    
    <div class="module">
        <h2><?php __('Some stats'); ?></h2>
        <p>
            <?php
            echo sprintf(
                __('%s contributions today', true),
                $nbrContributions
            );
            echo "<br />";
            echo sprintf(
                __('%s members so far', true),
                $nbrActiveMembers
            );
            echo "<br />";
            echo sprintf(
                __('%s supported languages', true),
                $languages->getNumberOfLanguages()
            );
            ?>
        </p>
    </div>
    

    <?php 
    echo $this->element('sentences_statistics');
    ?>

</div>

<div id="main_content">
    <div class="main_module">
        <h2><?php __('What is Tatoeba?'); ?></h2>
        <p>
            <object 
                type="application/x-shockwave-flash"
                style="width:480px; height:385px;"
                data="http://www.youtube.com/v/ac9SmJuwHqk&amp;hl=en_US&amp;fs=1&amp;"
            >
                <param name="movie" value="http://www.youtube.com/v/ac9SmJuwHqk&amp;hl=en_US&amp;fs=1&amp;" />
                <object 
                    data="http://www.tudou.com/v/FC72iaE81Rs/v.swf"
                    type="application/x-shockwave-flash"
                    style="width:480px; height:385px;"
                >
                </object>
            </object> 

        </p>
        <p>
            <?php
            __(
                'At its core, Tatoeba is a large database of <strong>example '.
                'sentences</strong> translated into several languages. '.
                'But as a whole, it is much more than that.'
            );
            // TODO : write something in the "About
            // echo ' ' . $html->link(__('Learn more...',true), 
            //     array('controller' => 'pages', 'action' => 'about'));
            echo ' ';
            echo $html->link(
                __("See what's happening now.", true),
                array(
                    "controller" => "pages",
                    "action" => "home"
                )
            );
            ?>
        </p>
    </div>

    <div class="module">
        <?php echo $this->element('random_sentence_header'); ?>
        <div class="random_sentences_set">
        <?php
        $sentence = $random['Sentence'];
        $sentenceOwner = $random['User'];

        $sentences->displaySentencesGroup(
            $sentence, 
            $translations, 
            $sentenceOwner,
            $indirectTranslations
        );
        ?>
        </div>
    </div>
</div>

