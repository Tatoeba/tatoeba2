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

/**
 * Activity timeline for contributions. It displays for each day the number of new
 * sentences.
 *
 * @category Contributions
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

$this->pageTitle = "Tatoeba - " . __("Activity timeline", true); 
 
$maxWidth = 600;
$maxTotal = 0;

foreach ($stats as $stat) {
    if ($stat[0]['total'] > $maxTotal) {
        $maxTotal = $stat[0]['total'];
    }
}

echo '<table id="timeline">';
foreach ($stats as $stat) {
    $total = $stat[0]['total'];
    $percent = $total / $maxTotal;
    $width = intval($percent * $maxWidth);
    
    if ($total > 200) {
        $color = 10;
    } else {
        $color = intval($total/20);
    }
    
    echo '<tr>';
        echo '<td class="date">';
        echo $stat[0]['day'];
        echo '</td>';
        
        echo '<td class="number color'.$color.'">';
        echo '<strong>'.$total.'</strong>';
        echo '</td>';
        
        echo '<td class="line">';
        echo '<div class="logs_stats color'.$color.'" style="width:'.$width.'px">';
        echo '</div>';
        echo '</td>';
    echo '</tr>';
}
echo '</table>';
?>
