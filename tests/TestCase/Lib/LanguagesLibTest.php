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
                'chi' => 'zh-cn',
                'cmn' => 'zh-cn',
                'zh-cn' => ['中文'],
                'fr' => ['Français'],
                'fre' => 'fr',
                'fra' => 'fr',
                'pt-br' => ['Português (Brasil)'],
                'por' => 'pt-br',
            ]
        );
    }

    public function tearDown() {
        Cache::delete('active_ui_languages');
        Configure::write('UI.languages', $this->oldConfig);
        parent::tearDown();
    }

    public function testActiveUiLanguages() {
        $expected = ['zh-cn', 'fr', 'pt-br'];
        $actual = array_keys(LanguagesLib::activeUiLanguages());
        $this->assertEquals($expected, $actual);
    }
}
