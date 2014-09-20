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
define('CMN_PINYIN', 3);
define('CMN_OTHER_SCRIPT', 4);
define('JPN_FURIGANA', 5);
define('JPN_ROMAJI', 6);

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
                return $this->_getJapaneseRomanization($text, 'romaji');
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
     * Convert Japanese text into furigana.
     */
    private function _getFurigana($text)
    {
        $romanization = "";

        $xml = simplexml_load_file(
            "http://127.0.0.1:8842/furigana?str=".urlencode($text)
            ,'SimpleXMLElement', LIBXML_NOCDATA
        );
        foreach($xml->{'parse'}->{'furigana'} as $key=>$furigana) {
            $romanization .= $furigana->{'token'}."[".trim($furigana->{"kana"})."] ";
        }

        return trim($romanization);
    }


    /**
     * get "romanisation" of the $text sentences in japanese
     * into romaji or furigana depending of $type value
     *
     * @param string $text text to romanized
     * @param string $type type of romanization to apply
     *
     * @return string romanized japanese text
     */
    private function _getJapaneseRomanization($text, $type)
    {
        // important to add this line before escaping a
        // utf8 string, workaround for an apache/php bug
        setlocale(LC_CTYPE, "en_US.UTF-8");
        $text = escapeshellarg($text);

        $text = nl2br($text);

        $Owakati = exec(
            "export LC_ALL=en_US.UTF-8 ; ".
            "echo $text | ".
            "mecab -Owakati"
        );

        $Oyomi = exec(
            "export LC_ALL=en_US.UTF-8 ; ".
            "echo $text | ".
            "mecab -Owakati | ".
            "mecab -Oyomi"
        );

        if (empty($Oyomi)) {
            return '';
        }

        $katakana = array(
        "ァ","ア","ィ","イ","ゥ","ウ","ェ","エ","ォ","オ",
        "カ","ガ","キ","ギ","ク","グ","ケ","ゲ","コ","ゴ",
        "サ","ザ","シ","ジ","ス","ズ","セ","ゼ","ソ","ゾ",
        "タ","ダ","チ","ヂ","ッ","ツ","ヅ","テ","デ","ト",
        "ド","ナ","ニ","ヌ","ネ","ノ","ハ","バ","パ","ヒ",
        "ビ","ピ","フ","ブ","プ","ヘ","ベ","ペ","ホ","ボ",
        "ポ","マ","ミ","ム","メ","モ","ャ","ヤ","ュ","ユ",
        "ョ","ヨ","ラ","リ","ル","レ","ロ","ヮ","ワ","ヲ",
        "ン","ヴ","ヵ","ヶ",
        "。","、","？","！","「","」","・"
        );

        $hiragana = array(
        "ぁ","あ","ぃ","い","ぅ","う","ぇ","え","ぉ","お",
        "か","が","き","ぎ","く","ぐ","け","げ","こ","ご",
        "さ","ざ","し","じ","す","ず","せ","ぜ","そ","ぞ",
        "た","だ","ち","ぢ","っ","つ","づ","て","で","と",
        "ど","な","に","ぬ","ね","の","は","ば","ぱ","ひ",
        "び","ぴ","ふ","ぶ","ぷ","へ","べ","ぺ","ほ","ぼ",
        "ぽ","ま","み","む","め","も","ゃ","や","ゅ","ゆ",
        "ょ","よ","ら","り","る","れ","ろ","ゎ","わ","を",
        "ん","ゔ","ゕ","ゖ",
        "。","、","？","！","「","」","・"
        );

        $kata = array(
        "キャ","キュ","キョ","ギャ","ギュ","ギョ","シャ",
        "シュ","ショ","ジャ","ジュ","ジョ","チャ","チュ",
        "チョ","ニャ","ニュ","ニョ","ヒャ","ヒュ","ヒョ",
        "ビャ","ビュ","ビョ","ピャ","ピュ","ピョ","ミャ",
        "ミュ","ミョ","リャ","リュ","リョ",

        "ウィ","ウェ","ウォ","ヴァ","ヴィ","ヴ","ヴェ",
        "ヴォ","シェ","ジェ","チェ","ツァ","ツィ","ツェ",
        "ツォ","デュ","ティ","トゥ","テュ","ディ","ドゥ",
        "ファ","フィ","フェ","フォ","フュ",

        "ァ","ア","ィ","イ","ゥ","ウ","ェ","エ","ォ","オ",
        "カ","ガ","キ","ギ","ク","グ","ケ","ゲ","コ","ゴ",
        "サ","ザ","シ","ジ","ス","ズ","セ","ゼ","ソ","ゾ",
        "タ","ダ","チ","ヂ","ッ","ツ","ヅ","テ","デ","ト",
        "ド","ナ","ニ","ヌ","ネ","ノ","ハ","バ","パ","ヒ",
        "ビ","ピ","フ","ブ","プ","ヘ","ベ","ペ","ホ","ボ",
        "ポ","マ","ミ","ム","メ","モ","ャ","ヤ","ュ","ユ",
        "ョ","ヨ","ラ","リ","ル","レ","ロ","ヮ","ワ","ヲ",
        "ン","ヴ","ヵ","ヶ",

        "。","、","？","！","「","」","・"
        );

        $romanji = array(
        "kya","kyu","kyo","gya","gyu","gyo","sha","shu","sho",
        "ja","ju","jo","cha","chu","cho","nya","nyu","nyo",
        "hya","hyu","hyo","bya","byu","byo","pya","pyu","pyo",
        "mya","myu","myo","rya","ryu","ryo",

        "wi","we","wo","va","vi","vu","vr","vo","she","je",
        "che","tsa","tsi","tse","tso","dyu","ti","tu","tyu","di",
        "du","fa","fi","fe","fo","fyu",

        "a","a","i","i","u","u","e","e","o","o",
        "ka","ga","ki","gi","ku","gu","ke","ge","ko","go",
        "sa","za","shi","ji","su","zu","se","ze","so","zo",
        "ta","da","chi","ji","","tsu","zu","te","de","to",
        "do","na","ni","nu","ne","no","ha","ba","pa","hi",
        "bi","pi","fu","bu","pu","he","be","pe","ho","bo",
        "po","ma","mi","mu","me","mo","ya","ya","yu","yu",
        "yo","yo","ra","ri","ru","re","ro","wa","wa","wo",
        "n","","","",

        ".",", ","?","!","\"","\"","."
        );

        $Owakati = explode(' ', $Owakati);
        $Oyomi = explode(' ', $Oyomi);
        $romanization = array();

        if ($type == 'furigana') {
            foreach ($Owakati as $i=>$word) {
                preg_match_all('/./u', $word, $char);
                if (in_array($char[0][0], $katakana)) {
                    array_push($romanization, $word);
                } else {
                    array_push(
                        $romanization,
                        str_replace($katakana, $hiragana, $Oyomi[$i])
                    );
                }
            }
        } elseif ($type == 'mix') {
            foreach ($Owakati as $i=>$word) {
                preg_match_all('/./u', $word, $chars);
                $char = $chars[0][0];
                if (in_array($char, $katakana) || in_array($char, $hiragana)) {
                    array_push(
                        $romanization,
                        $word
                    );
                } else {
                    $translatedWord = str_replace($katakana, $hiragana, $Oyomi[$i]);
                    array_push(
                        $romanization,
                        $word."[$translatedWord]"
                    );
                }
            }
        } elseif ($type == 'romaji') {
            $kata = str_replace($katakana, $hiragana, $kata); // Temporary fix to make jumandic-based mecab work.
            foreach ($Owakati as $i=>$word) {
                array_push(
                    $romanization,
                    str_replace($kata, $romanji, $Oyomi[$i])
                );
            }
        } else {
            $romanization = array();
        }

        return implode(" ", $romanization);
    }

}