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
?>

<div id="annexe_content">
    <div class="module">
    <h2><?php __('Actions'); ?></h2>
    <ul class="sentencesListActions">
        <li>
            <?php
            echo $html->link(
                __('Back to all the lists', true),
                array("controller"=>"sentences_lists", "action"=>"index")
            );
            ?>
        </li>
        <li>
            <?php
            echo $html->link(
                __('Send via Private Message', true),
                array(
                    'controller' => 'private_messages',
                    'action' => 'join',
                    'list',
                    $list['SentencesList']['id']
                )
            );
            ?>
        </li>

        <li>
            <?php
            __('Show translations :');
            echo ' ';
            $langArray = $languages->languagesArray();
            asort($langArray);
            $path  = '/' . Configure::read('Config.language') .
                '/sentences_lists/show/' . $list['SentencesList']['id'] . '/';
            echo $form->select(
                "translationLangChoice",
                $langArray,
                null,
                array(
                    "onchange" => "$(location).attr('href', '"
                    .$path."' + this.value);"
                )
            );
            ?>
        </li>
    </ul>
    </div>


    <div class="module">
    <h2><?php __('Printable versions'); ?></h2>
    <ul class="sentencesListActions">
        <li>
            <?php
            echo $html->link(
                __('Print as exercise', true),
                array(
                    "controller"=>"sentences_lists",
                    "action"=>"print_as_exercise",
                    $list['SentencesList']['id'],
                    'hide_romanization'
                ),
                array(
                    "onclick" => "window.open(this.href,‘_blank’);return false;",
                    "class" => "printAsExerciseOption"
                )
            );
            ?>
        </li>
        <li>
            <?php
            if (!isset($translationsLang)) { 
                $translationsLang = 'und';
            }
            echo $html->link(
                __('Print as correction', true),
                array(
                    "controller"=>"sentences_lists",
                    "action"=>"print_as_correction",
                    $list['SentencesList']['id'],
                    $translationsLang,
                    'hide_romanization'
                ),
                array(
                    "onclick" => "window.open(this.href,‘_blank’);return false;",
                    "class" => "printAsCorrectionOption"
                )
            );
            ?>
        </li>
        <li>
            <?php
            $javascript->link('sentences_lists.romanization_option.js', false);
            echo $form->checkbox(
                'display_romanization',
                array("id" => "romanizationOption", "class" => "display")
            );
            echo ' ';
            __('Check this box to display romanization in the print version');
            ?>
        </li>
    </ul>
    </div>
</div>

<div id="main_content">
    <div class="module">
    <h2><?php echo $list['SentencesList']['name']; ?></h2>

    <?php
    if (count($list['Sentence']) > 0) {
        echo '<ul class="sentencesList">';
        if ($translationsLang == 'und') {
            $translationsLang = null;
        }
        foreach ($list['Sentence'] as $sentence) {
            echo '<li id="sentence'.$sentence['id'].'">';
            $sentences->displaySentenceInList($sentence, $translationsLang);
            echo '</li>';
        }
        echo '</ul>';
    } else {
        __('This list does not have any sentence');
    }
    ?>
    </div>
</div>
