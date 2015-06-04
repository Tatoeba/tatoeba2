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
        $result = $this->AT->{$method}($sentence, $transcription);
        if ($true)
            $this->assertTrue($result, "$method “${sentence}” → “${transcription}” should pass check");
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
            )
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
        );
        $method = 'jpn_Jpan_to_Hrkt_validate';
        foreach ($testGood as $japanese => $furis)
            foreach ($furis as $furi)
                $this->_assertCheck($method, $japanese, $furi, true);
        foreach ($testBad as $japanese => $furis)
            foreach ($furis as $furi)
                $this->_assertCheck($method, $japanese, $furi, false);
    }
}
