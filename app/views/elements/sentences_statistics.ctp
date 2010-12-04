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
$stats = ClassRegistry::init('Sentence')->getStatistics(5);
$numSentences = ClassRegistry::init('Sentence')->getTotalNumberOfSentences();
?>

<div class="module">
    <h2>
    <?php 
    echo sprintf(__('%s sentences', true), $numSentences);
    ?>
    </h2>
    
    <div id="sentencesStats">
        <ul>
            <?php
            foreach ($stats as $stat) {
                $langCode = $stat['langStats']['lang'];
                $numberOfSentences = $stat['langStats']['numberOfSentences'];
                $languages->stat($langCode, $numberOfSentences);
            }
            ?>
            
            <li>
            <?php 
            echo $html->link(
                __('show all languages', true),
                array(
                    'controller' => 'stats',
                    'action' => 'sentences_by_language'
                )
            );
            ?>
            </li>
        </ul>    
    </div>
</div>