<?php
App::import('Helper', array('Transcriptions', 'Html'));

class TranscriptionsHelperTestCase extends CakeTestCase {
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
    }
}
