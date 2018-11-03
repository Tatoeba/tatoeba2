<?php
namespace App\Test\TestCase\View\Helper;

App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('HtmlHelper', 'View/Helper');
App::uses('TranscriptionsHelper', 'View/Helper');

class TranscriptionsHelperTest extends CakeTestCase {
    public $fixtures = array(
        'app.aro',
        'app.aco',
        'app.aros_aco',
        'app.contribution',
        'app.favorites_user',
        'app.group',
        'app.language',
        'app.link',
        'app.reindex_flag',
        'app.sentence',
        'app.sentence_comment',
        'app.sentence_annotation',
        'app.sentences_list',
        'app.sentences_sentences_list',
        'app.tag',
        'app.tags_sentence',
        'app.transcription',
        'app.user',
        'app.users_language',
        'app.wall',
        'app.wall_thread',
    );

    function setUp() {
        parent::setUp();
        $Controller = new Controller();
        $View = new View($Controller);
    	$this->T = new TranscriptionsHelper($View);
    	$this->T->Html = new HtmlHelper($View);
    }

    function endTest($method) {
    	unset($this->T);
    	ClassRegistry::flush();
    }

    function assertFurigana($internal, $editable, $ruby) {
        $transcription = array(
            'text' => $internal,
            'script' => 'Hrkt',
        );
        $expected =
            '<span style="display:none" class="markup">'.$editable.'</span>'.
            $ruby;
        $result = $this->T->transcriptionAsHTML('jpn', $transcription);
        $this->assertEqual($expected, $result);
    }

    function testTranscriptionAsHTML_jpn() {
        $this->assertFurigana(
            '[言葉|こと|ば]',
            '言葉｛こと｜ば｝',
            '<ruby>言<rp>（</rp><rt>こと</rt><rp>）</rp></ruby>'.
            '<ruby>葉<rp>（</rp><rt>ば</rt><rp>）</rp></ruby>'
        );
        $this->assertFurigana(
            '[−2|マイナス|に]',
            '&minus;2｛マイナス｜に｝',
            '<ruby>&minus;<rp>（</rp><rt>マイナス</rt><rp>）</rp></ruby>'.
            '<ruby>2<rp>（</rp><rt>に</rt><rp>）</rp></ruby>'
        );
        $this->assertFurigana(
            '[A&R|エー|アンド|アール]',
            'A&amp;R｛エー｜アンド｜アール｝',
            '<ruby>A<rp>（</rp><rt>エー</rt><rp>）</rp></ruby>'.
            '<ruby>&amp;<rp>（</rp><rt>アンド</rt><rp>）</rp></ruby>'.
            '<ruby>R<rp>（</rp><rt>アール</rt><rp>）</rp></ruby>'
        );
    }
}
