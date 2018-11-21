<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LanguagesTable;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;

class LanguagesTableTest extends TestCase {
    public $fixtures = array(
        'app.languages',
    );

    function setUp() {
        parent::setUp();
        $this->Languages = TableRegistry::getTableLocator()->get('Languages');
    }

    function tearDown() {
        unset($this->Languages);
        parent::tearDown();
    }

    function testGetNativeSpeakersStatistics() {
        $result = $this->Languages->getNativeSpeakersStatistics();
        $this->assertEquals(7, count($result));
    }

    function testGetUsersLanguagesStatistics() {
        $result = $this->Languages->getNativeSpeakersStatistics();
        $this->assertEquals(7, count($result));
    }
}