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
        $this->assertEquals(2614, $result['2016-11-01']['added']);
        $this->assertEquals(2500, $result['2016-11-01']['linked']);
        $this->assertEquals(100, $result['2016-11-01']['unlinked']);
        $this->assertEquals(15, $result['2016-11-01']['deleted']);
        $this->assertEquals(5229, $result['2016-11-01']['total']);

        $this->assertArrayNotHasKey('unlinked', $result['2016-11-30']);
        $this->assertArrayNotHasKey('deleted', $result['2016-11-30']);
    }

    public function testGetActivityTimelineStatistics_emptyResult() {
        $result = $this->ContributionsStats->getActivityTimelineStatistics(2017, 01);
        $this->assertEquals(0, count($result));
    }
}
