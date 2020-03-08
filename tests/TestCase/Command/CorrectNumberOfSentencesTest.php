<?php
namespace App\Test\TestCase\Command;

use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Console\Command;
use App\Model\Table\SentencesListsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

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
        $sentencesLists = $this->SentencesLists->find('all')->toList();
        $originalListsAndCounts = array_combine(array_column($sentencesLists, 'id'), array_column($sentencesLists, 'numberOfSentences'));
        foreach($sentencesLists as $sentencesList) {
            $sentencesList->numberOfSentences = 99;
            $this->SentencesLists->save($sentencesList);
        }

        $this->exec("correct_number_of_sentences");

        $sentencesLists = $this->SentencesLists->find('all')->toList();
        $newListsAndCounts = array_combine(array_column($sentencesLists, 'id'), array_column($sentencesLists, 'numberOfSentences'));
        $this->assertEquals($newListsAndCounts[1], 3);
        $this->assertEquals($newListsAndCounts[2], 0);
        $this->assertEquals($newListsAndCounts[3], 1);
        $this->assertEquals($newListsAndCounts[4], 2);
        $this->assertEquals($newListsAndCounts[5], 0);
        $this->assertEquals($newListsAndCounts[6], 1);
    }
}
