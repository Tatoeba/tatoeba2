<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UsersVocabularyTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\I18n\I18n;

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
        $result = $this->UsersVocabulary->findFirst(1, 4);
        $this->assertEquals(1, $result->id);
    }

    public function testFindFirst_returnsNothing()
    {
        $result = $this->UsersVocabulary->findFirst(1, 7);
        $this->assertEmpty($result);
    }

    public function testAdd()
    {
        $added = $this->UsersVocabulary->add(1, 2);
        $returned = $this->UsersVocabulary->get($added->id);
        $this->assertEquals($added->user_id, $returned->user_id);
        $this->assertEquals($added->vocabulary_id, $returned->vocabulary_id);
    }

    public function testAdd_correctDateUsingArabicLocale() {
        $prevLocale = I18n::getLocale();
        I18n::setLocale('ar');

        $added = $this->UsersVocabulary->add(1, 3);
        $returned = $this->UsersVocabulary->get($added->id);
        $this->assertEquals($added->created->format('Y-m-d H:i:s'), $returned->created->format('Y-m-d H:i:s'));

        I18n::setLocale($prevLocale);
    }
}
