<?php
/**
 * Tatoeba Project, free collaborativ creation of languages corpuses project
 * Copyright (C) 2009 Allan SIMON <allan.simon@supinfo.com>
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
 * @author   Allan Simon <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

$this->pageTitle = 'Tatoeba - ' . __('Useful tools', true);
?>
<div id="annexe_content" >
    <?php
    $attentionPlease->tatoebaNeedsYou();
    ?>
    
    <div class="module" >
        <h2><?php __("Need a tool?"); ?></h2>

        <p>
            <?php
            __(
                "Well, Tatoeba's developers don't have time to think of every".
                " possible useful tools for language learners."
            );
            ?>
        </p>
        
        <p>
            <?php
            echo sprintf(
                __(
                    "So if there is a must-have tool which is missing,or if you".
                    " have yourself coded something you think can help others".
                    " (as long as you can provide it under a GPL compatible".
                    " licence) don't hesitate to talk about it <a href=\"%s\">".
                    "here</a>, we're always looking for new stuff.",
                    true
                ),
                "/wall/index"
            ); 
            ?>
        </p>

    </div>
</div>

<div id="main_content">
    <div class="module">
        <h2><?php __("Tools"); ?></h2>

        <ul>
            <li>
                <?php 
                echo $html->link(
                    __('Romaji/furigana conversion', true),
                    array(
                        "controller" => "tools",
                        "action" => "romaji_furigana"
                    )
                );
                echo ': ';
                __("convert japanese to romaji or furigana.");
                ?>
            </li>
            <li>
                <?php
                echo $html->link(
                    __('Sinogram search', true),
                    array(
                        "controller" => "tools",
                        "action" => "search_hanzi_kanji"
                    )
                );
                echo ': ';
                __("search all Chinese characters / kanjis by all possible ways.");
                ?>
            </li>
            <li>
                <?php
                echo $html->link(
                    __('Pinyin converter', true),
                    array(
                        "controller" => "tools",
                        "action" => "pinyin_converter"
                    )
                );
                echo ': ';
                __(
                    "convert chinese to pinyin, numerical pinyin ".
                    "to diacritical pinyin etc."
                );
                ?>
            </li>  
            <li>
                <?php
                echo $html->link(
                    __('Chinese simplified/traditional conversion', true),
                    array(
                        "controller" => "tools",
                        "action" => "conversion_simplified_traditional_chinese"
                    )
                );
                echo ': ';
                __(
                    "convert traditional chinese to simplified and vice versa"
                );
                ?>
            </li>
            <li>
                <?php
                echo $html->link(
                    __('Shanghainese to IPA', true),
                    array(
                        "controller" => "tools",
                        "action" => "shanghainese_to_ipa"
                    )
                );
                echo ': ';
                __(
                    "Convert a shanghainese text into its pronunciation ".
                    "using the International Phonetic Alphabet"
                );
                ?>
            </li>  
        </ul>
    </div>
</div>
