<?php
namespace App\Test\TestCase\View\Helper;

use App\View\Helper\DownloadsHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Core\Configure;

class DownloadsHelperTest extends TestCase {

    public $DownloadsHelper;

    private static function createTempTree() {
        $languages = ['eng', 'fra', 'jpn', 'unknown'];
        $files = ['sentences.tsv.bz2', 'sentences_detailed.tsv.bz2', 'sentences_CC0.tsv.bz2'];
        foreach ($languages as $lang) {
            $path = Folder::addPathElement(TMP, ['exports', 'per_language', $lang]);
            $subdir = new Folder($path, true);
            foreach ($files as $file) {
                $newFile = Folder::addPathElement($subdir->path, "${lang}_$file");
                new File($newFile, true);
            }
        }
    }

    public static function setUpBeforeClass() {
        self::createTempTree();
        Configure::write(
            'Downloads.path',
            Folder::addPathElement(TMP, 'exports/')
        );
    }

    public function setUp() {
        parent::setUp();
        $View = new View();
        $this->DownloadsHelper = new DownloadsHelper($View);
    }

    public function tearDown() {
        unset($this->DownloadsHelper);
        parent::tearDown();
    }

    public static function tearDownAfterClass() {
        $dir = new Folder(Folder::addPathElement(TMP, 'exports'));
        $dir->delete();
    }

    public function testCreateOptions_InvalidBasename() {
        $options = $this->DownloadsHelper->createOptions('foobar');

        $this->assertEquals(1, count($options));
        $this->assertEquals(
            Folder::addPathElement(Configure::read('Downloads.url'), "foobar.tar.bz2"),
            $options[0]['url']
        );
    }

    public function filenameProvider () {
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
            Folder::addPathElement(
                Configure::read('Downloads.url'),
                "$basename.tar.bz2"
            ),
            $options[0]['url']
        );
        $this->assertEquals(
            Folder::addPathElement(
                Configure::read('Downloads.url'),
                ['per_language', 'eng', "eng_$basename.tsv.bz2"]
            ),
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
            Folder::addPathElement(
                Configure::read('Downloads.url'),
                "$basename.tar.bz2"
            ),
            $options[0]['url']
        );
    }

    public function fileFormatProvider () {
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
