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
                null,                   // shutdown warning message
            ],
            'announcement enabled' => [
                '2020-05-30 02:00 UTC',
                [ 'enabled' => true ],
                true,
                null
            ],
            'announcement disabled, hide in 1 minute' => [
                '2020-05-30 01:59 UTC',
                [ 'enabled' => false, 'hideAfter' => '2020-05-30 02:00 UTC' ],
                false,
                null,
            ],
            'announcement enabled, 11 minutes to go, without warning' => [
                '2020-05-30 01:49:00 UTC',
                [ 'enabled' => true, 'hideAfter' => '2020-05-30 02:00 UTC' ],
                true,
                null,
            ],
            'announcement enabled, 11 minutes to go, with warning' => [
                '2020-05-30 01:49:00 UTC',
                [ 'enabled' => true, 'hideAfter' => '2020-05-30 02:00 UTC', 'shutdownWarning' => true ],
                true,
                null,
            ],
            'announcement enabled, 10 minutes 59 seconds to go, with warning' => [
                '2020-05-30 01:49:01 UTC',
                [ 'enabled' => true, 'hideAfter' => '2020-05-30 02:00 UTC', 'shutdownWarning' => true ],
                true,
                'in 10 minutes',
            ],
            'announcement enabled, 10 minutes to go, with warning' => [
                '2020-05-30 01:50 UTC',
                [ 'enabled' => true, 'hideAfter' => '2020-05-30 02:00 UTC', 'shutdownWarning' => true ],
                true,
                'in 10 minutes',
            ],
            'announcement enabled, 1 minute to go, with warning' => [
                '2020-05-30 01:59 UTC',
                [ 'enabled' => true, 'hideAfter' => '2020-05-30 02:00 UTC', 'shutdownWarning' => true ],
                true,
                'in 1 minute',
            ],
            'announcement enabled, 59 seconds to go, with warning' => [
                '2020-05-30 01:59:01 UTC',
                [ 'enabled' => true, 'hideAfter' => '2020-05-30 02:00 UTC', 'shutdownWarning' => true ],
                true,
                'in 59 seconds',
            ],
            'announcement enabled, 1 second to go, with warning' => [
                '2020-05-30 01:59:59 UTC',
                [ 'enabled' => true, 'hideAfter' => '2020-05-30 02:00 UTC', 'shutdownWarning' => true ],
                true,
                'in 1 second',
            ],
            'announcement enabled, hide exactly now, without warning' => [
                '2020-05-30 02:00 UTC',
                [ 'enabled' => true, 'hideAfter' => '2020-05-30 02:00 UTC' ],
                false,
                null,
            ],
            'announcement enabled, hide exactly now, with warning' => [
                '2020-05-30 02:00 UTC',
                [ 'enabled' => true, 'hideAfter' => '2020-05-30 02:00 UTC' ],
                false,
                null,
            ],
            'announcement enabled, hide 1 second ago' => [
                '2020-05-30 02:00:01 UTC',
                [ 'enabled' => true, 'hideAfter' => '2020-05-30 02:00 UTC' ],
                false,
                null,
            ],
            'announcement enabled, hide 1 minute ago' => [
                '2020-05-30 02:01 UTC',
                [ 'enabled' => true, 'hideAfter' => '2020-05-30 02:00 UTC' ],
                false,
                null,
            ],
            'announcement enabled, hide 10 years ago' => [
                '2030-05-30 02:00 UTC',
                [ 'enabled' => true, 'hideAfter' => '2020-05-30 02:00 UTC' ],
                false,
                null,
            ],
            'announcement enabled, hideAfter empty' => [
                '2020-05-30 02:00 UTC',
                [ 'enabled' => true, 'hideAfter' => '' ],
                true,
                null,
            ],
            'announcement enabled, hideAfter invalid' => [
                '2020-05-30 02:00 UTC',
                [ 'enabled' => true, 'hideAfter' => 'foobar' ],
                true,
                null,
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
        if (is_null($warning)) {
            $this->assertNull($helper->shutdownWarning());
        } else {
            $this->assertContains($warning, $helper->shutdownWarning());
        }

        Time::setTestNow();
    }
}
