<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ExportsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class ExportsTableTest extends TestCase
{
    public $Exports;

    public $fixtures = [
        'app.Exports',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->Exports = TableRegistry::get('Exports');
    }

    public function tearDown()
    {
        unset($this->Exports);
        parent::tearDown();
    }

    public function testGetExportsOf()
    {
        $expected = [
            [
                'name' => 'Kazuki\'s sentences',
                'status' => 'online',
                'url' => 'https://downloads.tatoeba.org/exports/kazuki_sentences.zip',
            ],
            [
                'name' => 'Japanese-Russian sentence pairs',
                'status' => 'queued',
                'url' => null,
            ],
        ];

        $result = $this->Exports->getExportsOf(7);

        $this->assertEquals($expected, $result->hydrate(false)->toArray());
    }
}
