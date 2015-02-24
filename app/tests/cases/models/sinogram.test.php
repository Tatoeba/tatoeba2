<?php
/* Sinogram Test cases generated on: 2015-02-24 09:10:47 : 1424769047*/
App::import('Model', 'Sinogram');

class SinogramTestCase extends CakeTestCase {
	var $fixtures = array('app.sinogram', 'app.sinogram_subglyph');

	function startTest() {
		$this->Sinogram =& ClassRegistry::init('Sinogram');
	}

	function endTest() {
		unset($this->Sinogram);
		ClassRegistry::flush();
	}

	function testSearch_EmptySubglyphReturnsEmptyResults() {
		$excepted = array();
		$result = $this->Sinogram->search(array());
		$this->assertEqual($excepted, $result);
	}
}
