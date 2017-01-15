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

//TODO to factorize with pinyin converter in a tool helper

$this->set('title_for_layout', $this->Pages->formatTitle(
    __('Chinese traditional/simplified conversion')
));

// if it's the first time we call this tool
if (!isset($lastText)) {
    $lastText = '';
}

?>
<div id="annexe_content">
    <div class="module">
        <h2><?php echo __('Source code'); ?></h2>
        <p>
        <?php
        echo $this->Html->link(
            'GitHub/sinoparserd',
            'https://github.com/allan-simon/sinoparserd'
        );
        ?>
        </p>
    </div>
</div>



<div id="main_content">
    <div class="module">
        <h2><?php echo __('Chinese traditional/simplified conversion'); ?></h2>

        <?php
        if (isset($convertedText)) {
            echo $this->Languages->tagWithLang(
                'div', 'zh', $convertedText,
                array('id' => 'conversion'),
                $script
            );
        }

        echo $this->Form->create(
            'Tool',
            array(
                "action" => "conversion_simplified_traditional_chinese",
                "type" => "post"
            )
        );
        ?>
        <p>
        <?php
        echo $this->Form->label(
            'query',
            __('Enter either traditional or simplified Chinese')
        );
        echo $this->Form->textarea(
            'query',
            array(
                "value" => $lastText,
                "rows" => 30,
                "cols" => 40,
                "lang" => "zh",
                "dir" => "ltr",
            )
        );
        ?>
        </p>
        <p>
        </p>
        <?php echo $this->Form->end(__('Convert')); ?>
    </div>

</div>

