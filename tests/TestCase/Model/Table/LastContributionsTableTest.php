<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LastContributionsTable;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;

class LastContributionsTableTest extends TestCase {
	public $fixtures = array(
        'app.last_contributions',
        'app.users',
    );

    function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
		$this->LastContributions = TableRegistry::getTableLocator()->get('LastContributions');
	}

	function tearDown() {
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