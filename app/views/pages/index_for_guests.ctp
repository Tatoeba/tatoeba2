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

echo $this->element('short_description', array(
    'cache' => array(
        'time' => '+1 day',
        'key' => Configure::read('Config.language')
    )
));
?>

<div id="main_content">
    <?php if(!isset($searchProblem)) { ?>
    <div class="module">
        <?php echo $this->element('random_sentence_header'); ?>
        <div class="random_sentences_set">
            <?php
            $sentence = $random['Sentence'];
            $transcrs = $random['Transcription'];
            $translations = $random['Translation'];
            $sentenceOwner = $random['User'];

            $sentences->displaySentencesGroup(
                $sentence,
                $transcrs,
                $translations,
                $sentenceOwner
            );
            ?>
        </div>
    </div>
    <?php } ?>
</div>
