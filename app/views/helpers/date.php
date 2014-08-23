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
 * Helper to display date.
 *
 * @category Default
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class DateHelper extends AppHelper
{
    /**
     * Display how long ago compared to now.
     *
     * @param string $date        Format for the date is '%d/%m/%Y %H:%M:%S'.
     * @param bool   $isTimestamp Instead of giving a date to the format indicated,
     *                            it is possible to give a timestamp. But then this
     *                            $isTimestamp has to be set to true.
     *
     * @return string
     */
    public function ago($date, $isTimestamp = false)
    {
        if (!$isTimestamp) {
            $year = substr($date, 0, 4);
            $month = substr($date, 5, 2);
            $day = substr($date, 8, 2);
            $hour = substr($date, 11, 2);
            $min = substr($date, 14, 2);

            $pureNumberDate = $year.$month.$day.','.$hour.$min;
            $timestamp = strtotime($pureNumberDate);
        } else {
            $timestamp = $date;
        }
        
        if (empty($date) || $date == '0000-00-00 00:00:00' || $timestamp == 0) {
            return __('date unknown', true);
        }

        $now = time();
        $days = intval(($now-$timestamp)/(3600*24));
        $hours = intval(($now-$timestamp) / 3600);
        $minutes = intval(($now-$timestamp) / 60);
        if ($days > 30) {
            return date("M jS Y", $timestamp).', '.date("H:i", $timestamp);
        } elseif ($days > 0) {
            return sprintf(__('%s day(s) ago', true), $days);
        } elseif ($hours > 0) {
            return sprintf(__('%s hour(s) ago', true), $hours);
        } else {
            return sprintf(__('%s min(s) ago', true), $minutes);
        }
    }
}
?>
