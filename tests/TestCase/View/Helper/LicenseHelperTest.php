<?php
namespace App\Test\TestCase\View\Helper;

use App\View\Helper\LicenseHelper;
use App\View\Helper\SentenceLicenseHelper;
use App\View\Helper\AudioLicenseHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * App\View\Helper\FooHelper Test Case
 */
class LicenseHelperTest extends TestCase
{
    public $License;
    public $SentenceLicense;
    public $AudioLicense;

    public function setUp()
    {
        parent::setUp();
        $view = new View();
        $this->License = new LicenseHelper($view);
        $this->SentenceLicense = new SentenceLicenseHelper($view);
        $this->AudioLicense = new AudioLicenseHelper($view);
    }

    public function tearDown()
    {
        unset($this->License);
        unset($this->SentenceLicense);
        unset($this->AudioLicense);

        parent::tearDown();
    }

    public function getLicenseNameProvider() {
        return [
            // helper, license, link, expected
            ['SentenceLicense', '', true, 'Licensing'],
            ['SentenceLicense', 'CC0 1.0', false, 'CC0'],
            ['SentenceLicense', 'CC0 1.0', true, '<a'],
            ['AudioLicense', '', true, 'No license'],
            ['AudioLicense', 'CC0 1.0', false, 'CC0 1.0'],
            ['AudioLicense', 'CC BY 4.0', false, 'CC BY 4.0'],
            ['AudioLicense', 'CC BY 4.0', true, '<a'],
        ];
    }

    /**
     * @dataProvider getLicenseNameProvider
     */
    public function testGetLicenseName($helper, $license, $link, $expected)
    {
        $name = $this->$helper->getLicenseName($license, $link);
        $this->assertStringStartsWith($expected, $name);
    }

    public function getLicenseOptionsProvider() {
        return [
            // helper, admin, expected
            ['SentenceLicense', false, 2],
            ['SentenceLicense', true, 3],
            ['AudioLicense', false, 6],
            ['AudioLicense', true, 6],
        ];
    }

    /**
     * @dataProvider getLicenseOptionsProvider
     */
    public function testGetLicenseOptionsProvider($helper, $admin, $expected) {
        $options = $this->$helper->getLicenseOptions($admin);
        $this->assertEquals($expected, count($options));
    }

    public function testIsKnownLicense() {
        $this->assertTrue($this->SentenceLicense->isKnownLicense('CC0 1.0'));
        $this->assertFalse($this->SentenceLicense->isKnownLicense('ABC'));
    }
}
