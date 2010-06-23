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

$stats = ClassRegistry::init('Sentence')->getStatistics();
if (isset($this->params['lang'])) {
    Configure::write('Config.language', $this->params['lang']);
}

//TODO hack to be moved in a helper
function generateSentenceStatLine($stat, $html, $languages)
{
    $langCode = $stat['langStats']['lang'];
    $flagImage = $html->image(
        'flags/'.$langCode .'.png',
        array(
            'width' => 30,
            'height' => 20
        )
    );
    
    $linkToAllSentences = $html->link(
        $flagImage,
        array(
            "controller" => "sentences",
            "action" => "show_all_in",
            $langCode,
            'und',
            'none',
        ),
        array(),
        null,
        false
    );


    $numberOfSentences = $stat['langStats']['numberOfSentences'];
    
    echo '<li class="stat" title="'.$languages->codeToName($langCode).'">';
        echo $linkToAllSentences;
        echo '<span class="langCode">'.$langCode.' : </span>';
        echo '<span class="total">'.$numberOfSentences.'</span>';
    echo '</li>';
}


?>
<div id="sentencesStats">
    <ul>
        <?php
        for ($i = 0 ; $i < 5 ; $i++) {
            $stat = $stats[$i];
            generateSentenceStatLine($stat, $html, $languages);
        }
        ?>
    </ul>

    <?php /*TODO HACK SPOTTED  CSS in the code !*/ ?>
    <ul class="minorityLanguages" style="display:none">
        <?php
        $size = count($stats);
        for ($i = 5; $i < $size; $i++) {
            $stat = $stats[$i];
            generateSentenceStatLine($stat, $html, $languages);
        }
        ?>
    </ul>

    <a class="statsDisplay showStats">[+] <?php __('show all'); ?></a>
    <?php /*TODO HACK SPOTTED  CSS in the code !*/ ?>
    <a class="statsDisplay hideStats" style="display:none">
        [-] <?php __('top 5 only'); ?>
    </a>
</div>
