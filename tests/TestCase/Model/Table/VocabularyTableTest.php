<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\VocabularyTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Core\Configure;
use App\Model\CurrentUser;

class VocabularyTableTest extends TestCase
{
    public $fixtures = [
        'app.vocabulary',
        'app.users_vocabulary',
        'app.sentences',
        'app.users_languages'
    ];

    public function setUp()
    {
        parent::setUp();
        Configure::write('Acl.database', 'test');
        $this->Vocabulary = TableRegistry::getTableLocator()->get('Vocabulary');
    }

    public function tearDown()
    {
        unset($this->Vocabulary);

        parent::tearDown();
    }

    public function testAddItem_withExistingVocabulary()
    {
        CurrentUser::store(['id' => 1]);
        $result = $this->Vocabulary->addItem('eng', 'out of the blue');
        $this->assertEquals(1, $result->id);
        $this->assertTrue($result->duplicate);
    }

    public function testAddItem_withNewVocabulary()
    {
        CurrentUser::store(['id' => 7]);
        $result = $this->Vocabulary->addItem('eng', 'hashtag');
        $this->assertEquals(2, $result->id);
    }

    public function testIncrementNumSentences_succeeds()
    {
        $result = $this->Vocabulary->incrementNumSentences(1, 'This happened out of the blue.');
        $this->assertEquals(2, $result);
    }

    public function testIncrementNumSentences_fails()
    {
        $result = $this->Vocabulary->incrementNumSentences(1, 'This is just blue.');
        $this->assertEquals(1, $result);
    }
}