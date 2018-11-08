<?php
/* Sinogram Test cases generated on: 2015-02-24 09:10:47 : 1424769047*/
namespace App\Test\TestCase\Model;

use App\Model\Sinogram;
use Cake\TestSuite\TestCase;

class SinogramTest extends TestCase {
	public $fixtures = array(
		'app.sinograms',
		'app.sinogram_subglyphs'
	);

	function startTest($method) {
		$this->Sinogram = ClassRegistry::init('Sinogram');
	}

	function endTest($method) {
		unset($this->Sinogram);
		ClassRegistry::flush();
	}

	function assertGlyphs($expected, $result) {
		$result = array_map(function($rec) {
			return isset($rec['Sinogram']['glyph']) ? $rec['Sinogram']['glyph'] : null;
		}, $result);
		$this->assertEqual($expected, $result);
	}

	function testSearch_EmptySubglyphReturnsEmptyResults() {
		$excepted = array();
		$result = $this->Sinogram->search(array());
		$this->assertEqual($excepted, $result);
	}

	function testSearch_ReturnsOneGlyphs() {
		$excepted = array('蝴');
		$result = $this->Sinogram->search(array('月', '虫', '古'));
		$this->assertGlyphs($excepted, $result);
	}

	function testSearch_ReturnSeveralGlyphs() {
		$excepted = array('朗', '蓢', '蝴');
		$result = $this->Sinogram->search(array('月', '虫'));
		$this->assertGlyphs($excepted, $result);
	}

	function testSearch_ReturnsProvidedGlyphWhenOneGlyphPassed() {
		$excepted = array('蓢', '朗');
		$result = $this->Sinogram->search(array('朗'));
		$this->assertGlyphs($excepted, $result);
	}
}
