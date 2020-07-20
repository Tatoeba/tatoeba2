<?php
namespace App\Test\TestCase\View\Helper;

use App\View\Helper\VocabularyHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

class VocabularyHelperTest extends TestCase
{
    public $Vocabulary;

    public function setUp() {
        parent::setUp();
        $view = new View();
        $this->Vocabulary = new VocabularyHelper($view);
    }

    public function tearDown() {
        unset($this->Vocabulary);
        parent::tearDown();
    }

    public function sentenceCountProvider() {
        return [
            ['Unknown number of sentences', null],
            ['1 sentence', 1],
            ['2 sentences', 2],
            ['1000+ sentences', 1000],
        ];
    }

    /**
     * @dataProvider sentenceCountProvider
     */
    public function testSentenceCountLabel($expected, $count) {
        $this->assertEquals(
            $expected,
            $this->Vocabulary->sentenceCountLabel($count)
        );
    }
}
