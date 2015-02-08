
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

$this->set('title_for_layout', $pages->formatTitle(
    __('Convert Shanghainese into IPA', true)
));

// if it's the first time we call this tool
if (!isset($lastText)) {
    $lastText = '';
}

?>
<div id="annexe_content">
    <?php
    $attentionPlease->tatoebaNeedsYou();
    ?>

    <div class="module">
        <h2><?php __('Credits'); ?></h2>
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


    <div class="module">
        <h2><?php __("Note"); ?></h2>
        <p>
            <?php
            __(
                "This tool is in a very early stage, and we're looking for ".
                "people who can help us improve the transcription ".
                "by providing IPA of non-converted characters or reporting ".
                "mistakes."
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
        <h2><?php __('Convert Shanghainese into IPA'); ?></h2>

        <?php
        if (isset($convertedText)) {
            echo $languages->tagWithLang(
                'div', 'wuu-Latn', $convertedText,
                array('id' => 'conversion')
            );
        }

        echo $form->create(
            'Tool',
            array(
                "action" => "shanghainese_to_ipa",
                "type" => "post"
            )
        );
        ?>
        <p>
        <?php
        echo $form->label(
            'query',
            __('Enter a text in shanghainese dialect', true)
        );
        echo $form->textarea(
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
        <?php echo $form->end(__('Convert', true)); ?>
    </div>

</div>

