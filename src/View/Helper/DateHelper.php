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
namespace App\View\Helper;


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
    private $_months;

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
    public function ago($date)
    {
        $year = substr($date, 0, 4);
        $month = substr($date, 5, 2);
        $day = substr($date, 8, 2);
        $hour = substr($date, 11, 2);
        $min = substr($date, 14, 2);

        $pureNumberDate = $year.$month.$day.','.$hour.$min;
        $timestamp = strtotime($pureNumberDate);
        
        if (empty($date) || $date == '0000-00-00 00:00:00' || $timestamp == 0) {
            return __('date unknown');
        }

        $now = time();
        $days = intval(($now-$timestamp)/(3600*24));
        $hours = intval(($now-$timestamp) / 3600);
        $minutes = intval(($now-$timestamp) / 60);
        if ($days > 30) {
            // e.g., "2015-06-20 13:12"
            return date("Y-m-d H:i", $timestamp);
        } elseif ($days > 0) {
            return format(__n('yesterday', '{n}&nbsp;days ago', $days), array('n' => $days));
        } elseif ($hours > 0) {
            return format(__n('an hour ago', '{n}&nbsp;hours ago', $hours), array('n' => $hours));
        } else {
            return format(__n('a minute ago', '{n}&nbsp;minutes ago', $minutes), array('n' => $minutes));
        }
    }


    public function months()
    {
        if (!$this->_months) {
            $this->_months = array(
                '01' => __('January'),
                '02' => __('February'),
                '03' => __('March'),
                '04' => __('April'),
                '05' => __('May'),
                '06' => __('June'),
                '07' => __('July'),
                '08' => __('August'),
                '09' => __('September'),
                '10' => __('October'),
                '11' => __('November'),
                '12' => __('December'),
            );
        }

        return $this->_months;
    }

    /**
     * @param string $mm Month number in 2 digit format (ex: '01' for January).
     *
     * @return string
     */
    public function monthName($mm)
    {
        $months = $this->months();

        return $months[$mm];
    }

    /**
     * Format a user birthday. This method accepts incomplete dates.
     *
     * @param  string $dateTime   [mysql date|datetime format]
     * @param  string $dateFormat [supported: 'Y-m-d']
     *
     * @return string             [formatted date string]
     */
    public function formatBirthday($dateTime, $dateFormat)
    {
        $date = explode(' ', $dateTime)[0];

        $dateArray = explode('-', $date);

        // Catch leap year dates with no year given
        if ($date == '1904-02-29') {
            $dateArray[0] = '0000';
        }

        if ($this->_isCompleteDate($dateArray)) {
            return date($dateFormat, strtotime($dateTime));
        }

        $formatMethod = '_formatTo'.str_replace('-', '', $dateFormat);

        return $this->{$formatMethod}($dateArray);
    }

    /**
     * Date has year, month, and day fields set by user.
     *
     * @param  array  $dateArray
     *
     * @return boolean
     */
    private function _isCompleteDate($dateArray)
    {
        foreach ($dateArray as $item) {
            if (!$this->_hasValue($item)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Date field contains a non-zero value.
     *
     * @param  string  $date
     *
     * @return boolean
     */
    private function _hasValue($date)
    {
        if ($date != '00' || $date != '0000') {
            return true;
        }

        return false;
    }

    /**
     * Format date to Y-m-d.
     *
     * @param  array $dateArray
     *
     * @return string
     */
    private function _formatToYmd($dateArray)
    {
        return implode('-', $dateArray);
    }
}
?>
