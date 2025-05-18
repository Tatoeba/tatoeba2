<?php
namespace App\Test\TestCase\Command;

use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Console\Command;
use App\Model\Table\SentencesListsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

class CorrectNumberOfSentencesCommandTest extends TestCase {
    use ConsoleIntegrationTestTrait;

    public $fixtures = array(
        'app.sentences_lists',
        'app.sentences_sentences_lists',
    );

    function setUp() {
        parent::setUp();
        $this->UseCommandRunner();
        $this->SentencesLists = TableRegistry::getTableLocator()->get('SentencesLists');
    }

    function tearDown() {
        unset($this->SentencesLists);
        parent::tearDown();
    }

    function testExecute_CorrectNumberIsWritten() {
        $this->SentencesLists->updateAll(['numberOfSentences' => 99], []);

        $this->exec("correct_number_of_sentences");

        $sentencesLists = $this->SentencesLists->find('all')->toList();
        $newListsAndCounts = Hash::combine($sentencesLists, '{n}.id', '{n}.numberOfSentences');
        $expected = [
            1 => 3,
            2 =>0,
            3 => 1,
            4 => 2,
            5 => 0,
            6 => 1
        ];
        $this->assertEquals($expected, $newListsAndCounts);
    }

    function testExecute_DoesNotUpdateModifiedField()
    {
        $before = $this->SentencesLists
            ->find('list', ['valueField' => 'modified'])
            ->toArray();

        $this->exec("correct_number_of_sentences");

        $after = $this->SentencesLists
            ->find('list', ['valueField' => 'modified'])
            ->toArray();

        $this->assertEquals($before, $after);
    }
}
