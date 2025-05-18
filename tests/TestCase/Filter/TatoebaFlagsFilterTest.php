<?php

namespace App\Test\TestCase\Filter;

use App\Filter\TatoebaFlagsFilter;
use PHPUnit\Framework\TestCase;

class TatoebaFlagsFilterTest extends TestCase
{
    public function setUp() {
        parent::setUp();
        $this->flagsDir = WWW_ROOT . 'img' . DS . 'flags' . DS;
        $this->filter = new TatoebaFlagsFilter();
    }

    public function testHeader() {
        $file = $this->flagsDir . 'sprite_header';
        $content = file_get_contents($file);
        $result = $this->filter->input($file, $content);
        $this->assertEquals(trim($content), $result);
    }

    public function testFooter() {
        $file = $this->flagsDir . 'sprite_footer';
        $content = file_get_contents($file);
        $result = $this->filter->input($file, $content);
        $this->assertEquals(trim($content), $result);
    }

    public function testFlag() {
        $file = $this->flagsDir . 'epo.svg';
        $content = file_get_contents($file);
        $result = $this->filter->input($file, $content);

        $this->assertNotContains('<?xml', $result);
        $this->assertNotContains('<svg', $result);
        $this->assertNotContains('</svg>', $result);
        $this->assertNotContains('xmlns=', $result);
        $this->assertContains('<symbol id="epo"', $result);
    }

    public function testFlagForcesViewbox() {
        $file = $this->flagsDir . 'ryu.svg';
        $content = file_get_contents($file);
        $result = $this->filter->input($file, $content);

        $this->assertContains('<symbol id="ryu" viewBox="0 0 1050 700">', $result);
        $this->assertContains('<rect width="1050" height="700"', $result);
    }

    public function testFlagDoesNotOverwriteExistingViewbox() {
        $file = $this->flagsDir . 'ajp.svg';
        $content = file_get_contents($file);
        $result = $this->filter->input($file, $content);

        $this->assertContains('<symbol id="ajp" viewBox="0 0 4.5 3">', $result);
    }
}
