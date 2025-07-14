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

    public function svgWithIdsProvider() {
        return [
            // SVG fragment,
            // expected strings (ordered) after combining fragment with itself,
            // (default=1) number of times the fragment is combined with itself,
            '<use> with href=' => [
                <<<SVG
                <circle id="fooId" cx="100" cy="100" fill="black" r="30"/>
                <use href="#fooId"/>
                SVG,
                ['id="fooId"', 'href="#fooId"', 'id="0"', 'href="#0"']
            ],
            '<use> with (deprecated) xlink:href=' => [
                <<<SVG
                <circle id="fooId" cx="100" cy="100" fill="black" r="30"/>
                <use xlink:href="#fooId"/>
                SVG,
                ['id="fooId"', 'xlink:href="#fooId"', 'id="0"', 'xlink:href="#0"']
            ],
            '<linearGradient> with href=' => [
                <<<SVG
                <defs>
                  <linearGradient id="fooId"></linearGradient>
                  <linearGradient href="#fooId"></linearGradient>
                </defs>
                SVG,
                ['id="fooId"', 'href="#fooId"', 'id="0"', 'href="#0"']
            ],
            '<radialGradient> with href=' => [
                <<<SVG
                <defs>
                  <radialGradient id="0"></radialGradient>
                  <radialGradient href="#0"></radialGradient>
                </defs>
                SVG,
                ['id="0"', 'href="#0"', 'id="1"', 'href="#1"']
            ],
            '<pattern> with href=' => [
                <<<SVG
                <defs>
                  <pattern id="0"></pattern>
                  <pattern href="#0"></pattern>
                </defs>
                SVG,
                ['id="0"', 'href="#0"', 'id="1"', 'href="#1"']
            ],
            '<textPath> with href=' => [
                <<<SVG
                <path id="MyPath" fill="none" stroke="red"
                      d="M10,90 Q90,90 90,45 Q90,10 50,10 Q10,10 10,40 Q10,70 45,70" />
                <text>
                  <textPath href="#MyPath">Text here text here</textPath>
                </text>
                SVG,
                ['id="MyPath"', 'href="#MyPath"', 'id="0"', 'href="#0"']
            ],
            'fill= with url()' => [
                <<<SVG
                <defs><linearGradient id="fooId"></linearGradient></defs>
                <rect fill="url(#fooId)" width="10" height="10"/>
                SVG,
                ['id="fooId"', 'fill="url(#fooId)"', 'id="0"', 'fill="url(#0)"']
            ],
            'fill= with quoted url()' => [
                <<<SVG
                <defs><radialGradient id="fooId"></radialGradient></defs>
                <rect fill='url("#fooId")' width="10" height="10"/>
                SVG,
                ['id="fooId"', 'fill="url(&quot;#fooId&quot;)"', 'id="0"', 'fill="url(#0)"']
            ],
            'style="fill" with url()' => [
                <<<SVG
                <defs><linearGradient id="fooId"></linearGradient></defs>
                <rect style="fill: url(#fooId)" width="100" height="100"/>
                SVG,
                ['id="fooId"', 'style="fill: url(#fooId)"', 'id="0"', 'style="fill: url(#0)"']
            ],
            'marker-end= with url()' => [
                <<<SVG
                <defs><marker id="fooId"></marker></defs>
                <path d="M 1000 750 L 2000 750" marker-end="url(#fooId)"/>
                SVG,
                ['id="fooId"', 'marker-end="url(#fooId)"', 'id="0"', 'marker-end="url(#0)"']
            ],
            'id grows beyond letter z' => [
                <<<SVG
                <circle id="0" cx="100" cy="100" fill="black" r="30"/>
                <use href="#0"/>
                SVG,
                ['id="0"', 'href="#0"', 'id="z"', 'href="#z"', 'id="10"', 'href="#10"'],
                36
            ],
            'id grows beyond letter zz' => [
                <<<SVG
                <circle id="0" cx="100" cy="100" fill="black" r="30"/>
                <use href="#0"/>
                SVG,
                ['id="0"', 'href="#0"', 'id="zz"', 'href="#zz"', 'id="100"', 'href="#100"'],
                36*36
            ],
            'fill= with url() and color code resembling id' => [
                <<<SVG
                <defs><radialGradient id="100"></radialGradient></defs>
                <rect fill="#100" width="100" height="100" />
                <rect fill="url(#100)" width="10" height="10"/>
                SVG,
                [
                    'id="100"', 'fill="#100"', 'fill="url(#100)"',
                    'id="0"', 'fill="#100"', 'fill="url(#0)"',
                ]
            ],
        ];
    }

    private function assertContainsInThisOrder($expectations, $actual) {
        foreach ($expectations as $expected) {
            $this->assertContains($expected, $actual);
            $pos = strpos($actual, $expected);
            $actual = substr($actual, $pos + strlen($expected));
        }
    }

    /**
     * @dataProvider svgWithIdsProvider
     */
    public function testIdConflictsAreAvoided($svgFragment, $expectations, $dups = 1) {
        $target = $this->newSVGSpriteTarget(array_map(
            fn($filename) =>
                $this->mockSVGFile($filename, <<<XML
                <?xml version="1.0"?>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink">
                $svgFragment
                </svg>
                XML),
            array_map(
                fn($n) => "file$n.svg",
                range(0, $dups)
            )
        ));

        $result = $this->compiler->generate($target);

        $this->assertContainsInThisOrder($expectations, $result);
    }
}
