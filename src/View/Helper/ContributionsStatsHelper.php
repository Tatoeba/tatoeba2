<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2020
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
 *
 * @category PHP
 * @package  Tatoeba
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
namespace App\View\Helper;

use App\View\Helper\AppHelper;

/**
 * Helper for contributions stats.
 *
 * @category ContributionsStats
 * @package  Helpers
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class ContributionsStatsHelper extends AppHelper
{
    public $helpers = array('Html');

    /**
     * Return the HTML to display the stats bar corresponding to $category
     *
     * @param $stat             Stats array containing number of sentences for each category
     * @param string $category  Currently one of 'added' 'linked' 'unlinked' 'deleted'
     * @param int $maxTotal     The highest number of contributions in a day of the month
     *
     * @return string
     */
    public function statBar($stat, $category, $maxTotal)
    {
        if(isset($stat[$category]) && $stat[$category] > 0){
            $width = ($stat[$category] / $maxTotal) * 100;
            $stringWidth = mb_strwidth($stat[$category]) + 1; // + 1 for the sign
            $sign = '+';
            if ($category == "unlinked" || $category == "deleted")
                $sign = '-';
            return $this->Html->div("logs_stats ${category}", $sign . $stat[$category],
                array('style' => "width: ${width}%; min-width: calc(1em * ${stringWidth});")
            );
        }
    }
}
?>
