<?php
namespace App\View\Helper;

use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\View\Helper;

class AnnouncementHelper extends Helper
{
    protected $_defaultConfig = [
        'enabled' => false,
        'maintenance' => false,
    ];

    public function initialize(array $config) {
        $this->setConfig(Configure::read('Announcement'));
    }

    private function getConfigAsTime(string $configKey) {
        $time = $this->getConfig($configKey);
        if (strlen($time)) {
            try {
                return new Time($time);
            } catch (\Exception $e) {
            }
        }
        return null;
    }

    public function isDisplayed() {
        if ($this->getConfig('enabled')) {
            $hideAfter = $this->getConfigAsTime('hideAfter');
            if (is_null($hideAfter)) {
                return true;
            } else {
                return $hideAfter->isFuture();
            }
        } else {
            return false;
        }
    }

    public function getMaintenanceMessage() {
        $messages = [];
        $start = $this->getConfigAsTime('maintenance.start');
        if ($start && $start->isFuture()) {
            $now = new Time();
            $time = $start->i18nFormat([\IntlDateFormatter::NONE, \IntlDateFormatter::SHORT]);
            $datetime = $start->i18nFormat([\IntlDateFormatter::LONG, \IntlDateFormatter::SHORT]);
            $secsToGo = $now->diffInSeconds($start, false);
            $minsToGo = $now->diffInMinutes($start, false);
            $hoursToGo = $now->diffInHours($start, false);
            $daysToGo = $now->diffInDays($start, false);
            if ($secsToGo < 60) {
                $messages[] = format(
                    __n(
                        'Tatoeba will temporarily shut down for maintenance in {n} second.',
                        'Tatoeba will temporarily shut down for maintenance in {n} seconds.',
                        $secsToGo
                    ),
                    ['n' => $secsToGo]
                );
            } elseif ($minsToGo < 60) {
                $messages[] = format(
                    __n(
                        'Tatoeba will temporarily shut down for maintenance in {n} minute.',
                        'Tatoeba will temporarily shut down for maintenance in {n} minutes.',
                        $minsToGo
                    ),
                    ['n' => $minsToGo]
                );
            } elseif ($hoursToGo < 24) {
                $messages[] = format(
                    __n(
                        'Tatoeba will temporarily shut down for maintenance at {time} UTC,'
                        .' which is in {n} hour.',
                        'Tatoeba will temporarily shut down for maintenance at {time} UTC,'
                        .' which is in {n} hours.',
                        $hoursToGo
                    ),
                    ['time' => $time, 'n' => $hoursToGo]
                );
            } else {
                $messages[] = format(
                    __n(
                        'Tatoeba will temporarily shut down for maintenance on {datetime} UTC,'
                        .' which is in {n} day.',
                        'Tatoeba will temporarily shut down for maintenance on {datetime} UTC,'
                        .' which is in {n} days.',
                        $daysToGo
                    ),
                    ['datetime' => $datetime, 'n' => $daysToGo]
                );
            }

            if ($end = $this->getConfigAsTime('maintenance.end')) {
                $hoursEstimated = $start->diffInHours($end, false);
                $minsEstimated = $start->diffInMinutes($end, false);
                if ($hoursEstimated > 0) {
                    $messages[] = format(
                        __n(
                            'The maintenance is estimated to take no longer than {n} hour.',
                            'The maintenance is estimated to take no longer than {n} hours.',
                            $hoursEstimated
                        ),
                        ['n' => $hoursEstimated]
                    );
                } elseif ($minsEstimated > 0) {
                    $messages[] = format(
                        __n(
                            'The maintenance is estimated to take no longer than {n} minute.',
                            'The maintenance is estimated to take no longer than {n} minutes.',
                            $minsEstimated
                        ),
                        ['n' => $minsEstimated]
                    );
                }
            }
        }
        return implode(' ', $messages);
    }

    public function getMaintenanceStartsIn() {
        $start = $this->getConfigAsTime('maintenance.start');
        if ($start && $start->isFuture()) {
            $now = new Time();
            return $now->diffInSeconds($start, false);
        }
    }

    public function isMaintenanceImminent() {
        if ($start = $this->getConfigAsTime('maintenance.start')) {
            $now = new Time();
            return $now->diffInMinutes($start, false) <= 10;
        } else {
            return false;
        }
    }
}
