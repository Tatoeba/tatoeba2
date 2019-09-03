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

use App\Model\Table\SentencesTable;
?>

<div class="section md-whiteframe-1dp">
    <h2><?php echo __d('admin', 'Correctness') ?></h2>
    <?php
    echo $this->Form->create(
        "Sentence",
        array(
            "url" => array("action" => "edit_correctness"),
            "type" => "post",
        )
    );
    echo $this->Form->hidden(
        "id",
        array("value" => $sentenceId)
    );
    echo $this->Form->control(
        "correctness",
        array(
            "label" => false,
            "type" => "radio",
            "options" => array(
                SentencesTable::MIN_CORRECTNESS => "-1",
                SentencesTable::MAX_CORRECTNESS => "0"
            ),
            "value" => $sentenceCorrectness
        )
    );
    echo $this->Form->submit(__d('admin', 'Submit'));
    echo $this->Form->end();
    ?>
</div>
