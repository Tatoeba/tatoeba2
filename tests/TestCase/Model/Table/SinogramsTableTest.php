<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SinogramsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class SinogramsTableTest extends TestCase
{
    public $fixtures = array(
        'app.sinograms',
        'app.sinogram_subglyphs'
    );

    public $Sinograms;

    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Sinograms') ? [] : ['className' => SinogramsTable::class];
        $this->Sinograms = TableRegistry::getTableLocator()->get('Sinograms', $config);
    }

    public function tearDown()
    {
        unset($this->Sinograms);

        parent::tearDown();
    }

    public function assertGlyphs($expected, $result) {
        $result = array_map(function($ent) {
            return $ent->glyph;
        }, $result);
        $this->assertEquals($expected, $result);
    }

    public function testSearch_EmptySubglyphReturnsEmptyResults() {
        $excepted = array();
        $result = $this->Sinograms->search(array());
        $this->assertEquals($excepted, $result);
    }

    public function testSearch_ReturnsOneGlyphs() {
        $excepted = array('蝴');
        $result = $this->Sinograms->search(array('月', '虫', '古'));
        $this->assertGlyphs($excepted, $result);
    }

    public function testSearch_ReturnSeveralGlyphs() {
        $excepted = array('朗', '蓢', '蝴');
        $result = $this->Sinograms->search(array('月', '虫'));
        $this->assertGlyphs($excepted, $result);
    }

    public function testSearch_ReturnsProvidedGlyphWhenOneGlyphPassed() {
        $excepted = array('蓢', '朗');
        $result = $this->Sinograms->search(array('朗'));
        $this->assertGlyphs($excepted, $result);
    }
}
