<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\User;
use Cake\TestSuite\TestCase;

class UserTest extends TestCase
{
    public $User;

    public function setUp()
    {
        parent::setUp();
        $this->User = new User();
    }

    public function tearDown()
    {
        unset($this->User);
        parent::tearDown();
    }

    public function testSet_passwordhashesPassword()
    {
        $this->User->set('password', 'my super password');
        $this->assertContains('$', $this->User->password);
    }

    public function testSet_settingsMergesExistingSettings()
    {
        $this->User->set('settings', ['is_public' => true]);
        $this->User->set('settings', ['default_license' => 'CC0 1.0']);

        $this->assertEquals(true, $this->User->settings['is_public']);
    }

    public function testSet_settingsIgnoresUnknownSettings()
    {
        $this->User->set('settings', ['this_does_not_exist' => true]);

        $this->assertFalse(isset($this->User->settings['this_does_not_exist']));
    }

    public function testGet_settingsSetsDefaultSettings()
    {
        $defaultLicense = User::$defaultSettings['default_license'];

        $this->User->set('settings', ['is_public' => true]);

        $this->assertEquals($defaultLicense, $this->User->settings['default_license']);
    }

    public function testGet_settingsValidatesSettings()
    {
        $default = [
            User::$defaultSettings['sentences_per_page'],
            User::$defaultSettings['max_visible_translations']
        ];

        $this->User->set('settings', ['sentences_per_page' => 33]);
        $this->User->set('settings', ['max_visible_translations' => 99]);

        $result = [
            $this->User->settings['sentences_per_page'],
            $this->User->settings['max_visible_translations']
        ];

        $this->assertEquals($default, $result);
    }
}
