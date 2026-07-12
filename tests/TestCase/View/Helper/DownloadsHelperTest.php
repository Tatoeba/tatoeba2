<?php
namespace App\Test\TestCase\View\Helper;

use App\View\Helper\DownloadsHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use Cake\Core\Configure;
use Cake\Utility\Filesystem;

class DownloadsHelperTest extends TestCase {

    public $DownloadsHelper;

    private static function createTempTree() {
        $languages = ['eng', 'fra', 'jpn', 'unknown'];
        $files = ['sentences.tsv.bz2', 'sentences_detailed.tsv.bz2', 'sentences_CC0.tsv.bz2'];
        $fs = new Filesystem();
        foreach ($languages as $lang) {
            $path = TMP . 'exports' . DS . 'per_language' . DS . $lang;
            $fs->mkdir($path);
            foreach ($files as $file) {
                $newFile = $path . DS . "{$lang}_$file";
                touch($newFile);
            }
        }
    }

    public static function setUpBeforeClass(): void {
        self::createTempTree();
        Configure::write(
            'Downloads.path',
            TMP . 'exports' . DS
        );
    }

    public function setUp(): void {
        parent::setUp();
        $View = new View();
        $this->DownloadsHelper = new DownloadsHelper($View);
    }

    public function tearDown(): void {
        unset($this->DownloadsHelper);
        parent::tearDown();
    }

    public static function tearDownAfterClass(): void {
        (new Filesystem())->deleteDir(TMP . 'exports');
    }

    public function testCreateOptions_InvalidBasename() {
        $options = $this->DownloadsHelper->createOptions('foobar');

        $this->assertEquals(1, count($options));
        $this->assertEquals(
            Configure::read('Downloads.url') . 'foobar.tar.bz2',
            $options[0]['url']
        );
    }

    public static function filenameProvider () {
        return [
            ['sentences'],
            ['sentences_detailed'],
            ['sentences_CC0']
        ];
    }

    /**
     * @dataProvider filenameProvider
     **/
    public function testCreateOptions_ValidBasename($basename) {
        $options = $this->DownloadsHelper->createOptions($basename);

        $this->assertEquals(5, count($options));
        $this->assertEquals(
            Configure::read('Downloads.url') . "$basename.tar.bz2",
            $options[0]['url']
        );
        $this->assertEquals(
            Configure::read('Downloads.url') . 'per_language' . DS . 'eng' . DS . "eng_$basename.tsv.bz2",
            $options[1]['url']
        );
        $this->assertEquals('Japanese', $options[3]['language']);
    }

    /**
     * @dataProvider filenameProvider
     **/
    public function testCreateOptions_NoPerLanguageFilesAvailable($basename) {
        Configure::write('Downloads.path', '/some/path');

        $options = $this->DownloadsHelper->createOptions($basename);

        $this->assertEquals(1, count($options));
        $this->assertEquals(
            Configure::read('Downloads.url') . "$basename.tar.bz2",
            $options[0]['url']
        );
    }

    public static function fileFormatProvider () {
        return [
            'empty fields' => [[], ''],
            'one field' => [['id'], '%sparam%sid%s'],
            'two fields' => [['id', 'lang'], '%sparam%sid%ssymbol%sparam%slang%s'],
        ];
    }

    /**
     * @dataProvider fileFormatProvider
     **/
    public function testFileFormat($fields, $expected) {
        $result = $this->DownloadsHelper->fileFormat($fields);
        $this->assertStringMatchesFormat($expected, $result);
    }
}
