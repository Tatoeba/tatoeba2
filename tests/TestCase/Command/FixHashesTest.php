<?php
namespace App\Test\TestCase\Command;

use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;

class FixHashesCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    public $fixtures = [
        'app.Sentences',
        'app.reindex_flags'
    ];

    public function setUp() {
        parent::setUp();
        $this->UseCommandRunner();
        $this->Sentences = TableRegistry::getTableLocator()->get('Sentences');
    }

    public function testExecute() {
        $this->exec('fix_hashes Sentences');
        $this->assertOutputContains(
            sprintf('%u rows checked', $this->Sentences->find()->count())
        );
        $this->assertOutputContains('5 rows changed');

        $hash = $this->Sentences->get(3)->hash;
        $modified = $this->Sentences->get(3)->modified;
        $this->assertEquals("2hfhma4\0\0\0\0\0\0\0\0\0", $hash);
        $this->assertEquals("2014-04-15 00:33:18", $modified);

        $hash = $this->Sentences->get(42)->hash;
        $modified = $this->Sentences->get(42)->modified;
        $this->assertEquals("23jek2o\0\0\0\0\0\0\0\0\0", $hash);
        $this->assertEquals("2017-04-09 11:39:02", $modified);
    }
}
