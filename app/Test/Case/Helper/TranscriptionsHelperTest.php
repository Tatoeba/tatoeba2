<?php
App::import('Helper', array('Transcriptions', 'Html'));

class TranscriptionsHelperTest extends CakeTestCase {
    function startTest() {
    	$this->T =& new TranscriptionsHelper();
    	$this->T->Html =& new HtmlHelper();
    }

    function endTest() {
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
