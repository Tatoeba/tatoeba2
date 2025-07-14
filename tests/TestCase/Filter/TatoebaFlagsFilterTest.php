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
            $files,
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
        $target = $this->newSVGSpriteTarget([new Local($file)]);

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
        $target = $this->newSVGSpriteTarget([new Local($file)]);

        $result = $this->compiler->generate($target);

        $this->assertContains('<symbol id="ryu" viewBox="0 0 1050 700">', $result);
        $this->assertContains('<rect width="1050" height="700"', $result);
    }

    public function testFlagDoesNotOverwriteExistingViewbox() {
        $file = $this->flagsDir . 'ajp.svg';
        $target = $this->newSVGSpriteTarget([new Local($file)]);

        $result = $this->compiler->generate($target);

        $this->assertContains('<symbol id="ajp" viewBox="0 0 4.5 3">', $result);
    }

    private function mockSVGFile($path, $contents) {
        $mock = $this
            ->getMockBuilder('MiniAsset\File\FileInterface')
            ->setMethods(['name', 'contents', 'modifiedTime', 'path'])
            ->getMock();
        $mock
            ->method('path')
            ->will($this->returnValue($path));
        $mock
            ->method('contents')
            ->will($this->returnValue($contents));
        return $mock;
    }

    public function testFilterRaisesExceptionIfResultContainsDuplicateIds() {
        $expected = new \RuntimeException("id(s) dup,dup2 used more than once in tmptarget");
        $svg = <<<DUPIDS
               <svg>
                 <circle id="dup"/><circle id="dup"/>
                 <circle id="dup2"/><circle id="dup2"/>
               </svg>
               DUPIDS;
        $target = new AssetTarget(
            'final.svg',
            [$this->mockSVGFile('tmptarget', $svg)],
            ['TatoebaFlagsFilter']
        );

        try {
            $result = $this->compiler->generate($target);
        } catch (\Exception $actual) {
            $this->assertEquals($expected, $actual);
            return;
        }
        $this->fail("RuntimeException was not thrown");
    }
}
