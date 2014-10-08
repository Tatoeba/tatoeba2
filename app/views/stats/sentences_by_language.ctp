<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
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
 
$this->set('title_for_layout', __('Number of sentences per language', true) . __(' - Tatoeba', true));

$stats = ClassRegistry::init('Language')->getStatistics();
$audioStats = ClassRegistry::init('Sentence')->getTotalNumberOfSentencesWithAudio();
$totalSentences = ClassRegistry::init('Sentence')->getTotalNumberOfSentences();
$max = $stats[0]['Language']['numberOfSentences'];
?>
<div id="annexe_content">
    <?php echo $this->element('audio_stats', array(
        'stats' => $audioStats,
        'cache' => array(
            'time'=> '+6 hours',
            'key'=> Configure::read('Config.language')
        )
    )); ?>
</div>

<div id="main_content">
<div class="module">
    <h2>
    <?php 
    echo sprintf(__('%s sentences', true), $totalSentences);
    ?>
    </h2>
    
    <table id="sentencesStats">        
        <?php 
        $rank = 1;
        foreach ($stats as $stat) { 
        $langCode = $stat['Language']['code'];
        $numberOfSentences = $stat['Language']['numberOfSentences'];
        $percent = ($numberOfSentences / $max) * 100;
        ?>
        <tr>
            <td class="rank">
            <strong><?php echo $rank; $rank++; ?></strong>
            </td>
            
            <td class="icon">
            <?php 
            echo $languages->icon(
                $langCode, array('width' => 30, 'height' => 20)
            );
            ?>
            </td>
            
            <td>
            <?php echo $langCode; ?>
            </td>
            
            <td class="languageName">
            <?php 
            $langName = $languages->codeToName($langCode);
            if (empty($langCode)) {
                $langCode = 'unknown';
            }
            echo $html->link(
                $langName,
                array(
                    "controller" => "sentences",
                    "action" => "show_all_in",
                    $langCode,
                    'none',
                    'none',
                    'indifferent'
                )
            );
            ?>
            </td>
            
            <td class="numberOfSentences">
            <?php echo $numberOfSentences; ?>
            </td>
            
            <td class="chart">
            <div class="bar" style="width: <?php echo $percent;?>%"></div>
            </td>
        </tr>
        <?php 
        } 
        ?>
    </table>
</div>
</div>
