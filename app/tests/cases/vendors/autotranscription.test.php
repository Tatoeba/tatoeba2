<?php
App::import('Vendor', 'Autotranscription');

class AutotranscriptionTestCase extends CakeTestCase {
    function startTest() {
        $this->AT =& new Autotranscription();
    }

    function endTest() {
        unset($this->AT);
    }

    function _assertCheck($method, $sentence, $transcription, $true) {
        $errors = array();
        $result = $this->AT->{$method}($sentence, $transcription, $errors);
        if ($true)
            $this->assertTrue($result, "$method “${sentence}” → “${transcription}” should pass check, error is ".implode("\n", $errors));
        else
            $this->assertFalse($result, "$method “${sentence}” → “${transcription}” should NOT pass check");
    }

    function testFuriganaSyntax() {
        $testGood = array(
            '行けそう。' => array(
                '[行|い]けそう。',
            ),
            /* Allow mixing with other scripts */
            'ＡとＢは違う。' => array(
                '[Ａ|えい]と[Ｂ|びー]は[違|ちが]う。'
            ),
            '「やっと２人になれたね。」' => array(
                '「やっと[２人|ふたり]になれたね。」',
            ),
            'Perfumeの曲' => array(
                '[Perfume|パフューム]の[曲|きょく]',
            ),
        );
        $testBad = array(
            '行けそう。' => array(
                /* No spaces */
                '[行|い]け そう 。',
                /* No furigana */
                '行けそう。',
                /* Invalid furigana */
                '[行|]けそう。',
                '[行|行]けそう。',
                '[行|a]けそう。',
                /* Syntax error */
                '[|い]けそう。',
                '[行|いけそう。',
                '[行|い]]けそう。',
                '[行|い|]けそう。',
                '[行い]けそう。',
                '[行|い]けそう[|]。',
                /* Transcription different from the sentence */
                '[行|い]けそ。',
                '[行|い]けそう',
                '[逝|い]けそう。',
            ),
            'Perfumeの曲' => array(
                /* Everything that is not kana should have furi */
                'Perfumeの[曲|きょく]',
                '[Perfume|]の[曲|きょく]',
            ),
        );
        $this->assertValidTranscriptions('jpn', 'Jpan', 'Hrkt', $testGood);
        $this->assertInvalidTranscriptions('jpn', 'Jpan', 'Hrkt', $testBad);
    }

    function assertTranscriptions($lang, $fromScript, $toScript, $transcriptions, $validity) {
        $method = "${lang}_${fromScript}_to_${toScript}_validate";
        foreach ($transcriptions as $from => $tos)
            foreach ($tos as $to)
                $this->_assertCheck($method, $from, $to, $validity);
    }

    function assertInvalidTranscriptions($lang, $fromScript, $toScript, $transcriptions) {
        $this->assertTranscriptions($lang, $fromScript, $toScript, $transcriptions, false);
    }

    function assertValidTranscriptions($lang, $fromScript, $toScript, $transcriptions) {
        $this->assertTranscriptions($lang, $fromScript, $toScript, $transcriptions, true);
    }

    function _assertFurigana($kanji, $reading, $expected) {
        $result = $this->AT->formatFurigana($kanji, $reading);
        $this->assertEqual($expected, $result, "furigana should be formatted like “${expected}”, got “${result}”");
    }

    function test_formatFurigana() {
        $this->_assertFurigana('男', 'おとこ', '[男|おとこ]');
        $this->_assertFurigana('男の子', 'おとこのこ', '[男|おとこ]の[子|こ]');
        /* This is plain wrong, only to show we can't handle every case */
        $this->_assertFurigana('物の具', 'もののぐ', '[物|も]の[具|のぐ]');

        /* Remove furigana on numbers since they are almost always wrong.
           Mecab parses them individually, e.g. 10 reads いちぜろ. */
        $this->_assertFurigana('１', 'いち', '１');
        $this->_assertFurigana('1', 'いち', '1');
    }
}
