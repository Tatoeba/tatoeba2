<?php
namespace App\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use SimpleXMLElement;

class AssetsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    public function setUp() {
        parent::setUp();

        // Make sure debug is enabled so that
        // assets are generated on-the-fly
        Configure::write('debug', true);
    }

    public function testSvgSpriteIsWellFormed() {
        // Ignore output produced by GzipFilter
        $this->setOutputCallback(function ($output) { return ''; });

        // Make sure cached versions are not being served instead
        $caches = [
            CACHE . 'asset_compress' . DS . 'allflags.svg',
            WWW_ROOT . 'cache_svg' . DS . 'allflags.svg',
        ];
        foreach ($caches as $cache) {
            if (file_exists($cache)) {
                unlink($cache);
            }
        }

        $this->get("/cache_svg/allflags.svg");
        $svg = (string)$this->_response->getBody();
        $result = simplexml_load_string($svg);

        $this->assertInstanceOf(SimpleXMLElement::class, $result,
                                "allflags.svg does not contain valid XML");
    }
}
