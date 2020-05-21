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
            'announcement enabled, 11 minutes to go' => [
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
     * @dataProvider contextProvider()
     */
    public function testEverything($now, $config, $shouldShow)
    {
        Time::setTestNow(new Time($now));
        $helper = $this->createHelperWithConfig($config);

        $this->assertEquals($shouldShow, $helper->isDisplayed());
    }
}
