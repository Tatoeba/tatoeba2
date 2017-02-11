<?php
App::import('Lib', 'Autotranscription');

class AutotranscriptionTest extends CakeTestCase {
    function startTest($method) {
        $this->AT = new Autotranscription();
    }

    function endTest($method) {
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
            /* Allow spaces */
            '今は？ 今は？' => array('[今|いま]は？ [今|いま]は？'),
            '今は？　今は？' => array('[今|いま]は？　[今|いま]は？'),
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

    function testPinyin() {
        $testGood = array(
            '你不得不制造一些借口。' => array(
                'Ni3 bu4de2bu4 zhi4zao4 yi1xie1 jie4kou3.',
            ),
        );
        $testBad = array(
            '你不得不制造一些借口。' => array(
                'Ni3 bu4de2bu4 製 zao4 yi1xie1 jie4kou3.',
            ),
        );
        $this->assertValidTranscriptions('cmn', 'Hant', 'Latn', $testGood);
        $this->assertInvalidTranscriptions('cmn', 'Hant', 'Latn', $testBad);
    }
}
