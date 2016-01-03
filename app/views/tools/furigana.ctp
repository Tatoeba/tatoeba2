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

$this->set('title_for_layout', $pages->formatTitle(
    __('Autogenerate furigana over Japanese', true)
));

?>

<div id="annexe_content">
    <div class="module">
        <h2><?php __('Source code'); ?></h2>
        <p>
        <?php
        echo $html->link(
            'GitHub/nihongoparserd',
            'https://github.com/Tatoeba/nihongoparserd'
        );
        ?>
        </p>
    </div>
</div>

<div id="main_content">
    <div class="module">
        <h2><?php __('Autogenerate furigana over Japanese'); ?></h2>
        <?php

        if ($result) {
            echo $languages->tagWithLang(
                'div', 'ja', $transcriptions->transcriptionAsHTML('jpn', $result),
                array('id' => 'conversion', 'escape' => false)
            );
        }

        echo $form->create(
            'Tool',
            array(
                "action" => "furigana",
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
                "cols" => 40,
                "lang" => "ja",
                "dir" => "ltr",
            )
        );
        ?>
        </p>
        <?php echo $form->end(__('Autogenerate furigana', true)); ?>
    </div>
</div>
