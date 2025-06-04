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

    public function announcementProvider() {
        return [
            'announcement disabled' => [
                '2020-05-30 02:00 UTC', // "now" time
                [ 'enabled' => false ], // value for Announcement in app_config.php
                false,                  // should be displayed or not
            ],
            'announcement enabled' => [
                '2020-05-30 02:00 UTC',
                [ 'enabled' => true ],
                true,
            ],
            'announcement disabled, hide in 1 minute' => [
                '2020-05-30 01:59 UTC',
                [ 'enabled' => false, 'hideAfter' => '2020-05-30 02:00 UTC' ],
                false,
            ],
            'announcement enabled, hide in 11 minutes' => [
                '2020-05-30 01:49:00 UTC',
                [ 'enabled' => true, 'hideAfter' => '2020-05-30 02:00 UTC' ],
                true,
            ],
            'announcement enabled, hide exactly now' => [
                '2020-05-30 02:00 UTC',
                [ 'enabled' => true, 'hideAfter' => '2020-05-30 02:00 UTC' ],
                false,
            ],
            'announcement enabled, hide 1 second ago' => [
                '2020-05-30 02:00:01 UTC',
                [ 'enabled' => true, 'hideAfter' => '2020-05-30 02:00 UTC' ],
                false,
            ],
            'announcement enabled, hide 1 minute ago' => [
                '2020-05-30 02:01 UTC',
                [ 'enabled' => true, 'hideAfter' => '2020-05-30 02:00 UTC' ],
                false,
            ],
            'announcement enabled, hide 10 years ago' => [
                '2030-05-30 02:00 UTC',
                [ 'enabled' => true, 'hideAfter' => '2020-05-30 02:00 UTC' ],
                false,
            ],
            'announcement enabled, hideAfter empty' => [
                '2020-05-30 02:00 UTC',
                [ 'enabled' => true, 'hideAfter' => '' ],
                true,
            ],
            'announcement enabled, hideAfter invalid' => [
                '2020-05-30 02:00 UTC',
                [ 'enabled' => true, 'hideAfter' => 'foobar' ],
                true,
            ],
        ];
    }

    /**
     * @dataProvider announcementProvider()
     */
    public function testAnnouncement($now, $config, $shouldShow)
    {
        Time::setTestNow(new Time($now));
        $helper = $this->createHelperWithConfig($config);

        $this->assertEquals($shouldShow, $helper->isDisplayed());

        Time::setTestNow();
    }

    public function maintenanceProvider() {
        return [
            'maintenance in 11 minutes' => [
                '2020-05-30 01:49:00 UTC',  // "now" time
                [ 'maintenance' => [ 'start' => '2020-05-30 02:00 UTC' ] ], // value for Announcement in app_config.php
                'in 11 minutes', // confirm this text is present in the maintenance message
                false,           // confirm whether of not maintenance is considered "imminent"
            ],
            'maintenance in 10 minutes 59 seconds' => [
                '2020-05-30 01:49:01 UTC',
                [ 'maintenance' => [ 'start' => '2020-05-30 02:00 UTC' ] ],
                'in 10 minutes',
                true,
            ],
            'maintenance in 10 minutes' => [
                '2020-05-30 01:50 UTC',
                [ 'maintenance' => [ 'start' => '2020-05-30 02:00 UTC' ] ],
                'in 10 minutes',
                true,
            ],
            'maintenance in 1 minute' => [
                '2020-05-30 01:59 UTC',
                [ 'maintenance' => [ 'start' => '2020-05-30 02:00 UTC' ] ],
                'in 1 minute',
                true,
            ],
            'maintenance in 59 seconds' => [
                '2020-05-30 01:59:01 UTC',
                [ 'maintenance' => [ 'start' => '2020-05-30 02:00 UTC' ] ],
                'in 59 seconds',
                true,
            ],
            'maintenance in 1 second' => [
                '2020-05-30 01:59:59 UTC',
                [ 'maintenance' => [ 'start' => '2020-05-30 02:00 UTC' ] ],
                'in 1 second',
                true,
            ],
            'maintenance starts exactly now' => [
                '2020-05-30 02:00 UTC',
                [ 'enabled' => true, 'maintenance' => [ 'start' => '2020-05-30 02:00 UTC' ] ],
                '',
                true,
            ],
            'maintenance started 1 second ago' => [
                '2020-05-30 02:00:01 UTC',
                [ 'enabled' => true, 'maintenance' => [ 'start' => '2020-05-30 02:00 UTC' ] ],
                '',
                true,
            ],
        ];
    }

    /**
     * @dataProvider maintenanceProvider()
     */
    public function testMaintenance($now, $config, $expectedMessage, $expectedIsImminent)
    {
        Time::setTestNow(new Time($now));
        $helper = $this->createHelperWithConfig($config);

        if ($expectedMessage === '') {
            $this->assertEquals($expectedMessage, $helper->getMaintenanceMessage());
        } else {
            $this->assertContains($expectedMessage, $helper->getMaintenanceMessage());
        }
        $this->assertEquals($expectedIsImminent, $helper->isMaintenanceImminent());

        Time::setTestNow();
    }
}
