<?php
namespace App\Test\TestCase\Lib;

use App\Lib\Licenses;
use Cake\TestSuite\TestCase;

class LicensesTest extends TestCase {

    function testNameToKeys() {
        $map = Licenses::nameToKeys(Licenses::getSentenceLicenses());
        $this->assertEquals(3, count($map));
        $this->assertEquals('', $map['Licensing issue']);
        $this->assertEquals('CC BY 2.0 FR', $map['CC BY 2.0 FR']);
    }
}
