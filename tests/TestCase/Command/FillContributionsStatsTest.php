<?php
namespace App\Test\TestCase\Command;

use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\Console\Command;
use Cake\ORM\TableRegistry;
use App\Model\Table\ContributionsStats;

class FillContributionsStatsCommand extends TestCase {
    use ConsoleIntegrationTestTrait;

    public $fixtures = array(
        'app.contributions',
        'app.contributions_stats'
    );

    function setUp() {
        parent::setUp();
        $this->UseCommandRunner();
        $this->ContributionsStats = TableRegistry::getTableLocator()->get('ContributionsStats');
    }

    function tearDown() {
        unset($this->ContributionsStats);
        parent::tearDown();
    }

    function testExecute_oldestDateIsCorrect() {
        $this->exec("fill_contributions_stats");

        $contributionStats = $this->ContributionsStats->find('all')->toList();
        $this->assertEquals('2014-04-09', $contributionStats[0]->date);
    }

    function testExecute_numberOfInsertedSentencesIsCorrect() {
        $this->exec("fill_contributions_stats");

        $insertedSentences = $this->ContributionsStats->find()
                                   ->where(['date' => '2018-04-12', 'action' => 'insert', 'type' => 'sentence'])
                                   ->first();
        // 3 in contributions but 1 delete as well
        $this->assertEquals(2, $insertedSentences->sentences);
    }

    function testExecute_numberOfDeletedSentencesIsCorrect() {
        $this->exec("fill_contributions_stats");

        $deletedSentences = $this->ContributionsStats->find()
                                   ->where(['date' => '2018-04-12', 'action' => 'delete', 'type' => 'sentence'])
                                   ->first();
        // 2 in contributions but 1 was inserted the same day
        $this->assertEquals(1, $deletedSentences->sentences);
    }

    function testExecute_numberOfInsertedLinksIsCorrect() {
        $this->exec("fill_contributions_stats");

        $insertedLinks = $this->ContributionsStats->find()
                                   ->where(['date' => '2018-04-12', 'action' => 'insert', 'type' => 'link'])
                                   ->first();
        $this->assertEquals(4, $insertedLinks->sentences);
    }

    function testExecute_numberOfDeletedLinksIsCorrect() {
        $this->exec("fill_contributions_stats");

        $deletedLinks = $this->ContributionsStats->find()
                                   ->where(['date' => '2018-04-12', 'action' => 'delete', 'type' => 'link'])
                                   ->first();
        $this->assertEquals(2, $deletedLinks->sentences);
    }

    function testExecute_noDeletedLinksWhenNotInContributions() {
        $this->exec("fill_contributions_stats");

        $deletedLinks = $this->ContributionsStats->find()
                                   ->where(['date' => '2017-04-13', 'action' => 'delete', 'type' => 'link']);
        $this->assertEquals(0, $deletedLinks->count());
    }

    function testExecute_oneInsertDeleted_noInsertAndNoDelete() {
        $this->exec("fill_contributions_stats");

        $insertedSentences = $this->ContributionsStats->find()
                                   ->where(['date' => '2014-09-02', 'action' => 'insert', 'type' => 'sentence']);
        $deletedSentences = $this->ContributionsStats->find()
                                  ->where(['date' => '2014-09-02', 'action' => 'delete', 'type' => 'sentence']);
        $this->assertEquals(0, $insertedSentences->count());
        $this->assertEquals(0, $deletedSentences->count());
    }
}
