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

$this->set('title_for_layout', $pages->formatTitle(__('Pinyin converter', true)));

// if it's the first time we call this tool
if (!isset($lastText)) {
    $lastText = '';
}

?>

<div id="main_content">
    <div class="module">
         <h2><?php __('Pinyin converter'); ?></h2>
        <?php
        if (isset($convertedText)) {
            if ($this->data['Tool']['to'] === 'diacPinyin') {
                $convertedText = $this->Pinyin->numeric2diacritic($convertedText);
            }

            echo $languages->tagWithLang(
                'div', 'zh', $convertedText,
                array('id' => 'conversion'),
                'Latn'
            );
        }

        echo $form->create(
            'Tool',
            array(
                "action" => "pinyin_converter",
                "type" => "post"
            )
        );
        ?>
        <p>
        <?php
        echo $form->textarea(
            'query',
            array(
                "value" => $lastText,
                "rows" => 30,
                "cols"=> 40,
                "lang"=> "zh",
                "dir"=> "ltr",
            )
        );
        ?>
        </p>
        <p>
            <?php
            __('Convert text from: ');
            echo $form->radio(
                'from',
                array(
                    'chinese' => __('Chinese characters', true),
                    'numPinyin' => __('numerical pinyin', true),
                ),
                array(
                    'value' => 'chinese',
                    'legend' => ''
                )
            );
            ?>
        </p>
        <p>
            <?php
            __('Convert text to: ');
            echo $form->radio(
                'to',
                array(
                    'numPinyin' => __('numerical pinyin', true),
                    'diacPinyin' => __('diacritical pinyin', true),
                ),
                array(
                    'value' => 'numPinyin',
                    'legend' => ''
                )
            );
            ?>
        </p>
        <?php echo $form->end(__('Convert', true)); ?>
    </div>

</div>
