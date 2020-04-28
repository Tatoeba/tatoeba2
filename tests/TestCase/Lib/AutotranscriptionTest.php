<?php
namespace App\Test\TestCase\Lib;

use App\Lib\Autotranscription;
use Cake\TestSuite\TestCase;

class AutotranscriptionTest extends TestCase {
    public function setUp(){
        parent::setUp();
        $this->AT = new Autotranscription();
    }

    public function tearDown() {
        parent::tearDown();
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
            /* Allow furi to span over more than one kanji */
            '田舎道' => array('[田舎道|いなか||みち]'),
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
            /* Do not allow furi to span over nothing */
            '4年間' => array('[4年間||ねん|かん]'),
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

    function _mockHttpClient($body) {
        $response = $this->getMockBuilder(Cake\Http\Response::class)
                       ->setMethods(['isOk', 'getStringBody'])
                       ->getMock();
        $response->expects($this->once())
                 ->method('isOk')
                 ->will($this->returnValue(true));
        $response->expects($this->once())
                 ->method('getStringBody')
                 ->will($this->returnValue($body));
        $client = $this->getMockBuilder(Cake\Network\Http\Client::class)
                       ->setMethods(['get'])
                       ->getMock();
        $client->expects($this->once())
               ->method('get')
               ->will($this->returnValue($response));
        return $client;
    }

    function test_jpn_Jpan_to_Hrkt_generate() {
        $needsReview = false;
        $sentence = '行こうよ。';
        $expectedFurigana = '[行|い]こうよ。';
        $response = '<?xml version="1.0" encoding="UTF-8"?>
<root>
<parse>
<token><reading furigana="い"><![CDATA[行]]></reading><![CDATA[こう]]></token>
<token><![CDATA[よ]]></token>
<token><![CDATA[。]]></token>
</parse>
</root>
';
        $this->AT->HTTPClient($this->_mockHttpClient($response));

        $furigana = $this->AT->jpn_Jpan_to_Hrkt_generate($sentence, $needsReview);

        $this->assertEquals($furigana, $expectedFurigana);
    }

    function test_jpn_Jpan_to_Hrkt_generate_empty() {
        $needsReview = false;
        $sentence = '';
        $expectedFurigana = '';
        $response = '<?xml version="1.0" encoding="UTF-8"?>
<root>
<parse>
</parse>
</root>
';
        $this->AT->HTTPClient($this->_mockHttpClient($response));

        $furigana = $this->AT->jpn_Jpan_to_Hrkt_generate($sentence, $needsReview);

        $this->assertEquals($furigana, $expectedFurigana);
    }

    function lfnSentencesProvider() {
        return [
            [ 'Bon Pesah!',  'Бон Песах!' ],
            [ 'Esther es un traduor.', 'Естхер ес ун традуор.' ],
            [ 'Layla es un traduor.', 'Лаила ес ун традуор.' ],
            [ 'Fode tu, merda.', 'Фоде ту, мерда.' ],
            [ 'Fode tu, buco de culo.', 'Фоде ту, буко де куло.' ],
            [ 'Me studia ancora franses.', 'Ме студиа анкора франсес.'],
            [ 'Esce la cortina es pal?', 'Еске ла кортина ес пал?' ],
            [ 'La tera covre la eras de la dotores medical.',
              'Ла тера ковре ла ерас де ла доторес медикал.'
            ],
            [ 'Tom es un de la "icones" santa de Tatoeba. Donce, el es nonviolable.',
              'Том ес ун де ла "иконес" санта де Татоеба. Донке, ел ес нонвиолабле.'
            ],
            [ '"Perce la esta ata? Car nos es nunca escutada," '.
              'un portavose de CRAV ia informa jornalistes.',
              '"Перке ла еста ата? Кар нос ес нунка ескутада," '.
              'ун портавосе де КРАВ иа информа жорналистес.'
            ],
            [ 'A Mina, on ia erije un estende vasta de tendas blanca '.
              'e secur contra foco, per casi plu ca 2,5 milion peregrinores.',
              'А Мина, он иа ериже ун естенде васта де тендас бланка '.
              'е секур контра фоко, пер каси плу ка 2,5 милион перегринорес.'
            ],
            [ 'La autor de la libro "La evolui – imajes de nosа jovenia", '.
              'Emile de Cooman, meа frate, es gravor e pintor.',
              'Ла аутор де ла либро "Ла еволуи – имажес де носа жовениа", '.
              'Емиле Де Кооман, меа фрате, ес гравор е пинтор.'
            ],
            [ '"Perce la esta ata? Car nos es nunca escutada," un portavose de CRAV ia informa jornalistes.',
              '"Перке ла еста ата? Кар нос ес нунка ескутада," ун портавосе де КРАВ иа информа жорналистес.'
            ],
            [ '"Me es triste," el ia dise. "Lo es un farsa grande, un menti grande, un pirotecnical grande. Lo cual aveni no depresa me, ma simple lo motiva me a apare e parla plu."',
              '«Ме ес тристе,» ел иа дисе. «Ло ес ун фарса гранде, ун менти гранде, ун пиротекникал гранде. Ло куал авени но депреса ме, ма симпле ло мотива ме а апаре е парла плу.»'
            ],
            [ 'Astronomistes ia oserva sesdes-du lunas cual orbita Saturno.',
              'Астрономистес иа осерва сесдес-ду лунас куал орбита Сатурно.'
            ],
            [ 'Chelsea es entre la distritos la plu modosa de Manhattan, e sua bares e restorantes es comun folida a finis de semana.',
              'Chelsea ес ентре ла дистритос ла плу модоса де Manhattan, е суа барес е ресторантес ес комун фолида а финис де семана.'
            ],
            [ 'Christoph Schlütermann, un laboror per la Crus Roja, ia descrive el como "tan diferente de la otras – multe noncapas de ata sin aida".',
              'Christoph Schlütermann, ун лаборор пер ла Крус Рожа, иа дескриве ел комо "тан диференте де ла отрас – мулте нонкапас де ата син аида".'
            ],
            [ 'Esperanto es un lingua cual on debe aprende, usa, recorda, parla, difusa, aseta, scribe, leje, transmete.',
              'Есперанто ес ун лингуа куал он дебе апренде, уса, рекорда, парла, дифуса, асета, скрибе, деже, трансмете.'
            ],
            [ '"Nos ia ave no tempo," Kellner ia esplica, "donce me ia core pos el per cisa 15 metres o simil. Un de meа amis es ance un polisior, donce nos ia saisi la om. El ia atenta evade, donce nos ia teni plu forte el."',
'"Нос иа аве но темпо," Кеllnеr иа есплика, "донке ме иа коре пос ел пер киса 15 метрес о симил. Ун де меа амис ес анке ун полисиор, донке нос иа саиси ла ом. Ел иа атента еваде, донке нoс иа тени плу форте ел."',
            ],
        ];
    }

    /**
     * @dataProvider lfnSentencesProvider
     */
    function testLfnTranscription($latin, $cyrillic) {
        $result = $this->AT->lfn_Latn_to_Cyrl_generate($latin, $needsReview);
        $this->assertFalse($needsReview);
        $this->assertEquals($cyrillic, $result);
    }
}
