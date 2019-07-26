<?php
namespace App\Test\TestCase\Model;

use App\Model\CurrentUser;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

/**
 * Contribution Test Case
 */
class ContributionsStatsTableTest extends TestCase {

    public $fixtures = array(
        'app.contributions_stats',
    );

    public function setUp() {
        parent::setUp();
        $this->ContributionsStats = TableRegistry::getTableLocator()->get('ContributionsStats');
    }

    public function tearDown() {
        unset($this->ContributionsStats);

        parent::tearDown();
    }

    public function testGetActivityTimelineStatistics() {
        $result = $this->ContributionsStats->getActivityTimelineStatistics(2016, 11);
        $this->assertEquals(30, count($result));
        $this->assertEquals('2016-11-01', $result[0]->date);
    }
}