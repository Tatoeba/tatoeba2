<?php
namespace App\View\Helper;

use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\View\Helper;

class AnnouncementHelper extends Helper
{
    protected $_defaultConfig = [
        'enabled' => false,
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
}
