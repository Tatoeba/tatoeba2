<?php
namespace App\Test\TestCase\View\Helper;

use App\View\Helper\AnnouncementHelper;
use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\TestSuite\TestCase;
use Cake\View\View;

class AnnouncementHelperTest extends TestCase
{
    private function createHelperWithConfig($config)
    {
        Configure::write('Announcement', $config);
        $view = new View();
        return new AnnouncementHelper($view);
    }

    public function contextProvider() {
        return [
            'announcement disabled' => [
                '2020-05-30 02:00 UTC', // "now" time
                [ 'enabled' => false ], // value for Announcement in app_config.php
                false,                  // should be displayed or not
                null,                   // maintenance message
            ],
            'announcement enabled' => [
                '2020-05-30 02:00 UTC',
                [ 'enabled' => true ],
                true,
                null,                   // no maintenance message since no maintenance is scheduled
            ],
            'announcement enabled, 10 minutes 59 seconds to go, with warning' => [
                '2020-05-30 01:49:01 UTC',
                [ 'enabled' => true, 'maintenance' => ['start' => '2020-05-30 02:00 UTC'] ],
                true,
                'Tatoeba will temporarily shut down for maintenance in 10 minutes.',
            ],
            'announcement enabled, 1 minute to go, with warning' => [
                '2020-05-30 01:59 UTC',
                [ 'enabled' => true, 'maintenance' => ['start' => '2020-05-30 02:00 UTC'] ],
                true,
                'Tatoeba will temporarily shut down for maintenance in 1 minute.',
            ],
        ];
    }

    /**
     * @dataProvider contextProvider()
     */
    public function testEverything($now, $config, $shouldShow, $warning)
    {
        Time::setTestNow(new Time($now));
        $helper = $this->createHelperWithConfig($config);
    
        $this->assertEquals($shouldShow, $helper->isDisplayed());
        $maintenanceMessage = $helper->getMaintenanceMessage();
    
        if (is_null($warning)) {
            $this->assertEmpty($maintenanceMessage);
        } elseif (is_string($maintenanceMessage)) {
            $this->assertContains($warning, $maintenanceMessage);
        } else {
            $this->fail('getMaintenanceMessage returned an unexpected value: ' . var_export($maintenanceMessage, true));
        }
    
        Time::setTestNow();
    }
}
