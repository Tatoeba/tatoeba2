<?php
namespace App\Test\TestCase\View\Helper;

use App\View\Helper\HtmlHelper;
use App\View\Helper\TranscriptionsHelper;
use Cake\Controller\Controller;
use Cake\TestSuite\TestCase;
use Cake\View\View;

class TranscriptionsHelperTest extends TestCase {
    public $fixtures = array(
        'app.Contributions',
        'app.FavoritesUsers',
        'app.Languages',
        'app.Links',
        'app.ReindexFlags',
        'app.Sentences',
        'app.SentenceComments',
        'app.SentenceAnnotations',
        'app.SentencesLists',
        'app.SentencesSentencesLists',
        'app.Tags',
        'app.TagsSentences',
        'app.Transcriptions',
        'app.Users',
        'app.UsersLanguages',
        'app.Walls',
        'app.WallThreads'
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
        $this->assertFurigana(
            '[行|い|]',
            '行｛い｜｝',
            '<ruby>行<rp>（</rp><rt>い</rt><rp>）</rp></ruby>'
        );
    }

    function assertPinyin($numeric, $diacritic) {
        $transcription = array(
            'text' => $numeric,
            'script' => 'Latn',
        );
        $expected =
            '<span style="display:none" class="markup">'.$numeric.'</span>'.
            $diacritic;
        $result = $this->T->transcriptionAsHTML('cmn', $transcription);
        $this->assertEquals($expected, $result);
    }

    function testTranscriptionAsHTML_cmn() {
        $this->assertPinyin(
            'Ni3hao3.',
            'Nǐhǎo.'
        );
        $this->assertPinyin(
            'Ta1 zai4 zher4!',
            'Tā zài zhèr!'
        );
        $this->assertPinyin(
            'A1er3ji2li4ya4 shi4 E2luo2si1 he2 Zhong1guo2 de5 qin1mi4 meng2you3.',
            'Āěrjílìyà shì Éluósī hé Zhōngguó de qīnmì méngyǒu.'
        );
    }
}
