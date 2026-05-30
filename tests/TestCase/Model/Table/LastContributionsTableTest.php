<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LastContributionsTable;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

class LastContributionsTableTest extends TestCase {
	public $fixtures = array(
        'app.LastContributions',
        'app.Users',
    );

    private $LastContributions;

    function setUp(): void {
        parent::setUp();
		$this->LastContributions = $this->fetchTable('LastContributions');
	}

	function tearDown(): void {
		unset($this->LastContributions);
		parent::tearDown();
    }
    
    function testGetCurrentContributors() {
        $stats = $this->LastContributions->getCurrentContributors();

        $result = Hash::extract($stats, '{n}.total');
        $expected = [2, 1];

        $this->assertEquals($result, $expected);
    }
}
