<?php
namespace App\View\Helper;

use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\View\Helper;

class AnnouncementHelper extends Helper
{
    protected $_defaultConfig = [
        'enabled' => false,
        'shutdownWarning' => false,
    ];

    public function initialize(array $config) {
        $this->setConfig(Configure::read('Announcement'));
    }

    private function getHideAfterAsTime() {
        $hideAfter = $this->getConfig('hideAfter');
        if (strlen($hideAfter)) {
            try {
                return new Time($hideAfter);
            } catch (\Exception $e) {
            }
        }
        return null;
    }

    public function isDisplayed() {
        if ($this->getConfig('enabled')) {
            $hideAfter = $this->getHideAfterAsTime();
            if (is_null($hideAfter)) {
                return true;
            } else {
                return $hideAfter->isFuture();
            }
        } else {
            return false;
        }
    }

    public function shutdownWarning() {
        if ($this->isDisplayed() && $this->getConfig('shutdownWarning')) {
            $hideAfter = $this->getHideAfterAsTime();
            if (is_null($hideAfter)) {
                return null;
            }

            $now = new Time();
            $secs = $now->diffInSeconds($hideAfter, false);
            $mins = (int)($secs/60);
            if ($secs > 0 && $mins <= 10) {
                if ($mins >= 1) {
                    return format(
                        __n(
                            'Tatoeba will temporarily shut down for maintenance in {n} minute.',
                            'Tatoeba will temporarily shut down for maintenance in {n} minutes.',
                            $mins
                        ),
                        ['n' => $mins]
                    );
                } else {
                    return format(
                        __n(
                            'Tatoeba will temporarily shut down for maintenance in {n} second.',
                            'Tatoeba will temporarily shut down for maintenance in {n} seconds.',
                            $secs
                        ),
                        ['n' => $secs]
                    );
                }
            }
        }
        return null;
    }
}
