<?php
namespace App\Test\TestCase\Lib;

use App\Lib\LanguagesLib;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

class LanguagesLibTest extends TestCase {

    private $oldConfig;

    public function setUp() {
        parent::setUp();
        Cache::delete('active_ui_languages');
        $this->oldConfig = Configure::read('UI.languages');
        Configure::write(
            'UI.languages',
            [
                'chi' => 'cmn',
                'cmn' => ['中文', 'Hans'],
                'fra' => ['Français', null],
                'fre' => 'fra',
                'por' => ['Português (Brasil)', 'BR'],
            ]
        );
    }

    public function tearDown() {
        Cache::delete('active_ui_languages');
        Configure::write('UI.languages', $this->oldConfig);
        parent::tearDown();
    }

    public function testActiveUiLanguages() {
        $expected = ['cmn', 'fra', 'por'];
        $actual = array_keys(LanguagesLib::activeUiLanguages());
        $this->assertEquals($expected, $actual);
    }
}
