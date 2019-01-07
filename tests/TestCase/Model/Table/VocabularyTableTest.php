<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\VocabularyTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class VocabularyTableTest extends TestCase
{
    public $fixtures = [
        'app.vocabulary',
        'app.users_vocabulary',
        'app.sentences'
    ];

    public function setUp()
    {
        parent::setUp();
        $this->Vocabulary = TableRegistry::getTableLocator()->get('Vocabulary');
    }

    public function tearDown()
    {
        unset($this->Vocabulary);

        parent::tearDown();
    }
}