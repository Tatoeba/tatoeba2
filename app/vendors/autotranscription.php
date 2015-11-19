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


	// some Unicode routines for parsing / constructing Ge'ez chars:
	//  Oyd11,  2014 , MIT license , based on JS code :: http://jsfiddle.net/oyd11/Z5Wws/
	
// Concider moving to utf8 lib:
 // 	http://pageconfig.com/post/portable-utf8
// (if tatoeba stays on php)	
	/**
	* Helper function: unicode safe version of 'str_split'
	* Since: str_split - does not handle Unicode arrays:
	 * str_split_unicode
	 * 
     * @param string $unicode_str
     *
     * @return array of unicode chars
	 *
	 * from http://php.net/manual/en/function.mb-split.php
	 */
	  function unicode_str_split($unicode_str) {
		 return preg_split('//u', $unicode_str, null, PREG_SPLIT_NO_EMPTY); 
	}
	
 /**
 * @param string $unicode_str - a one-character string
 *
 * @return unicode representation as a number
	*/
	function unicode_str_ord($unicode_str) {	
		$le_str = mb_convert_encoding($unicode_str, "UTF-32LE");	
		$le_arr = unpack('C*', $le_str);
		$acc = 0;
		$factor = 1;
		foreach ($le_arr as $b) {
			$acc += $b*$factor;
			$factor <<= 8;
		}
		return $acc;
	}
	
     function unicode_chr_to_utf8($num) {
		$ret = iconv('UCS-4LE', 'UTF-8', pack('V', $num));
		return $ret;
	}
	
    $geezVowels = array( 'ä' , 'u' , 'i' ,'a' ,'e' , "" ,'o' , 'wa' );
	
 $geezConsBase = array(
		'ሀ' => 'h',		'ለ' => 'l',	'ሐ' => 'ħ',		'መ' => 'm',
		'ሠ' => 'ś',		'ረ' => 'r',	'ሰ' => 's',		'ሸ' =>'š',
		'ቀ' => 'q', 	'ቈ' => 'qʷ',	'ቐ' => 'Q',	'ቘ' => 'Qʷ',
		'በ' => 'b',		'ቨ' => 'v',		'ተ' => 't',		'ቸ' => 'č',
		'ኀ' => 'ḫ',		'ኈ' => 'ḫʷ',	'ነ' => 'n',		'ኘ' => 'ñ',
		'አ' => '‘',		'ከ' => 'k',	'ኰ' => 'kʷ',		'ኸ' => 'K',
		'ዀ' => 'kʷ',		'ወ' => 'w',	'ዐ' => 'ʕ',		'ዘ' => 'z',
		'ዠ' => 'ž',		'የ' => 'y',	'ደ' =>'d',		'ዸ' => 'D',
		'ጀ' =>'ǧ',		'ገ' =>'g',	'ጐ' =>'gʷ',		'ጘ' =>'ŋ',
		'ጠ' =>'T',		'ጨ' =>'Č',	'ጰ' =>'P',		'ጸ' =>'ṣ',
		'ፀ' =>'ṩ',		'ፈ' =>'f',	'ፐ' =>'p'
	);
	
	
 $geezPuncTable = array(
    'ፘ' => 'RYA', 	'ፙ' => 'MYA' , 'ፚ' => 'FYA' ,
    '፠' => '/section/',
    '፡' => ' ',
    '።'	=> '.',
    '፣' => ',',
    '፤' => ';',
    '፥' => ':' ,	'፦' => '<' ,	'፧' => '?' ,	'፨' => '/parag/',	
    '፩' =>'1',	'፪' => '2',	'፫' => '3', 	'፬' => '4' , 	'፭' => '5',
    '፮' => '6', 	'፯' => '7', '፰' => '8'	, '፱' =>'9' , 	'፲'=>'10+',
    '፳' =>'20+', 	'፴'=>'30',	'፵'=>'40'	,'፶'=>'50',	'፷'=>'60',
    '፸' => '70'	,'፹'=>'80' ,
    '፺' => '90',	'፻'=> '100',	'፼'=>'1000'	
	);
	

/**  unicode 6.3 Ethiopic::
 * http://www.unicode.org/charts/PDF/U1200.pdf
 */
 function isGeezCharCode($num) {
		return ($num >= 0x1200) && ($num < 0x1380);
	}


 function isGeezPunc($num) {
		return ($num >= 0x1358) && ($num < 0x1380);
	}


	   /**
     * Return Phonetic-transcription of a Ge'ez script-text
     * Covered languages: Tigrinya (tir), Tigre (tig), Amharic (amh), Ge'ez (gez)
	 * TODO: Missing Blin (byn) letters
     * @param string $text text in Ge'ez
     *
     * @return string
     */
 function geezNum2Latin($num) {
		 global $geezConsBase, $geezVowels;
		$row = $num & 0x07;
		$consonantNum = $num & (~0x07);
		$v = $geezVowels[$row];
		$conAsChar = unicode_chr_to_utf8( $consonantNum );
		$c = $geezConsBase[$conAsChar];
		return "$c$v";
		}

		


class Autotranscription
{
    var $availableLanguages = array(
        'cmn', 'jpn', 'kat',
	'tig', 'tir', 'gez', 'amh',
	'uzb', 'wuu', 'yue'
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
                return $this->_getJapaneseRomanization($text, 'romaji');
        }
    }

    public function kat($text)
    {
        return $this->_getGeorgianRomanization($text);
    }

    // Ge'ez::
    public function amh($text)
    {
        return $this->_getGeezRomanization($text);
    }
    public function gez($text)
    {
        return $this->_getGeezRomanization($text);
    }
    public function tig($text)
    {
        return $this->_getGeezRomanization($text);
    }
    public function tir($text)
    {
        return $this->_getGeezRomanization($text);
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
       # Changing table :: Oyd11::
   /**
     * transforming to a consistant translitaration with Wiktionary/Wikipedia:
     * https://en.wiktionary.org/wiki/Appendix:Georgian_script
     * https://en.wikipedia.org/wiki/Help:IPA_for_Georgian
     * which is consistant with ISO-9984, 2010 revision(2.0)
     * http://www.translitteration.com/transliteration/en/georgian/iso-9984/
     * 
     * ISO-9984 2010, has apparently made these decisions:
     *   + One latin letter per georgian letter (apart from extra apostrophes )
     *  + Apostrophe ’ - mark Aspirated consonants, Ejectives are unmarked
     *  + The only extended characters are latin letters with Caron(haček)
     *  + With the exeption with "g macron"
     *  + No capitilization (apparently)
     *  - Both as it doesn't appear in the original script
     *  - and might confuse people since common "web" translitarations
     *   transribe the Caron chars as capitals instead. (eg S for š)
     *  - (finially!) No Digraphs
     */
        $ipaArray = array(
            'a', 'b', 'g', 'd', 'e',
            'v', 'z', 't’', 'i', 'k',
            'l', 'm', 'n', 'o', 'p',
            'ž' , 'r', 's', 't’', 'u',
            'p’', 'k’', 'ḡ', 'q', 'š',
            'č’', 'c’', 'j', 'c’', 'č',
            'x', 'ǰ', 'h','w','ō', 'f'
        );

        $alphabetArray = array(
            'ა', 'ბ', 'გ', 'დ', 'ე',
            'ვ', 'ზ', 'თ', 'ი', 'კ',
            'ლ', 'მ', 'ნ', 'ო', 'პ',
            'ჟ', 'რ', 'ს', 'ტ', 'უ',
            'ფ', 'ქ', 'ღ', 'ყ', 'შ',
            'ჩ', 'ც', 'ძ', 'წ', 'ჭ',
            'ხ', 'ჯ', 'ჰ','ჳ', 'ჵ', 'ჶ'
        );
		
        $ipaSentence = str_replace($alphabetArray, $ipaArray, $text);
        return $ipaSentence;
    }


  /**
  * as Ge'ez is an  abugida (syllable alphabet)
  *  eg: Ge'ez :     ሀ    ሁ  ሂ   ሃ   ሄ   ህ ሆ   ሇ
  *  reads :            hä hu hi ha he h ho hwa
  * encoded as unicode : 0x1200 .. 0x1207
  * so the low-7-bits - encode the 'vowel'
  * So:
  * map 'base consonant' to a latin consonant
  *  and alter to matching character using a bit-mask
  *
  * There is no accepted standard for transcribing Ge'ez:
  * Following the transliteration on http://memhr.org/dic/ (almost)
  * rather than "BGN/PCGN"
  * differences being prefering "ħ" over "ḥ" for "ሐ" , etc
  * using CAPITALs for velarized variants of letter 
  * (which is somewhat a standard) 
  * eg: "ክ" => "k" , "ኽ" => "K"
  * but: "ብ" => "b", "ቭ" => "v" (more in loan words) 
  */
	   // testing in "http://ideone.com/eizWUV"
	 
	private function _getGeezRomanization($text) {
		global $geezPuncTable;
		$out = "";
		foreach ( unicode_str_split($text) as $ch ) {
			$num = unicode_str_ord($ch);
			$out .=  isGeezCharCode($num) ?
				(isGeezPunc($num) ? $geezPuncTable[$ch] : 	geezNum2Latin($num) )
				: $ch;
		}
		return $out;
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
    private function _getFurigana($text)
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
        
        // remove line returns so that they don't mess up
        // with mecab tokenization
        $text = str_replace(array("\r\n", "\r", "\n"), "", $text);

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
        "ッキャ","ッキュ","ッキョ","ッギャ","ッギュ","ッギョ","ッシャ",
        "ッシュ","ッショ","ッジャ","ッジュ","ッジョ","ッチャ","ッチュ",
        "ッチョ","ッニャ","ッニュ","ッニョ","ッヒャ","ッヒュ","ッヒョ",
        "ッビャ","ッビュ","ッビョ","ッピャ","ッピュ","ッピョ","ッミャ",
        "ッミュ","ッミョ","ッリャ","ッリュ","ッリョ",

        "キャ","キュ","キョ","ギャ","ギュ","ギョ","シャ",
        "シュ","ショ","ジャ","ジュ","ジョ","チャ","チュ",
        "チョ","ニャ","ニュ","ニョ","ヒャ","ヒュ","ヒョ",
        "ビャ","ビュ","ビョ","ピャ","ピュ","ピョ","ミャ",
        "ミュ","ミョ","リャ","リュ","リョ",
        
        "ッウィ","ッウェ","ッウォ","ッヴァ","ッヴィ","ッヴ","ッヴェ",
        "ッヴォ","ッシェ","ッジェ","ッチェ","ッツァ","ッツィ","ッツェ",
        "ッツォ","ッデュ","ッティ","ットゥ","ッテュ","ッディ","ッドゥ",
        "ッファ","ッフィ","ッフェ","ッフォ","ッフュ",
        
        "ウィ","ウェ","ウォ","ヴァ","ヴィ","ヴ","ヴェ",
        "ヴォ","シェ","ジェ","チェ","ツァ","ツィ","ツェ",
        "ツォ","デュ","ティ","トゥ","テュ","ディ","ドゥ",
        "ファ","フィ","フェ","フォ","フュ",
        
        "ッカ","ッガ","ッキ","ッギ","ック","ッグ","ッケ","ッゲ","ッコ","ッゴ",
        "ッサ","ッザ","ッシ","ッジ","ッス","ッズ","ッセ","ッゼ","ッソ","ッゾ",
        "ッタ","ッダ","ッチ","ッヂ","ッッ","ッツ","ッヅ","ッテ","ッデ","ット",
        "ッド","ッナ","ッニ","ッヌ","ッネ","ッノ","ッハ","ッバ","ッパ","ッヒ",
        "ッビ","ッピ","ッフ","ッブ","ップ","ッヘ","ッベ","ッペ","ッホ","ッボ",
        "ッポ","ッマ","ッミ","ッム","ッメ","ッモ","ッャ","ッヤ","ッュ","ッユ",
        "ッョ","ッヨ","ッラ","ッリ","ッル","ッレ","ッロ","ッヮ","ッワ","ッヲ",
        "ッン",

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
        "kya","kkyu","kkyo","ggya","ggyu","ggyo","ssha","sshu","ssho",
        "jja","jju","jjo","ccha","cchu","ccho","nnya","nnyu","nnyo",
        "hhya","hhyu","hhyo","bbya","bbyu","bbyo","ppya","ppyu","ppyo",
        "mmya","mmyu","mmyo","rrya","rryu","rryo",
        
        "kya","kyu","kyo","gya","gyu","gyo","sha","shu","sho",
        "ja","ju","jo","cha","chu","cho","nya","nyu","nyo",
        "hya","hyu","hyo","bya","byu","byo","pya","pyu","pyo",
        "mya","myu","myo","rya","ryu","ryo",
        
        "wwi","wwe","wwo","vva","vvi","vvu","vvr","vvo","sshe","jje",
        "che","ttsa","ttsi","ttse","ttso","ddyu","tti","ttu","ttyu","ddi",
        "ddu","ffa","ffi","ffe","ffo","ffyu",
        
        "wi","we","wo","va","vi","vu","vr","vo","she","je",
        "che","tsa","tsi","tse","tso","dyu","ti","tu","tyu","di",
        "du","fa","fi","fe","fo","fyu",
        
        "kka","gga","kki","ggi","kku","ggu","kke","gge","kko","ggo",
        "ssa","zza","sshi","jji","ssu","zzu","sse","zze","sso","zzo",
        "tta","dda","cchi","jji","","tsu","zzu","tte","dde","tto",
        "ddo","nna","nni","nnu","nne","nno","hha","bba","ppa","hhi",
        "bbi","ppi","ffu","bbu","ppu","hhe","bbe","ppe","hho","bbo",
        "ppo","mma","mmi","mmu","mme","mmo","yya","yya","yyu","yyu",
        "yyo","yyo","rra","rri","rru","rre","rro","wwa","wwa","wwo",
        "nn",

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
            $prev_word = null;
            foreach ($Owakati as $i=>$word) {
                // Ensure we only have katanakas.
                $Oyomi[$i] = str_replace($hiragana, $katakana, $Oyomi[$i]);
                // Little tsus are part of the previous word (やった splits into やっ た)
                // so we need to get tsus back first. This code merge multiple tsus into
                // one (やっっっった becomes "yatta"), but it removes ending tsus
                // (やったっ！ becomes "yatta!").
                if (!is_null($prev_word)) {
                    $prev_word_last_chr = mb_substr($prev_word, -1, 1, "UTF-8");
                    if ($prev_word_last_chr == "ッ")
                        $Oyomi[$i] = $prev_word_last_chr.$Oyomi[$i];
                }
                array_push(
                    $romanization,
                    str_replace($kata, $romanji, $Oyomi[$i])
                );
                $prev_word = $Oyomi[$i];
            }
        } else {
            $romanization = array();
        }

        return implode(" ", $romanization);
    }

}
