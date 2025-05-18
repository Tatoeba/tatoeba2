<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\VocabularyTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use App\Model\CurrentUser;
use Cake\I18n\I18n;

class VocabularyTableTest extends TestCase
{
    use \App\Test\TestCase\SearchMockTrait;

    public $fixtures = [
        'app.vocabulary',
        'app.users_vocabulary',
        'app.sentences',
        'app.users_languages'
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

    public function testAddItem_updatesCurrentNumberOfSentences()
    {
        $this->enableMockedSearch([21, 11, 12], 4);
        CurrentUser::store(['id' => 7]);

        $result = $this->Vocabulary->addItem('eng', 'hashtag');

        $this->assertEquals(4, $result->numSentences);
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

    public function testAddItem_correctDateUsingArabicLocale() {
        $prevLocale = I18n::getLocale();
        I18n::setLocale('ar');

        $added = $this->Vocabulary->addItem('eng', 'test');
        $returned = $this->Vocabulary->get($added->id);
        $this->assertEquals($added->created->format('Y-m-d H:i:s'), $returned->created->format('Y-m-d H:i:s'));

        I18n::setLocale($prevLocale);
    }
}
