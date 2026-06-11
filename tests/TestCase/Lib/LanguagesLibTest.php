<?php
namespace App\Test\TestCase\Lib;

use App\Lib\LanguagesLib;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

class LanguagesLibTest extends TestCase {

    private $oldConfig;

    public function setUp(): void {
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

    public function tearDown(): void {
        Cache::delete('active_ui_languages');
        Configure::write('UI.languages', $this->oldConfig);
        parent::tearDown();
    }

    public function testActiveUiLanguages() {
        $expected = ['zh-cn', 'fr', 'pt-br'];
        $actual = array_keys(LanguagesLib::activeUiLanguages());
        $this->assertEquals($expected, $actual);
    }

    public function languageDirectionProvider() {
        return [
            ['eng', 'ltr'],
            ['jpn', 'ltr'],
            ['nav', 'ltr'],
            ['crs', 'ltr'],
            ['lij', 'ltr'],

            ['ara', 'rtl'],
            ['heb', 'rtl'],
            ['yid', 'rtl'],
            ['pnb', 'rtl'],
            ['ckb', 'rtl'],

            ['lad', 'auto'],
            ['qxq', 'auto'],
            ['zlm', 'auto'],
            ['knc', 'auto'],
            ['bal', 'auto'],
            [null, 'auto'],
        ];
    }

    /**
     * @dataProvider languageDirectionProvider
     */
    public function testGetLanguageDirection($lang, $expectedDirection) {
        $actualDirection = LanguagesLib::getLanguageDirection($lang);
        $this->assertSame($expectedDirection, $actualDirection);
    }
}
