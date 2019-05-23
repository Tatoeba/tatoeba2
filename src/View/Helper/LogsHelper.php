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

use App\View\Helper\AppHelper;


/**
 * Helper for contribution logs.
 *
 * @category Contributions
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class LogsHelper extends AppHelper
{

    public $helpers = array('Date', 'Html', 'Languages');

    private function _findLatestContributionDates($contributions) {
        $latestContribs = array();
        foreach ($contributions as $contribution) {
            $sentenceId = $contribution->sentence_id;
            if ($contribution->datetime) {
                $contribTime = (int)$contribution->datetime->toUnixString();
            } else {
                $contribTime = 0;
            }
            if (!isset($latestContribs[$sentenceId]) ||
                (isset($latestContribs[$sentenceId]) && $latestContribs[$sentenceId] < $contribTime)) {
                $latestContribs[$sentenceId] = $contribTime;
            }
        }
        return $latestContribs;
    }

    /**
     * Mark contributions that are not the latest contributions of a given set
     * as obsolete, and the others as non obsolete.
     *
     * @param array $contributions Set of contributions which is
     *                             to be displayed together.
     * @return void
     */
    public function obsoletize(&$contributions) {
        $latestContribs = $this->_findLatestContributionDates($contributions);
        foreach ($contributions as $contribution) {
            $sentenceId = $contribution->sentence_id;
            if ($contribution->datetime) {
                $contribTime = (int)$contribution->datetime->toUnixString();
            } else {
                $contribTime = 0;
            }
            $contribution->obsolete = ($contribTime < $latestContribs[$sentenceId]);
        }
    }


    /**
     * Create the html link to the profile of a given user
     *
     * @param string $userName The user name
     *
     * @return string The html link.
     */
    private function _linkToUserProfile($username)
    {
        return $this->Html->link(
            $username,
            array(
                "controller" => "user",
                "action" => "profile",
                $username
            )
        );
    }


    /**
     * @param $type
     * @param $action
     * @param $username
     * @param $date
     *
     * @return string
     */
    public function getInfoLabel($type, $action, $username = null, $date = null) {
        $date = $date ? $date->format('Y-m-d H:i:s') : null;
        $userProfileLink = $username ? $this->_linkToUserProfile($username) : null;
        $dateLabel = $this->Date->ago($date);

        switch ($action) {
            case 'insert' :
                if ($type == 'sentence') {
                    $label = $username ? __('added by {user}') : __('added by a former member');
                } else if ($type == 'link') {
                    $label = $username ? __('linked by {user}') : __('linked by a former member');
                } else if ($type == 'license') {
                    $label = $username ? __('license chosen by {user}') : __('license chosen by a former member');
                }
                break;
            case 'update' :
                if ($type == 'sentence') {
                    $label = $username ? __('edited by {user}') : __('edited by a former member');
                } else if ($type == 'license') {
                    $label = $username ? __('license changed by {user}') : __('license changed by a former member');
                }
                break;
            case 'delete' :
                if ($type == 'sentence') {
                    $label = $username ? __('deleted by {user}') : __('deleted by a former member');
                } else if ($type == 'link') {
                    $label = $username ? __('unlinked by {user}') : __('unlinked by a former member');
                }
                break;
            default:
                $status = null;
                break;
        }

        $formatLabel = format($label, array('user' => $userProfileLink));

        return format(__('{info}, {date}'), array(
            'info' => $formatLabel,
            'date' => $dateLabel
        ));
    }
}
?>
