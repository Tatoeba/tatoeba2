<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Export;
use Cake\TestSuite\TestCase;

class ExportTest extends TestCase
{
    public $Export;

    public function setUp()
    {
        parent::setUp();
        $this->Export = new Export();
    }

    public function tearDown()
    {
        unset($this->Export);
        parent::tearDown();
    }

    public function filesProvider()
    {
        return [
            [null, ''],
            [false, ''],
            ['foo.csv', '.csv'],
            ['/', ''],
            ['/foo', ''],
            ['/foo.csv', '.csv'],
            ['/foo/', ''],
            ['/foo/bar', ''],
            ['/foo/bar.csv', '.csv'],
            ['/foo/bar.zip', '.zip'],
            ['/foo/bar.csv.zip', '.zip'],
            ['/foo/bar.tar', '.tar'],
            ['/foo/bar.tar.gz', '.tar.gz'],
            ['/foo/tar.gz', '.gz'],
            ['/foo/one.two', '.two'],
            ['/foo/one.two.three', '.three'],
            ['foo.csv.', ''],
            ['foo.tar.', ''],
        ];
    }

    /**
     * @dataProvider filesProvider
     */
    public function testGetFileExtension($path, $expectedExtension)
    {
        $this->Export->filename = $path;
        $this->assertEquals($expectedExtension, $this->Export->getFileExtension());
    }
}
