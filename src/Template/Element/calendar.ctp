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
 * @link     https://tatoeba.org
 */

/**
 * Some sort of calendar that displays links for each month. 
 * Used in activity timeline.
 *
 * @category Elements
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
 
$years = range(2007, date('Y'));
$months = $this->Date->months();
?>

<div class="section md-whiteframe-1dp month">
    <?php /* @translators: used in the calendar on the Activity timeline page */ ?>
    <h2><?php echo __('Month'); ?></h2>
    <ul>
    <?php
    foreach ($months as $monthNumber => $monthName) {
        if (intval($monthNumber) == intval($currentMonth)) {
            echo '<li class="selected">';
        } else {
            echo '<li>';
        }
        
        echo $this->Html->link(
            $monthName,
            array(
                'controller' => 'contributions',
                'action' => 'activity_timeline',
                $currentYear, $monthNumber
            )
        );
        
        echo '</li>';
    }
    ?>
    </ul>
</div>

<div class="section md-whiteframe-1dp years">
    <?php /* @translators: used in the calendar on the Activity timeline page */ ?>
    <h2><?php echo __('Year'); ?></h2>
    <ul>
    <?php
    foreach ($years as $year) {
        if (intval($year) == intval($currentYear)) {
            echo '<li class="selected">';
        } else {
            echo '<li>';
        }
        
        echo $this->Html->link(
            $year,
            array(
                'controller' => 'contributions',
                'action' => 'activity_timeline',
                $year, $currentMonth
            )
        );
        
        echo '</li>';
    }
    ?>
    </ul>
</div>
