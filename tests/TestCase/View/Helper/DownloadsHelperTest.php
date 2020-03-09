<?php
/**
 * If the static property $files in the test class is set, we return its
 * value instead of calling shell_exec() in the private method
 * DownloadsHelper\availableFiles().
 **/
namespace App\View\Helper {

    function shell_exec($command) {
        return \App\Test\TestCase\View\Helper\DownloadsHelperTest::$files ?: \shell_exec($command);
    }
}

namespace App\Test\TestCase\View\Helper {

    use App\View\Helper\DownloadsHelper;
    use Cake\TestSuite\TestCase;
    use Cake\View\View;

    class DownloadsHelperTest extends TestCase {

        public $DownloadsHelper;

        public static $files = null;

        public function setUp() {
            parent::setUp();
            $View = new View();
            $this->DownloadsHelper = new DownloadsHelper($View);
        }

        public function tearDown() {
            self::$files = null;
            unset($this->DownloadsHelper);
            parent::tearDown();
        }

        public function testCreateOptions_InvalidBasename() {
            $options = $this->DownloadsHelper->createOptions('foobar');

            $this->assertEquals(1, count($options));
            $this->assertEquals('All languages', $options[0]['language']);
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
            self::$files = "eng/eng_$basename.tar.bz2\n" .
                           "fra/fra_$basename.tar.bz2\n" .
                           "jpn/jpn_$basename.tar.bz2\n";

            $options = $this->DownloadsHelper->createOptions($basename);

            $this->assertEquals(4, count($options));
            $this->assertEquals(
                $this->DownloadsHelper::DOWNLOAD_URL . "/$basename.tar.bz2",
                $options[0]['url']
            );
            $this->assertEquals(
                $this->DownloadsHelper::DOWNLOAD_URL . "/per_language/eng/eng_$basename.tar.bz2",
                $options[1]['url']
            );
            $this->assertEquals('Japanese', $options[3]['language']);
        }

        /**
         * @dataProvider filenameProvider
         **/
        public function testCreateOptions_NoPerLanguageFilesAvailable($basename) {
            self::$files = "\n";

            $options = $this->DownloadsHelper->createOptions($basename);

            $this->assertEquals(1, count($options));
            $this->assertEquals('All languages', $options[0]['language']);
        }

        public function fileFormatProvider () {
            return [
                'empty fields' => [[], ''],
                'one field' => [['id'], '<span class="param">id</span>'],
                'two fields' => [
                    ['id', 'lang'],
                    '<span class="param">id</span>' .
                    '<span class="symbol">[tab]</span>' .
                    '<span class="param">lang</span>'
                ]
            ];
        }

        /**
         * @dataProvider fileFormatProvider
         **/
        public function testFileFormat($fields, $expected) {
            $result = $this->DownloadsHelper->fileFormat($fields);
            $this->assertEquals($expected, $result);
        }
    }
}
