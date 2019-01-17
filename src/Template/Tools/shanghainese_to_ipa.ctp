
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
    __('Convert Shanghainese into IPA')
));

// if it's the first time we call this tool
if (!isset($lastText)) {
    $lastText = '';
}

?>
<div id="annexe_content">
    <div class="module">
        <h2><?php echo __('Credits'); ?></h2>
        <p>
            <?php
            echo format(
                __(
                    "We really want to thank <a href='{0}'>Kellen Parker</a> ".
                    "who has provided us with much more complete data files. If ".
                    "you're interested in his work, you can check his project ".
                    "page <a href='{1}'>here</a>. Without him, the Shanghainese ".
                    "sentences wouldn't have such a complete IPA.",
                    true
                ),
                'http://www.sinoglot.com/wu',
                'http://www.sinoglot.com/wu/tools/data'
            );
            ?>
        </p>
    </div>
</div>



<div id="main_content">
    <div class="module">
        <h2><?php echo __('Convert Shanghainese into IPA'); ?></h2>

        <?php
        if (isset($convertedText)) {
            echo $this->Languages->tagWithLang(
                'div', 'wuu', $convertedText,
                array('id' => 'conversion'),
                'Latn'
            );
        }

        echo $this->Form->create(
            'Tool',
            array(
                "url" => array("action" => "shanghainese_to_ipa"),
                "type" => "post"
            )
        );
        ?>
        <p>
        <?php
        echo $this->Form->label(
            'query',
            __('Enter a text in shanghainese dialect')
        );
        echo $this->Form->textarea(
            'query',
            array(
                "value" => $lastText,
                "rows" => 30,
                "cols"=> 40,
                "lang"=> "wuu",
                "dir"=> "ltr",
            )
        );
        ?>
        </p>
        <p>
        </p>
        <?php echo $this->Form->submit(__('Convert')); ?>
        <?php echo $this->Form->end(); ?>
    </div>

</div>
