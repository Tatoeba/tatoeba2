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

$this->set('title_for_layout', $this->Pages->formatTitle(__('Edit Comment')));


?>

<div id="annexe_content">
    
    
    <!-- <div class="module">
    </div> -->
</div>

<div id="main_content">
    

<div class="module">
    <?php
    echo '<h2>';
    echo format(
        __('Edit Comment on Sentence #{number}'),
        array('number' => $sentenceComment['SentenceComment']['sentence_id'])
    );
    echo '</h2>';
    ?>
    
    <div id="sentence<?php 
        echo $sentenceComment['SentenceComment']['sentence_id'];
        ?>" 
        class="sentenceSingle">
    <?php
    $this->Comments->displaySentence($sentenceComment);
    ?>
    </div>
    
    <?php
    $this->Comments->displayCommentEditForm(
        $sentenceComment['SentenceComment'],
        $sentenceComment['User']
    );
    ?>
</div>
    
</div>
