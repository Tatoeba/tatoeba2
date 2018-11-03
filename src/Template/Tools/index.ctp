<?php
/**
 * Tatoeba Project, free collaborative creation of languages corpuses project
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

$this->set('title_for_layout', $this->Pages->formatTitle(__('Useful tools')));
?>
<div id="main_content">
    <div class="module">
        <h2><?php echo __("Tools"); ?></h2>

        <ul>
            <li>
                <?php
                echo $this->Html->link(
                    __('Furigana autogeneration'),
                    array(
                        "controller" => "tools",
                        "action" => "furigana"
                    )
                );
                echo ': ';
                echo __("autogenerate furigana over Japanese.");
                ?>
            </li>
            <li>
                <?php
                echo $this->Html->link(
                    __('Sinogram search'),
                    array(
                        "controller" => "tools",
                        "action" => "search_hanzi_kanji"
                    )
                );
                echo ': ';
                echo __("search all Chinese characters / kanjis by all possible methods.");
                ?>
            </li>
            <li>
                <?php
                echo $this->Html->link(
                    __('Chinese simplified/traditional conversion'),
                    array(
                        "controller" => "tools",
                        "action" => "conversion_simplified_traditional_chinese"
                    )
                );
                echo ': ';
                echo __(
                    "convert traditional Chinese to simplified and vice versa"
                );
                ?>
            </li>
            <li>
                <?php
                echo $this->Html->link(
                    __('Shanghainese to IPA'),
                    array(
                        "controller" => "tools",
                        "action" => "shanghainese_to_ipa"
                    )
                );
                echo ': ';
                echo __(
                    "Convert a Shanghainese text into its pronunciation ".
                    "using the International Phonetic Alphabet"
                );
                ?>
            </li>
        </ul>
    </div>
</div>
