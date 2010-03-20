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

//TODO to factorize with pinyin converter in a tool helper

$this->pageTitle = __('Chinese conversion traditional/simplified tool', true);

// if it's the first time we call this tool
if (!isset($lastText)) {
    $lastText = '';
}

?>
<div id="annexe_content">


    <div class="module">
        <h2><?php __('Credits'); ?></h2>
        <p>
            <?php
            echo sprintf(
                __(
                    'This tool is powered by <a href="%s">Adso</a>',
                    true
                ),
                'http://adsotrans.com/downloads/'
            );
            ?>
        </p>
    </div>
    
    <div class="module">
        <h2><?php __('Improvements'); ?></h2>
        <p>
            <?php
            __(
                'The result you will get will not always be perfect. However,'.
                ' you can help us improve this by telling us the mistakes you saw.'.
                ' We will try, if possible, to correct it.'
            );
            ?>
        </p>
        <p class="more_link">
            <?php
            echo $html->link(
                __('Feedback', true),
                array(
                    "controller"=>"pages",
                    "action"=>"contact"
                )
            );
            ?>
        </p>
    </div>
</div>



<div id="main_content">
    <div class="module">
        <h2><?php __('Traditional/simplified chinese converter'); ?></h2>

        <?php
        if (isset($convertedText)) {
            echo '<div id="conversion">';
                echo $convertedText;
            echo '</div>';
        }

        echo $form->create(
            'Tool',
            array(
                "action" => "conversion_simplified_traditional_chinese",
                "type" => "post"
            )
        );
        ?>
        <p>
        <?php
        echo $form->label(
            'query',
            __('Enter either traditional or simplified chinese', true)
        );
        echo $form->textarea(
            'query',
            array(
                "value" => $lastText,
                "rows" => 30,
                "cols"=> 40
            )
        );
        ?>
        </p>
        <p>
        </p>
        <?php echo $form->end(__('Convert', true)); ?>
    </div>

</div>

