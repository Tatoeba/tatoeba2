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
namespace App\View\Helper;

use App\View\Helper\AppHelper;
use Cake\I18n\Time;
use DateTimeInterface;

/**
 * Helper to display date.
 *
 * @category Default
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class DateHelper extends AppHelper
{
    private $_months;

    public $helpers = array('Time');

    /**
     * Wrap CakePHP nice() function/method
     *
     * @param $date DateTime|string Datetime to format
     *
     * @return string
     */
    public function nice($date)
    {

        if (empty($date) || $date == '0000-00-00 00:00:00') {
            return __('date unknown');
        } elseif ($date instanceof DateTimeInterface) {
            return $date->nice();
        } else {
            return $this->Time->nice($date);
        }
    }

    /**
     * Create the date label used for comments, wall messages, ...
     *
     * @param string          $text     Text for the label. It must contain both
     *                                  "{createdDate}" and "{modifiedDate}" placeholders.
     * @param DateTime|string $created  Creation datetime (in MySQL format if string)
     * @param DateTime|string $modified Modification datetime (in MySQL format if string)
     * @param boolean         $tooltip  When the label is used for a tooltip the dates will
     *                                  always be exact.
     *
     * @return string
     */
    public function getDateLabel($text, $created, $modified, $tooltip=false)
    {
        if (empty($modified) || $created == $modified) {
            if ($tooltip) {
                return $this->nice($created);
            } else {
                return $this->ago($created);
            }
        } else {
            if ($tooltip) {
                return format($text,
                              array('createdDate' => $this->nice($created),
                                    'modifiedDate' => $this->nice($modified)));
            } else {
                return format($text,
                              array('createdDate' => $this->ago($created),
                                    'modifiedDate' => $this->ago($modified, false)));
            }
        }
    }

    /**
     * Display how long ago compared to now.
     *
     * @param DateTime|string $date  If the date is given as a string, the format should be
     *                               'Y-m-d H:i:s' (MySQL format).
     * @param boolean         $alone Indicates whether the date is shown alone or in a phrase
     *
     * @return string
     */
    public function ago($date, $alone=true)
    {
        if (!($date instanceof DateTimeInterface)) {
            if (empty($date) || $date == '0000-00-00 00:00:00') {
                return __('date unknown');
            } else {
                $dateObj = Time::parseDateTime($date);
                if (!$dateObj) {
                    return __('date unknown');
                }
            }
        } else {
            $dateObj = $date;
        }

        $diff = Time::fromNow($dateObj);

        if ($diff->days > 30) {
            $formattedDate = $dateObj->i18nFormat([\IntlDateFormatter::LONG, \IntlDateFormatter::NONE]);

            if ($alone) {
                return $formattedDate;
            } else {
                return format(
                    /* @translators: This date appears in a phrase (e.g. "edited April 1, 2012"), so you
                       may want to add a preposition or an article. */
                    __('{date}'),
                    array('date' => $formattedDate)
                );
            }
        } elseif ($diff->days > 0) {
            return format(__n('yesterday', "{n}\u{00A0}days ago", $diff->days), array('n' => $diff->days));
        } elseif ($diff->h > 0) {
            return format(__n('an hour ago', "{n}\u{00A0}hours ago", $diff->h), array('n' => $diff->h));
        } else {
            // we stop at minute accuracy
            $minutes = max($diff->i, 1);
            return format(__n('a minute ago', "{n}\u{00A0}minutes ago", $minutes), array('n' => $minutes));
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
     * Get the name for a given month number
     *
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
     * @param  string $dateTime   mysql date|datetime format
     * @param  string $dateFormat ICU date format string (see
     *                            https://www.php.net/manual/en/class.intldateformatter.php)
     *
     * @return string             formatted date string
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
            return Time::parseDateTime($dateTime)->i18nFormat($dateFormat);
        }

        return $this->_formatIncompleteDate($dateArray);
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
     * Format an incomplete date
     *
     * An incomplete date is either just a year, a month and a year
     * or a day and a month.
     *
     * @param  array $dateArray  Three-element array containing the year, month,
     *                           day (in that order)
     *
     * @return string
     */
    private function _formatIncompleteDate($dateArray)
    {
        list($year, $month, $day) = $dateArray;

        if ($year == '0000') {
            return format(__x('incomplete date', '{month} {day}'),
                          (array ('day' => $day, 'month' => $this->monthName($month))));
        } else {
            if ($month == '00') {
                return $year;
            } else {
                return format(__x('incomplete date', '{month} {year}'),
                              (array ('month' => $this->monthName($month), 'year' => $year)));
            }
        }
    }
}
?>
