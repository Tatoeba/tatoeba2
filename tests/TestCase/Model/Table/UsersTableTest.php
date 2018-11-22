<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UsersTable;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class UsersTableTest extends TestCase
{
    public $Users;

    public $fixtures = [
        'app.users',
        'app.groups',
        'app.aros',
        'app.acos',
        'app.aros_acos',
    ];

    public function setUp()
    {
        parent::setUp();

        Configure::write('Acl.database', 'test');

        $config = TableRegistry::getTableLocator()->exists('Users') ? [] : ['className' => UsersTable::class];
        $this->Users = TableRegistry::getTableLocator()->get('Users', $config);
    }

    public function tearDown()
    {
        unset($this->Users);

        parent::tearDown();
    }

    public function testSettingsParsedAsJSON()
    {
        $user = $this->Users->get(7, ['fields' => ['settings']]);

        $this->assertEquals('CC0 1.0', $user->settings['default_license']);
    }

    public function testUpdatePasswordVersion_doesUpdate()
    {
        $password = '123456';
        $userId = 1;
        $oldPassword = $this->Users->get($userId)->password;

        $this->Users->updatePasswordVersion($userId, $password);

        $newPassword = $this->Users->get($userId)->password;
        $this->assertNotEquals($oldPassword, $newPassword);
    }

    public function testUpdatePasswordVersion_doesNotUpdate()
    {
        $password = '123456';
        $userId = 7;
        $oldPassword = $this->Users->get($userId)->password;

        $this->Users->updatePasswordVersion($userId, $password);

        $newPassword = $this->Users->get($userId)->password;
        $this->assertEquals($oldPassword, $newPassword);
    }

    public function testGetSettings()
    {
        $userSettings = $this->Users->getSettings(4);
        $expected = ['send_notifications', 'settings', 'email'];
        $result = array_keys($userSettings->toArray());
        $this->assertEquals($expected, $result);
    }
}
