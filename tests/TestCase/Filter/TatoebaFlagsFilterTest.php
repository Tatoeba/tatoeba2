<?php

namespace App\Test\TestCase\Filter;

use App\Filter\TatoebaFlagsFilter;
use MiniAsset\AssetTarget;
use MiniAsset\File\Local;
use MiniAsset\File\Target;
use MiniAsset\Filter\FilterRegistry;
use MiniAsset\Output\Compiler;
use PHPUnit\Framework\TestCase;

class TatoebaFlagsFilterTest extends TestCase
{
    public function setUp() {
        parent::setUp();

        $this->flagsDir = WWW_ROOT . 'img' . DS . 'flags' . DS;

        $filter = new TatoebaFlagsFilter();
        $filter->settings(['tmptargets' => ['tmptarget']]);
        $filters = new FilterRegistry(['TatoebaFlagsFilter' => $filter]);
        $this->compiler = new Compiler($filters, false);
    }

    private function newSVGSpriteTarget($files) {
        $tmptarget = new AssetTarget(
            'tmptarget',
            array_map(fn($file) => new Local($file), $files),
            ['TatoebaFlagsFilter'],
            [$this->flagsDir]
        );
        return new AssetTarget(
            'sprites.svg',
            [new Target($tmptarget, $this->compiler)],
            ['TatoebaFlagsFilter']
        );
    }

    public function testFlag() {
        $file = $this->flagsDir . 'epo.svg';
        $target = $this->newSVGSpriteTarget([$file]);

        $result = $this->compiler->generate($target);

        $didParsingFail = simplexml_load_string($result) === false;
        $this->assertFalse($didParsingFail);

        $this->assertContains('<?xml version="1.0"?>', $result);
        $this->assertContains('<svg ', $result);
        $this->assertContains('xmlns="http://www.w3.org/2000/svg"', $result);
        $this->assertContains('xmlns:xlink="http://www.w3.org/1999/xlink', $result);
        $this->assertContains('<symbol id="epo"', $result);
        $this->assertContains('</svg>', $result);
    }

    public function testFlagForcesViewbox() {
        $file = $this->flagsDir . 'ryu.svg';
        $target = $this->newSVGSpriteTarget([$file]);

        $result = $this->compiler->generate($target);

        $this->assertContains('<symbol id="ryu" viewBox="0 0 1050 700">', $result);
        $this->assertContains('<rect width="1050" height="700"', $result);
    }

    public function testFlagDoesNotOverwriteExistingViewbox() {
        $file = $this->flagsDir . 'ajp.svg';
        $target = $this->newSVGSpriteTarget([$file]);

        $result = $this->compiler->generate($target);

        $this->assertContains('<symbol id="ajp" viewBox="0 0 4.5 3">', $result);
    }
}
