<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UsersVocabularyTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class UsersVocabularyTableTest extends TestCase
{
    public $fixtures = [
        'app.users_vocabulary',
        'app.vocabulary',
        'app.users'
    ];

    public function setUp()
    {
        parent::setUp();
        $this->UsersVocabulary = TableRegistry::getTableLocator()->get('UsersVocabulary');
    }

    public function tearDown()
    {
        unset($this->UsersVocabulary);

        parent::tearDown();
    }

    public function testFindFirst_returnsSomething()
    {
        $result = $this->UsersVocabulary->findFirst(1, 1);
        $this->assertEquals(1, $result->id);
    }

    public function testFindFirst_returnsNothing()
    {
        $result = $this->UsersVocabulary->findFirst(1, 7);
        $this->assertEmpty($result);
    }

    public function testAdd()
    {
        $result = $this->UsersVocabulary->add(1, 2);
        $expected = [
            'id' => 2,
            'vocabulary_id' => 1,
            'user_id' => 2
        ];
        $this->assertEquals($expected, $result->toArray());
    }
}
