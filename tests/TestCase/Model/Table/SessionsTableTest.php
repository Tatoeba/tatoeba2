<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\CurrentUser;
use App\Model\Table\SessionsTable;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class SessionsTableTest extends TestCase
{
    public $Sessions;

    public $fixtures = [
        'app.Sessions',
        'app.UsersLanguages',
    ];

    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Sessions') ? [] : ['className' => SessionsTable::class];
        $this->Sessions = TableRegistry::getTableLocator()->get('Sessions', $config);

        CurrentUser::store(null);
    }

    public function tearDown()
    {
        unset($this->Sessions);

        parent::tearDown();
    }

    public function testSave_ok()
    {
        $session = new Entity(['id' => 'sessionidfoobar', 'data' => 'somedatahere']);
        $ok = (bool)$this->Sessions->save($session);
        $this->assertTrue($ok);
    }

    public function testSave_savesNullUserId()
    {
        $id = 'sessionidfoobar';
        $session = new Entity(['id' => $id, 'data' => 'somedatahere']);
        $this->Sessions->save($session);

        $session = $this->Sessions->findById($id)->first();
        $this->assertNull($session->user_id);
    }

    public function testSave_savesUserId()
    {
        CurrentUser::store(['id' => 23]);
        $id = 'sessionidfoobar';
        $session = new Entity(['id' => $id, 'data' => 'somedatahere']);

        $this->Sessions->save($session);

        $session = $this->Sessions->findById($id)->first();
        $this->assertEquals(23, $session->user_id);
    }

    public function testSave_doesNotSaveUserIdWhenDataIsUnmodified()
    {
        CurrentUser::store(['id' => 23]);
        $id = 'sessionidfoobar';
        $session = new Entity(['id' => $id]);

        $this->Sessions->save($session);

        $session = $this->Sessions->findById($id)->first();
        $this->assertNull($session->user_id);
    }
}
