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
 * @author   HO Ngoc Phuong Trang <trang@tatoeba.org>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
?>
 
<div class="module">
    <h2><?php echo __d('admin', 'Correctness') ?></h2>
    <?php
    echo $this->Form->create(
        "Sentence",
        array(
            "action" => "edit_correctness",
            "type" => "post",
        )
    );
    echo $this->Form->input(
        "id",
        array("value" => $sentenceId)
    );
    echo $this->Form->input(
        "correctness", 
        array(
            "legend" => false,
            "type" => "radio",
            "options" => array(
                Sentence::MIN_CORRECTNESS => "-1", 
                Sentence::MAX_CORRECTNESS => "0"
            ),
            "value" => $sentenceCorrectness
        )
    );
    echo $this->Form->end(__d('admin', 'Submit'));
    ?>
</div>
