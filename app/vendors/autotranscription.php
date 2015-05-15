<?php
/**
 * Tatoeba Project, free collaborative creation of languages corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang (tranglich@gmail.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link *   http://tatoeba.org
 */

/**
 * Transcription/transliteration tools.
 *
 * To install these tools: https://github.com/Tatoeba/admin
 * Some script can also be found in /docs/daemons.
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link *   http://tatoeba.org
 */


define('UZB_SCRIPT_SWITCH', 0);
define('UZB_SCRIPT_CYRYLLIC', 1);
define('UZB_SCRIPT_LATIN', 2);
define('CMN_PINYIN', 10);
define('CMN_OTHER_SCRIPT', 11);
define('CMN_SCRIPT', 12);
define('JPN_FURIGANA', 20);
define('JPN_ROMAJI', 21);

class Autotranscription
{
    var $availableLanguages = array(
        'cmn', 'jpn', 'kat', 'uzb', 'wuu', 'yue'
    );

    
    // ------------------------------------------------
    //
    //  public
    //
    // ------------------------------------------------

    public function cmn($text, $type = null)
    {
        switch ($type) {
            case CMN_OTHER_SCRIPT:
                return $this->_getChineseOtherScriptVersion($text);

            case CMN_SCRIPT:
                return $this->_getChineseScript($text);

            default:
                return $this->_getPinyin($text);
                
        }
    }

    public function jpn($text, $type = null)
    {
        switch ($type) {
            case JPN_FURIGANA:
                return $this->_getFurigana($text);
                
            default:
                return $this->tokenizedJapaneseWithReadingsToRomaji($text);
        }
    }

    public function kat($text)
    {
        return $this->_getGeorgianRomanization($text);
    }

    public function uzb($text)
    {
        return $this->_uzbekScriptChange($text);
    }

    public function wuu($text)
    {
        return $this->_getShanghaineseRomanization($text);
    }

    public function yue($text)
    {
        return $this->_getJyutping($text);
    }



    // ------------------------------------------------
    //
    //  private
    //
    // ------------------------------------------------

    /**
     * Return IPA of a shanghainese text
     *
     * @param string $shanghaineseText text in shanghainese
     *
     * @return string
     */
    private function _getShanghaineseRomanization($shanghaineseText)
    {
        $ipaFile = fopen(
            "http://static.tatoeba.org/data/shanghainese2IPA2.txt",
            "r"
        );

        $ipaArray = array();
        $sinogramsArray = array();

        // the file is tab separated value
        // we create two array one with characters, the other
        // with the IPA
        while ($line = fgets($ipaFile)) {
            $arrayLine = explode("\t", $line);
            // there's some blank line in this file so mustn't
            // handle them
            if (count($arrayLine) > 1) {
                array_push($ipaArray, str_replace("\n", ". ", $arrayLine[1]));
                array_push($sinogramsArray, $arrayLine[0]);
            }
        }

        $ipaSentence = str_replace($sinogramsArray, $ipaArray, $shanghaineseText);
        return $ipaSentence;

    }


    /**
     * Return IPA of a Georgian text
     *
     * @param string $text text in Georgian
     *
     * @return string
     */
    private function _getGeorgianRomanization($text) {
        //a - b - g - d - e - v - z - t - i - k - l - m - n -
        // o - p - dj - r - s - t - u - p - q - gh - kh - sh -
        //ch - ts - dz - ts - tch- x - j - h -

        $ipaArray = array(
            'a', 'b', 'g', 'd', 'e',
            'v', 'z', 'tʰ', 'i', 'k’',
            'l', 'm', 'n', 'o', 'p’',
            'ʒ' , 'r', 's', 't’', 'u',
            'pʰ', 'q', 'gh', 'kh', 'ʃ',
            'ch', 'ʦ', 'dz', 'ts', 'tch',
            'x', 'ʤ', 'h',
        );

        $alphabetArray = array(
            'ა', 'ბ', 'გ', 'დ', 'ე',
            'ვ', 'ზ', 'თ', 'ი', 'კ',
            'ლ', 'მ', 'ნ', 'ო', 'პ',
            'ჟ', 'რ', 'ს', 'ტ', 'უ',
            'ფ', 'ქ', 'ღ', 'ყ', 'შ',
            'ჩ', 'ც', 'ძ', 'წ', 'ჭ',
            'ხ', 'ჯ', 'ჰ',
        );

        $ipaSentence = str_replace($alphabetArray, $ipaArray, $text);
        return $ipaSentence;
    }


    /**
     * Uzbek sctript-switching functions
     * © 2010, Dmitry Kushnariov. Distributed under the BSD license
     *
     * Finds a script of Uzbek text
     * $str - an UTF-8 string of Uzbek text
     * Returns 1 for Cyrillic, 2 for Latin, FALSE on Error
     */
    private function _uzbekScriptGet($str) {
        if (empty($str)) {
            return FALSE;
        }

        $needles = array(
            '‘', '’', '.', ',', ';',
            ':', '1', '2', '3', '4',
            '5', '6', '7', '8', '9',
            '0', ' ', '-', '«', '»',
            '—'
        );
        $replacements = array(
            "'", "'", '', '', '',
            '', '', '', '', '',
            '', '', '', '', '',
            '',  '',  '', '', '',
            ''
        );
        $sentence = str_replace($needles, $replacements, $str);

        $cyr = 0;
        $lat = 0;
        for ($i = 0; $i < strlen($sentence); $i++) {
            if (ord($sentence[$i]) < 128) {
                $lat += 2;
            } else {
                $cyr += 1;
            }
        }
        return ($cyr >= $lat) ? 1 : 2;
    }

    /**
     * Changes a script of Uzbek text
     * $str - an UTF-8 string of Uzbek text
     * $script - 1 for Cyrillic, 2 for Latin, 0 to switch
     * Returns a string of FALSE on error
     */
    private function _uzbekScriptChange($str, $script = UZB_SCRIPT_SWITCH) {
        $scriptArray = array(
            UZB_SCRIPT_SWITCH,
            UZB_SCRIPT_CYRYLLIC,
            UZB_SCRIPT_LATIN
        );

        if (empty($str) || !in_array($script, $scriptArray)) {
            return FALSE;
        }

        $new_script = $script;

        if ($script == UZB_SCRIPT_SWITCH) {
            $new_script = ($this->_uzbekScriptGet($str) == UZB_SCRIPT_CYRYLLIC ) ? UZB_SCRIPT_LATIN : UZB_SCRIPT_CYRYLLIC;
        }

        if ($new_script == UZB_SCRIPT_CYRYLLIC) {//change to Cyrillic

            $needles = array(
                '‘', '’', "s'h", "S'h", "S'H",
                "O'", "o'", "G'", "g'", 'SH',
                'Sh', 'sh', 'CH', 'Ch', 'ch',
                'YO', 'Yo', 'yo', ' E', ' e',
                '-E', '-e', 'Ye', 'YE', 'ye',
                'e', 'E', 'YA', 'Ya', 'ya',
                'YU', 'Yu', 'yu', 'A', 'a',
                'B', 'b', 'D', 'd', 'F',
                'f', 'G', 'g', 'H', 'h',
                'I', 'i', 'J', 'j', 'K',
                'k', 'L', 'l', 'M', 'm',
                'N', 'n', 'O', 'o', 'P',
                'p', 'Q', 'q', 'R', 'r',
                'S', 's', 'T', 't', 'U',
                'u', 'V', 'v', 'X', 'x',
                'Y', 'y', 'Z', 'z', "'",
                'ТС', 'тс', 'Тс', 'циз', 'ЦИЗ',
                'сирк', 'Сирк'
            );
            $replacements = array(
                "'", "'", 'сҳ',  'Сҳ',  "СҲ",
                'Ў',  'ў',  'Ғ',  'ғ',  'Ш',
                'Ш',  'ш',  'Ч',  'Ч',  'ч',
                'Ё',  'Ё',  'ё',  ' Э', ' э',
                '-Э', '-э', 'Е',  'Е',  'е',
                'е', 'Е', 'Я',  'Я',  'я',
                'Ю',  'Ю',  'ю',  'А', 'а',
                'Б', 'б', 'Д', 'д', 'Ф',
                'ф', 'Г', 'г', 'Ҳ', 'ҳ',
                'И', 'и', 'Ж', 'ж', 'К',
                'к', 'Л', 'л', 'М', 'м',
                'Н', 'н', 'О', 'о', 'П',
                'п', 'Қ', 'қ', 'Р', 'р',
                'С', 'с', 'Т', 'т', 'У',
                'у', 'В', 'в', 'Х', 'х',
                'Й', 'й', 'З', 'з', 'ъ',
                'Ц',  'ц',  'ц',  'тсиз', 'ТСИЗ',
                'цирк', 'Цирк'
            );

        } else {//change to Latin
            $needles =  array(
                'ац',  'ец',  'иц',  'оц',  'уц',
                'ўц',   'эц',  'АЦ',  'ЕЦ',  'ИЦ',
                'ОЦ',  'УЦ',  'ЎЦ',   'Ац',  'Ец',
                'Ии',  'Оц',  'Уц',  'Ўц',   'ц',
                'Ц', ' Е',  ' е',  '-Е', '-е',
                'е', 'А', 'а', 'Б', 'б',
                'В', 'в', 'Г', 'г', 'Ғ',
                'ғ', 'Д', 'д', 'Ё', 'ё',
                'Ж', 'ж', 'З', 'з', 'И',
                'и', 'Й', 'й', 'К', 'к',
                'Қ', 'қ', 'Л', 'л', 'М',
                'м', 'Н', 'н', 'О', 'о',
                'П', 'п', 'Р', 'р', 'С',
                'с', 'Т', 'т', 'У', 'у',
                'Ў', 'ў', 'Ф', 'ф', 'Х',
                'х', 'Ҳ', 'ҳ', 'Ч', 'ч',
                'Ш', 'ш', 'Ъ', 'ъ', 'Ы',
                'ы', 'Ь', 'ь', 'Э', 'э',
                'Ю',  'ю',  'Я',  'я'
            );
            $replacements = array(
                'ats', 'еts', 'its', 'ots', 'uts',
                'o‘ts', 'эts', 'ATS', 'ЕTS', 'ITS',
                'OTS', 'UTS', 'O‘TS', 'Ats', 'Еts',
                'Its', 'Ots', 'Uts', 'O‘ts', 's',
                'S', ' Ye', ' ye', '-Ye', '-ye',
                'e', 'A', 'a', 'B', 'b',
                'V', 'v', 'G', 'g', 'G‘',
                'g‘', 'D', 'd', 'Yo', 'yo',
                'J', 'j', 'Z', 'z', 'I',
                'i', 'Y', 'y', 'K', 'k',
                'Q', 'q', 'L', 'l', 'M',
                'm', 'N', 'n', 'O', 'o',
                'P', 'p', 'R', 'r', 'S',
                's', 'T', 't', 'U', 'u',
                'O‘','o‘', 'F', 'f', 'X',
                'x', 'H', 'h', 'Ch', 'ch',
                'Sh', 'sh', '’', '’', 'I',
                'i', '', '', 'E', 'e',
                'Yu', 'yu', 'Ya', 'ya'
            );
        }
        return str_replace($needles, $replacements, $str);
    }


    /**
     *
     */
    private function _getPinyin($text)
    {
        $xml = simplexml_load_file(
            "http://127.0.0.1:8042/pinyin?str=".urlencode($text)
            ,'SimpleXMLElement', LIBXML_NOCDATA
        );
        foreach($xml as $key=>$value) {
            return $value;
        }
    }


    /**
     *
     */
    private function _getJyutping($text)
    {
        $xml = simplexml_load_file(
            "http://127.0.0.1:8042/jyutping?str=".urlencode($text)
            ,'SimpleXMLElement', LIBXML_NOCDATA
        );
        foreach($xml as $key=>$value) {
            return $value;
        }
    }


    /**
     * convert a chinese text from traditional to simplified
     * and vice versa
     *
     * @param string $chineseText chinese text to switch
     *
     * @return string
     */
    private function _getChineseOtherScriptVersion($chineseText)
    {
        $xml = simplexml_load_file(
            "http://127.0.0.1:8042/change_script?str=".urlencode($chineseText)
            ,'SimpleXMLElement', LIBXML_NOCDATA
        );
        foreach($xml as $key=>$value) {
            return $value;
        }
        return "";
    }


    /**
     * detect the script of the given Chinese text
     *
     * @param string $chineseText Chinese text
     *
     * @return string
     */
    private function _getChineseScript($chineseText)
    {
        $xml = simplexml_load_file(
            "http://127.0.0.1:8042/guess_script?str=".urlencode($chineseText)
            ,'SimpleXMLElement', LIBXML_NOCDATA
        );
        foreach($xml as $key=>$value) {
            return (string)$value;
        }
        return "";
    }

    private function firstElement($node) {
        foreach ($node->childNodes as $subNode) {
            if ($subNode->nodeType == XML_ELEMENT_NODE) {
                return $subNode;
            }
        }
        return $node;
    }

    /**
     * Convert Japanese text into furigana.
     */
    public function jpn_Jpan_to_Hrkt_generate($text)
    {
        $romanization = '';
        // Use DOMDocument since SimpleXML can't handle mixed content
        $xml = DOMDocument::load("http://127.0.0.1:8842/furigana?str=".urlencode($text), LIBXML_NOBLANKS|LIBXML_NOCDATA);
        $parse = $xml->firstChild->firstChild;
        foreach ($parse->childNodes as $token) {
            foreach ($token->childNodes as $reading) {
                $text = $reading->nodeValue;
                if ($reading->hasChildNodes()) {
                    $furigana = $reading->getAttribute('furigana');
                    $romanization .= "[$text|$furigana]";
                } else {
                    $romanization .= $text;
                }
            }
            $romanization .= ' ';
        }

        return trim($romanization);
    }


    /**
     * Transforms Japanese text with readings into romaji.
     * Readings must be formatted like [kanji|reading].
     * $text should already be tokenized with spaces.
     *
     * @param string $text text to romanized
     *
     * @return string romanized japanese text
     */
    public function jpn_Hrkt_to_Latn_generate($text)
    {
        $particles = array(
            'は' => 'wa',
            'へ' => 'e',
        );

        $mb_regex_encoding = mb_regex_encoding();
        mb_regex_encoding('UTF-8');

        $text = mb_ereg_replace(' ([？！。…・＝」、ー〜])', '\\1', $text);
        $text = mb_ereg_replace('([「・＝〜]) ', '\\1', $text);
        $text = mb_ereg_replace('(。)(…)', '\\1 \\2', $text);
        $text = preg_replace('/\[[^|]*\|([^\]]*)\]/', '|$1|', $text);

        $romajized = array();
        $words = explode(' ', $text);
        foreach ($words as $word) {
            if (isset($particles[$word])) {
                $romajiWord = $particles[$word];
            } else {
                $readings = explode('|', $word);
                $readings = array_map(array($this, '_toRomaji'), $readings);
                $romajiWord = implode('', $readings);
            }
            array_push($romajized, $romajiWord);
        }
        $romaji = implode(' ', $romajized);

        $longVowelMark = array(
            'aー' => 'ā', 'iー' => 'ī', 'uー' => 'ū',
            'eー' => 'ē', 'oー' => 'ō'
        );
        $romaji = str_replace(array_keys($longVowelMark), array_values($longVowelMark), $romaji);

        $tsu = 'っ';
        $romaji = mb_ereg_replace("$tsu+ch", 'tch', $romaji);
        $romaji = mb_ereg_replace("$tsu+([a-z])", '\\1\\1', $romaji);
        $romaji = mb_ereg_replace("$tsu,", ',', $romaji);
        $romaji = mb_ereg_replace($tsu, '…', $romaji);

        mb_regex_encoding($mb_regex_encoding);
        return $romaji;
    }

    private function _toRomaji($word) {
        $katakana = array(
            'ァ','ア','ィ','イ','ゥ','ウ','ェ','エ','ォ','オ',
            'カ','ガ','キ','ギ','ク','グ','ケ','ゲ','コ','ゴ',
            'サ','ザ','シ','ジ','ス','ズ','セ','ゼ','ソ','ゾ',
            'タ','ダ','チ','ヂ','ッ','ツ','ヅ','テ','デ','ト',
            'ド','ナ','ニ','ヌ','ネ','ノ','ハ','バ','パ','ヒ',
            'ビ','ピ','フ','ブ','プ','ヘ','ベ','ペ','ホ','ボ',
            'ポ','マ','ミ','ム','メ','モ','ャ','ヤ','ュ','ユ',
            'ョ','ヨ','ラ','リ','ル','レ','ロ','ヮ','ワ','ヲ',
            'ン','ヴ','ヵ','ヶ',
        );

        $hiragana = array(
            'ぁ','あ','ぃ','い','ぅ','う','ぇ','え','ぉ','お',
            'か','が','き','ぎ','く','ぐ','け','げ','こ','ご',
            'さ','ざ','し','じ','す','ず','せ','ぜ','そ','ぞ',
            'た','だ','ち','ぢ','っ','つ','づ','て','で','と',
            'ど','な','に','ぬ','ね','の','は','ば','ぱ','ひ',
            'び','ぴ','ふ','ぶ','ぷ','へ','べ','ぺ','ほ','ぼ',
            'ぽ','ま','み','む','め','も','ゃ','や','ゅ','ゆ',
            'ょ','よ','ら','り','る','れ','ろ','ゎ','わ','を',
            'ん','ゔ','ゕ','ゖ',
        );

        $kana2romaji = array(
            'きゃ' => 'kya', 'きゅ' => 'kyu', 'きょ' => 'kyo',
            'ぎゃ' => 'gya', 'ぎゅ' => 'gyu', 'ぎょ' => 'gyo',    
            'しゃ' => 'sha', 'しゅ' => 'shu', 'しょ' => 'sho',
        
            'じゃ' => 'ja',  'じゅ' => 'ju',  'じょ' => 'jo',
            'ちゃ' => 'cha', 'ちゅ' => 'chu', 'ちょ' => 'cho',
            'にゃ' => 'nya', 'にゅ' => 'nyu', 'にょ' => 'nyo',
            'ひゃ' => 'hya', 'ひゅ' => 'hyu', 'ひょ' => 'hyo',
            'びゃ' => 'bya', 'びゅ' => 'byu', 'びょ' => 'byo',
            'ぴゃ' => 'pya', 'ぴゅ' => 'pyu', 'ぴょ' => 'pyo',
            'みゃ' => 'mya', 'みゅ' => 'myu', 'みょ' => 'myo',
            'りゃ' => 'rya', 'りゅ' => 'ryu', 'りょ' => 'ryo',
            'うぃ' => 'wi',  'うぇ' => 'we',  'うぉ' => 'wo',
            'ゔぁ' => 'va',  'ゔぃ' => 'vi',  'ゔ' => '',
            'ゔぇ' => 'vr',  'ゔぉ' => 'vo',
            'しぇ' => 'she', 'じぇ' => 'je',  'ちぇ' => 'che',
            'つぁ' => 'tsa', 'つぃ' => 'tsi', 'つぇ' => 'tse',
            'つぉ' => 'tso', 'てぃ' => 'ti',  'でゅ' => 'dyu',
            'とぅ' => 'tu',  'てゅ' => 'tyu', 'でぃ' => 'di',
            'どぅ' => 'du',  'ふぁ' => 'fa',  'ふぃ' => 'fi',
            'ふぇ' => 'fe',  'ふぉ' => 'fo',  'ふゅ' => 'fyu',
        
            'ぁ' => 'a',  'あ' => 'a',  'ぃ' => 'i',  'い' => 'i',  'ぅ' => 'u',
            'う' => 'u',  'ぇ' => 'e',  'え' => 'e',  'ぉ' => 'o',  'お' => 'o',
            'か' => 'ka', 'が' => 'ga', 'き' => 'ki', 'ぎ' => 'gi', 'く' => 'ku', 
            'ぐ' => 'gu', 'け' => 'ke', 'げ' => 'ge', 'こ' => 'ko', 'ご' => 'go',
            'さ' => 'sa', 'ざ' => 'za', 'し' => 'shi', 'じ' => 'ji', 'す' => 'su',
            'ず' => 'zu', 'せ' => 'se', 'ぜ' => 'ze', 'そ' => 'so', 'ぞ' => 'zo',
            'た' => 'ta', 'だ' => 'da', 'ち' => 'chi', 'ぢ' => 'ji', 'つ' => 'tsu',
            'づ' => 'zu', 'て' => 'te', 'で' => 'de', 'と' => 'to', 'ど' => 'do',
            'な' => 'na', 'に' => 'ni', 'ぬ' => 'nu', 'ね' => 'ne', 'の' => 'no',
            'は' => 'ha', 'ば' => 'ba', 'ぱ' => 'pa', 'ひ' => 'hi', 'び' => 'bi',
            'ぴ' => 'pi', 'ふ' => 'fu', 'ぶ' => 'bu', 'ぷ' => 'pu', 'へ' => 'he',
            'べ' => 'be', 'ぺ' => 'pe', 'ほ' => 'ho', 'ぼ' => 'bo', 'ぽ' => 'po',
            'ま' => 'ma', 'み' => 'mi', 'む' => 'mu', 'め' => 'me', 'も' => 'mo',
            'ゃ' => 'ya', 'や' => 'ya', 'ゅ' => 'yu',
            'ゆ' => 'yu', 'ょ' => 'yo', 'よ' => 'yo',
            'ら' => 'ra', 'り' => 'ri', 'る' => 'ru', 'れ' => 're', 'ろ' => 'ro',
            'ゎ' => 'wa', 'わ' => 'wa', 'を' => 'o',  'ん' => 'n',
        
            'ゕ' => '', 'ゖ' => '',
            '。' => '.', '、' => ',', '？' => '?', '！' => '!', '「' => '"',
            '」' => '"', '・' => ' ', '＝' => '-', '〜' => '~'
        );

        $longVowels = array(
            'おお'   => 'ō',   'おう'   => 'ō',

            'きゅう' => 'kyū', 'ぎゅう' => 'gyū',
            'きょう' => 'kyō', 'ぎょう' => 'gyō',
            'かあ'   => 'kā',
            'くう'   => 'kū',  'ぐう'   => 'gū',
            'こお'   => 'kō',
            'こう'   => 'kō',  'ごう'   => 'gō',

            'しゅう' => 'shū', 'じゅう' => 'jū',
            'しょう' => 'shō', 'じょう' => 'jō',
            'すう'   => 'sū',  'ずう'   => 'zū',
            'そう'   => 'sō',  'ぞう'   => 'zō',

            'ちゅう' => 'chū', 'ぢゅう' => 'jū',
            'ちょう' => 'chō', 'ぢょう' => 'jō',
            'とお'   => 'tō',  'どお'   => 'dō',
            'とう'   => 'tō',  'どう'   => 'dō',

            'にゅう' => 'nyū',
            'にょう' => 'nyō',
            'ねえ'   => 'nē',
            'のう'   => 'nō',
            'のお'   => 'nō',

            'ひゅう' => 'hyū', 'びゅう' => 'byū', 'ぴゅう' => 'pyū',
            'ひょう' => 'hyō', 'びょう' => 'byō', 'ぴょう' => 'pyō',
                               'ばあ'   => 'bā',
            'ほう'   => 'hō',  'ぼう'   => 'bō',  'ぽう'   => 'pō',
            'ほお'   => 'hō',

            'みゅう' => 'myū',
            'みょう' => 'myō',
            'まあ'   => 'mā',
            'もう'   => 'mō',

            'ゆう'   => 'yū',
            'よう'   => 'yō',

            'りゅう' => 'ryū',
            'りょう' => 'ryō',
            'ろう'   => 'rō',
        );

        $specials = array(
            'みずうみ' => 'mizuumi',
        );

        if (isset($specials[$word])) {
            $word = $specials[$word];
        } else {
            $word = mb_ereg_replace_callback(
                '([よねぞなぜえおオ])(ー+)',
                function ($matches) use ($kana2romaji, $katakana, $hiragana) {
                    $kana = str_replace($katakana, $hiragana, $matches[1]);
                    $syl = str_replace(array_keys($kana2romaji), array_values($kana2romaji), $kana);
                    $intonations = mb_strlen($matches[2]);
                    $vowel = substr($syl, -1);
                    while ($intonations--)
                        $syl = $syl.$vowel;
                    return $syl;
                },
                $word
            );

            $word = str_replace($katakana, $hiragana, $word);
            $word = mb_ereg_replace('ん([あいうえおやゆよ])', 'n\'\\1', $word);
            $word = str_replace(array_keys($longVowels), array_values($longVowels), $word);
            $word = str_replace(array_keys($kana2romaji), array_values($kana2romaji), $word);
        }
        return $word;
    }

    public function jpn_Jpan_to_Hrkt_validate($sentenceText, $transcr) {
        $transcr = str_replace(' ', '', $transcr);
        $withoutFuri = preg_replace('/\[([^|]+)\|([\p{Hiragana}ー]+)\]/u', '$1', $transcr);
        if ($sentenceText !== $withoutFuri)
            return false;

        $withFuri = preg_replace('/\[([^|]+)\|([\p{Hiragana}ー]+)\]/u', '$2', $transcr);
        if (preg_match("/[\p{Han}]/u", $withFuri))
            return false;

        return true;
    }

    public function cmn_detectScript($text) {
    }
}
