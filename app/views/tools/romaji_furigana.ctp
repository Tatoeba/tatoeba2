<?php
/**
 * Tatoeba Project, free collaborative creation of languages corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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

$javascript->link(JS_PATH . 'furigana.js', false);

$this->set('title_for_layout', 'Tatoeba - '. __(
    'Convert Japanese text into romaji or furigana', true
));

?>

<div id="annexe_content">
    <?php
    $attentionPlease->tatoebaNeedsYou();
    ?>

    <div class="module">
        <h2><?php __('Credits'); ?></h2>
        <p>
            <?php
            echo sprintf(
                __('This tool is powered by <a href="%s">MeCab</a>.',true),
                'http://mecab.sourceforge.net/feature.html'
            );

            ?>
        </p>
    </div>

    <div class="module">
    <h2><?php __('More information'); ?></h2>
    <ul>
        <li>
            <a href="http://blog.tatoeba.org/2010/04/japanese-romanization-in-tatoeba-now.html">
               <?php __('Japanese romanization in Tatoeba, now using MeCab'); ?>
            </a>
        </li>
        <li>&nbsp;</li>
        <li>
            <a href="http://blog.tatoeba.org/2009/02/tools-for-japanese-romanization.html">
                <?php __('Tools for Japanese romanization'); ?>
            </a>
        </li>
    </ul>
    </div>
</div>

<div id="main_content">
    <div class="module">
        <h2><?php __('Convert Japanese text into romaji or furigana'); ?></h2>
        <?php

        echo '<div id="conversion" class="furigana">';
        echo $result;
        echo '</div>';

        echo $form->create(
            'Tool',
            array(
                "action" => "romaji_furigana",
                "type" => "get"
            )
        );
        ?>
        <p>
        <?php
        echo $form->textarea(
            'query',
            array(
                "value" => $query,
                "rows" => 30,
                "cols"=> 40
            )
        );
        ?>
        </p>
        <p>
            <?php
            __('Convert Japanese text into: ');
            echo $form->radio(
                'type',
                array(
                    'romaji' => __('romaji', true),
                    'furigana' => __('furigana', true)
                ),
                array(
                    'value' => $type,
                    'legend' => ''
                )
            );
            ?>
        </p>
        <?php echo $form->end(__('Convert', true)); ?>
    </div>
</div>
