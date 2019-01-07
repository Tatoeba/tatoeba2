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
}
