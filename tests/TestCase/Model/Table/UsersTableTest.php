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
}
