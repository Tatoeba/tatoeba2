<?php
namespace App\Test\TestCase\View\Helper;

use App\View\Helper\HtmlHelper;
use App\View\Helper\TranscriptionsHelper;
use Cake\Controller\Controller;
use Cake\TestSuite\TestCase;
use Cake\View\View;

class TranscriptionsHelperTest extends TestCase {
    public $fixtures = array(
        'app.aros',
        'app.acos',
        'app.aros_acos',
        'app.contributions',
        'app.favorites_users',
        'app.groups',
        'app.languages',
        'app.links',
        'app.reindex_flags',
        'app.sentences',
        'app.sentence_comments',
        'app.sentence_annotations',
        'app.sentences_lists',
        'app.sentences_sentences_lists',
        'app.tags',
        'app.tags_sentences',
        'app.transcriptions',
        'app.users',
        'app.users_languages',
        'app.walls',
        'app.wall_threads'
    );

    function setUp() {
        parent::setUp();
        $View = new View();
    	$this->T = new TranscriptionsHelper($View);
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
        $this->assertEquals($expected, $result);
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
