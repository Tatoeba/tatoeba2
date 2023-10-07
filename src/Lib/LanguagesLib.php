<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009-2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 * @link     https://tatoeba.org
 */
namespace App\Lib;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\I18n\I18n;

class LanguagesLib
{
    /**
     * Converts an ISO-639-3 code to its ISO-639-1 equivalent.
     * If not there is no equivalent, the provided code is returned.
     */
    public static function iso639_3_To_Iso639_1($code)
    {
        $map = self::get_Iso639_3_To_Iso639_1_Map();
        if (isset($map[$code])) {
            $code = $map[$code];
        }
        return $code;
    }

    /**
     * Extracts the language code from a locale and converts
     * it to its ISO-639-3 equivalent code. If not there is
     * no equivalent, the locale language code is returned.
     *
     * @param string $code    Locale
     *
     * @return string ISO-639-3 code or locale language
     */
    public static function locale_To_Iso639_3($code)
    {
        $hyphen = strpos($code, '-');
        if ($hyphen !== false) {
            $code = substr($code, 0, $hyphen);
        }
        $map = array_flip(self::get_Iso639_3_To_Iso639_1_Map());
        return $map[$code] ?? $code;
    }

    /**
     * Returns Tatoeba's map from ISO-639-3 to ISO-639-1.
     */
    public static function get_Iso639_3_To_Iso639_1_Map() {
        // Note that many languages with an ISO 639-3 code do not have an ISO 639-1 code.
        // Example: Khasi
        static $map = array(
            'abk' => 'ab', // Abkhaz
            'aar' => 'aa', // Afar
            'afr' => 'af', // Afrikaans
            'amh' => 'am', // Amharic
            'arg' => 'an', // Aragonese
            'asm' => 'as', // Assamese
            'ava' => 'av', // Avaric
            'ave' => 'ae', // Avestan
            'bak' => 'ba', // Bashkir
            'bam' => 'bm', // Bambara
            'bel' => 'be', // Belarusian
            'ben' => 'bn', // Bengali
            'bis' => 'bi', // Bislama
            'bod' => 'bo', // Tibetan
            'bos' => 'bs', // Bosnian
            'bre' => 'br', // Breton
            'bul' => 'bg', // Bulgarian
            'cat' => 'ca', // Catalan
            'ces' => 'cs', // Czech
            'cha' => 'ch', // Chamorro
            'che' => 'ce', // Chechen
            'chu' => 'cu', // Church Slavic, Old Slavonic, Church Slavonic, Old Bulgarian, Old Church Slavonic
            'chv' => 'cv', // Chuvash
            'cor' => 'kw', // Cornish
            'cos' => 'co', // Corsican
            'cym' => 'cy', // Welsh
            'dan' => 'da', // Danish
            'deu' => 'de', // German
            'div' => 'dv', // Divehi, Dhivehi, Maldivian
            'dzo' => 'dz', // Dzongkha
            'ell' => 'el', // Greek
            'eng' => 'en', // English
            'epo' => 'eo', // Esperanto
            'eus' => 'eu', // Basque
            'ewe' => 'ee', // Ewe
            'fao' => 'fo', // Faroese
            'fij' => 'fj', // Fijian
            'fin' => 'fi', // Finnish
            'fra' => 'fr', // French
            'fry' => 'fy', // Frisian
            'gla' => 'gd', // Scottish Gaelic
            'gle' => 'ga', // Irish
            'glg' => 'gl', // Galician
            'glv' => 'gv', // Manx
            'guj' => 'gu', // Gujarati
            'hat' => 'ht', // Haitian Creole
            'hau' => 'ha', // Hausa
            'heb' => 'he', // Hebrew
            'her' => 'hz', // Herero
            'hin' => 'hi', // Hindi
            'hmo' => 'ho', // Hiri Motu
            'hrv' => 'hr', // Croatian
            'hun' => 'hu', // Hungarian
            'hye' => 'hy', // Eastern Armenian
            'ibo' => 'ig', // Igbo
            'ido' => 'io', // Ido
            'ile' => 'ie', // Interlingue
            'ina' => 'ia', // Interlingua
            'ind' => 'id', // Indonesian
            'iii' => 'ii', // Sichuan Yi, Nuosu
            'isl' => 'is', // Icelandic
            'ita' => 'it', // Italian
            'jav' => 'jv', // Javanese
            'jpn' => 'ja', // Japanese
            'kal' => 'kl', // Greenlandic
            'kan' => 'kn', // Kannada
            'kas' => 'ks', // Kashmiri
            'kat' => 'ka', // Georgian
            'kaz' => 'kk', // Kazakh
            'khm' => 'km', // Khmer
            'kik' => 'ki', // Kikuyu, Gikuyu
            'kin' => 'rw', // Kinyarwanda
            'kir' => 'ky', // Kyrgyz
            'kor' => 'ko', // Korean
            'kua' => 'kj', // Kuanyama, Kwanyama
            'lao' => 'lo', // Lao
            'lat' => 'la', // Latin
            'lim' => 'li', // Limburgan, Limburger, Limburgish
            'lin' => 'ln', // Lingala
            'lit' => 'lt', // Lithuanian
            'ltz' => 'lb', // Luxembourgish
            'lug' => 'lg', // Luganda
            'lub' => 'lu', // Luba-Katanga
            'mah' => 'mh', // Marshallese
            'mal' => 'ml', // Malayalam
            'mar' => 'mr', // Marathi
            'mkd' => 'mk', // Macedonian
            'mlt' => 'mt', // Maltese
            'mri' => 'mi', // Maori
            'mya' => 'my', // Burmese
            'nau' => 'na', // Nauruan
            'nav' => 'nv', // Navajo
            'nbl' => 'nr', // South Ndebele
            'nde' => 'nd', // North Ndebele
            'ndo' => 'ng', // Ndonga
            'nld' => 'nl', // Dutch
            'nob' => 'nb', // Norwegian Bokmål)
            'nno' => 'nn', // Norwegian Nynorsk
            'nya' => 'ny', // Chinyanja
            'oci' => 'oc', // Occitan
            'oss' => 'os', // Ossetian
            'pan' => 'pa', // Punjabi (Eastern)
            'pli' => 'pi', // Pali
            'pol' => 'pl', // Polish
            'por' => 'pt', // Portuguese
            'roh' => 'rm', // Romansh
            'ron' => 'ro', // Romanian
            'run' => 'rn', // Rundi
            'rus' => 'ru', // Russian
            'sag' => 'sg', // Sango
            'san' => 'sa', // Sanskrit
            'sin' => 'si', // Sinhala
            'slk' => 'sk', // Slovak
            'slv' => 'sl', // Slovenian
            'sme' => 'se', // Northern Sami
            'smo' => 'sm', // Samoan
            'sna' => 'sn', // Shona
            'snd' => 'sd', // Sindhi
            'som' => 'so', // Somali
            'sot' => 'st', // Southern Sotho
            'spa' => 'es', // Spanish
            'srp' => 'sr', // Serbian
            'ssw' => 'ss', // Swazi
            'sun' => 'su', // Sundanese
            'swe' => 'sv', // Swedish
            'tah' => 'ty', // Tahitian
            'tam' => 'ta', // Tamil
            'tat' => 'tt', // Tatar
            'tel' => 'te', // Telugu
            'tgk' => 'tg', // Tajik
            'tgl' => 'tl', // Tagalog
            'tha' => 'th', // Thai
            'tir' => 'ti', // Tigrinya
            'ton' => 'to', // Tongan
            'tsn' => 'tn', // Setswana
            'tso' => 'ts', // Tsonga
            'tuk' => 'tk', // Turkmen
            'tur' => 'tr', // Turkish
            'twi' => 'tw', // Twi
            'uig' => 'ug', // Uyghur
            'ukr' => 'uk', // Ukrainian
            'urd' => 'ur', // Urdu
            'ven' => 've', // Venda
            'vie' => 'vi', // Vietnamese
            'vol' => 'vo', // Volapük
            'wln' => 'wa', // Walloon
            'wol' => 'wo', // Wolof
            'xho' => 'xh', // Xhosa
            'yor' => 'yo', // Yoruba
            'zul' => 'zu', // Zulu

            // Macrolanguages that we are using because of legacy but
            // should ideally changed into individual language codes
            'sqi' => 'sq', // Albanian
            'ara' => 'ar', // Arabic
            'aym' => 'ay', // Aymara
            'aze' => 'az', // Azerbaijani
            'est' => 'et', // Estonian
            'grn' => 'gn', // Guarani
            'mlg' => 'mg', // Malagasy
            'mon' => 'mn', // Mongolian
            'oji' => 'oj', // Ojibwa
            'ori' => 'or', // Oriya
            'pus' => 'ps', // Pashto, Pushto
            'que' => 'qu', // Quechua
            'srd' => 'sc', // Sardinian
            'uzb' => 'uz', // Uzbek
            'yid' => 'yi', // Yiddish

            // Macrolanguages that are here because most browsers
            // still need lang="zh-..." for proper font selection
            'yue' => 'zh',
            'wuu' => 'zh',
            'lzh' => 'zh',
            'cmn' => 'zh', // 'cmn' must appear last within the multiple 'zh'
                           // because we're using it as UI language code and
                           // so that array_flip()'ing this array will return
                           // 'zh' => 'cmn'.
            'zsm' => 'ms', // Malay
        );
        return $map;
    }

    /**
     * Return array of languages in Tatoeba. Do not call this function too
     * soon in the CakePHP process, or I18n::setLocale() won't be called
     * yet and it will defeat the purpose of the memoizer $languages,
     * hitting performance down.
     *
     * @return array
     */
    public static function languagesInTatoeba()
    {
        static $languages;
        static $lastLang;

        $currentLang = I18n::getLocale();

        if (!$languages || $currentLang != $lastLang) {
            $lastLang = $currentLang;
            $languages = array(
                'ara' => __d('languages', 'Arabic'),
                'eng' => __d('languages', 'English'),
                'jpn' => __d('languages', 'Japanese'),
                'fra' => __d('languages', 'French'),
                'deu' => __d('languages', 'German'),
                'spa' => __d('languages', 'Spanish'),
                'ita' => __d('languages', 'Italian'),
                'vie' => __d('languages', 'Vietnamese'),
                'rus' => __d('languages', 'Russian'),
                'cmn' => __d('languages', 'Mandarin Chinese'),
                'kor' => __d('languages', 'Korean'),
                'nld' => __d('languages', 'Dutch'),
                'heb' => __d('languages', 'Hebrew'),
                'ind' => __d('languages', 'Indonesian'),
                'por' => __d('languages', 'Portuguese'),
                'fin' => __d('languages', 'Finnish'),
                'bul' => __d('languages', 'Bulgarian'),
                'ukr' => __d('languages', 'Ukrainian'),
                'ces' => __d('languages', 'Czech'),
                'epo' => __d('languages', 'Esperanto'),
                'ell' => __d('languages', 'Greek'),
                'tur' => __d('languages', 'Turkish'),
                'swe' => __d('languages', 'Swedish'),
                'nob' => __d('languages', 'Norwegian Bokmål'),
                'zsm' => __d('languages', 'Malay'),
                'est' => __d('languages', 'Estonian'),
                'kat' => __d('languages', 'Georgian'),
                'pol' => __d('languages', 'Polish'),
                'swh' => __d('languages', 'Swahili'),
                'lat' => __d('languages', 'Latin'),
                // TODO to change when shanghainese will not be the only wu dialect
                'wuu' => __d('languages', 'Shanghainese'),
                'arz' => __d('languages', 'Egyptian Arabic'),
                'bel' => __d('languages', 'Belarusian'),
                'hun' => __d('languages', 'Hungarian'),
                'isl' => __d('languages', 'Icelandic'),
                'sqi' => __d('languages', 'Albanian'),
                'yue' => __d('languages', 'Cantonese'),
                'afr' => __d('languages', 'Afrikaans'),
                'fao' => __d('languages', 'Faroese'),
                'fry' => __d('languages', 'Frisian'),
                'bre' => __d('languages', 'Breton'),
                'ron' => __d('languages', 'Romanian'),
                'uig' => __d('languages', 'Uyghur'),
                'uzb' => __d('languages', 'Uzbek'),
                'nno' => __d('languages', 'Norwegian Nynorsk'),
                'srp' => __d('languages', 'Serbian'),
                'tat' => __d('languages', 'Tatar'),
                'yid' => __d('languages', 'Yiddish'),
                'pes' => __d('languages', 'Persian'),
                'nan' => __d('languages', 'Min Nan Chinese'),
                'eus' => __d('languages', 'Basque'),
                'slk' => __d('languages', 'Slovak'),
                'dan' => __d('languages', 'Danish'),
                'hye' => __d('languages', 'Eastern Armenian'),
                'acm' => __d('languages', 'Iraqi Arabic'),
                'san' => __d('languages', 'Sanskrit'),
                'urd' => __d('languages', 'Urdu'),
                'hin' => __d('languages', 'Hindi'),
                'ben' => __d('languages', 'Bengali'),
                'cycl' => __d('languages', 'CycL'),
                'cat' => __d('languages', 'Catalan'),
                'kaz' => __d('languages', 'Kazakh'),
                'lvs' => __d('languages', 'Latvian'),
                'bos' => __d('languages', 'Bosnian'),
                'hrv' => __d('languages', 'Croatian'),
                'orv' => __d('languages', 'Old East Slavic'),
                'cha' => __d('languages', 'Chamorro'),
                'tgl' => __d('languages', 'Tagalog'),
                'que' => __d('languages', 'Quechua'),
                'mon' => __d('languages', 'Mongolian'),
                'lit' => __d('languages', 'Lithuanian'),
                'glg' => __d('languages', 'Galician'),
                'gle' => __d('languages', 'Irish'),
                'ina' => __d('languages', 'Interlingua'),
                'jbo' => __d('languages', 'Lojban'),
                'tok' => __d('languages', 'Toki Pona'),
                'ain' => __d('languages', 'Ainu'),
                'scn' => __d('languages', 'Sicilian'),
                'mal' => __d('languages', 'Malayalam'),
                'nds' => __d('languages', 'Low German (Low Saxon)'),
                'tlh' => __d('languages', 'Klingon'),
                'slv' => __d('languages', 'Slovenian'),
                'tha' => __d('languages', 'Thai'),
                'lzh' => __d('languages', 'Literary Chinese'),
                'oss' => __d('languages', 'Ossetian'),
                'roh' => __d('languages', 'Romansh'),
                'vol' => __d('languages', 'Volapük'),
                'gla' => __d('languages', 'Scottish Gaelic'),
                'ido' => __d('languages', 'Ido'),
                'ast' => __d('languages', 'Asturian'),
                'ile' => __d('languages', 'Interlingue'),
                'oci' => __d('languages', 'Occitan'),
                'xal' => __d('languages', 'Kalmyk'),
                'ang' => __d('languages', 'Old English'),
                'dsb' => __d('languages', 'Lower Sorbian'),
                'hsb' => __d('languages', 'Upper Sorbian'),
                'ksh' => __d('languages', 'Kölsch'),
                'cym' => __d('languages', 'Welsh'),
                'ewe' => __d('languages', 'Ewe'),
                'sjn' => __d('languages', 'Sindarin'),
                'tel' => __d('languages', 'Telugu'),
                'tpi' => __d('languages', 'Tok Pisin'),
                'qya' => __d('languages', 'Quenya'),
                'nov' => __d('languages', 'Novial'),
                'mri' => __d('languages', 'Maori'),
                'lld' => __d('languages', 'Ladin'),
                'ber' => __d('languages', 'Berber'),
                'xho' => __d('languages', 'Xhosa'),
                'pnb' => __d('languages', 'Punjabi (Western)'),
                'mlg' => __d('languages', 'Malagasy'),
                'grn' => __d('languages', 'Guarani'),
                'lad' => __d('languages', 'Ladino'),
                'pms' => __d('languages', 'Piedmontese'),
                'avk' => __d('languages', 'Kotava'),
                'mar' => __d('languages', 'Marathi'),
                'tpw' => __d('languages', 'Old Tupi'),
                'tgk' => __d('languages', 'Tajik'),
                'prg' => __d('languages', 'Old Prussian'),
                'npi' => __d('languages', 'Nepali'),
                'mlt' => __d('languages', 'Maltese'),
                'ckt' => __d('languages', 'Chukchi'),
                'cor' => __d('languages', 'Cornish'),
                'aze' => __d('languages', 'Azerbaijani'),
                'khm' => __d('languages', 'Khmer'),
                'lao' => __d('languages', 'Lao'),
                'bod' => __d('languages', 'Tibetan'),
                'hil' => __d('languages', 'Hiligaynon'),
                'arq' => __d('languages', 'Algerian Arabic'),
                'pcd' => __d('languages', 'Picard'),
                'grc' => __d('languages', 'Ancient Greek'),
                'amh' => __d('languages', 'Amharic'),
                'awa' => __d('languages', 'Awadhi'),
                'bho' => __d('languages', 'Bhojpuri'),
                'cbk' => __d('languages', 'Chavacano'),
                'enm' => __d('languages', 'Middle English'),
                'frm' => __d('languages', 'Middle French'),
                'hat' => __d('languages', 'Haitian Creole'),
                'jdt' => __d('languages', 'Juhuri (Judeo-Tat)'),
                'kal' => __d('languages', 'Greenlandic'),
                'mhr' => __d('languages', 'Meadow Mari'),
                'nah' => __d('languages', 'Nahuatl'),
                'pdc' => __d('languages', 'Pennsylvania German'),
                'sin' => __d('languages', 'Sinhala'),
                'tuk' => __d('languages', 'Turkmen'),
                'wln' => __d('languages', 'Walloon'),
                'bak' => __d('languages', 'Bashkir'),
                'hau' => __d('languages', 'Hausa'),
                'ltz' => __d('languages', 'Luxembourgish'),
                'mgm' => __d('languages', 'Mambae'),
                'som' => __d('languages', 'Somali'),
                'zul' => __d('languages', 'Zulu'),
                'haw' => __d('languages', 'Hawaiian'),
                'kir' => __d('languages', 'Kyrgyz'),
                'mkd' => __d('languages', 'Macedonian'),
                'mrj' => __d('languages', 'Hill Mari'),
                'ppl' => __d('languages', 'Pipil'),
                'yor' => __d('languages', 'Yoruba'),
                'kin' => __d('languages', 'Kinyarwanda'),
                'shs' => __d('languages', 'Shuswap'),
                'chv' => __d('languages', 'Chuvash'),
                'lkt' => __d('languages', 'Lakota'),
                'ota' => __d('languages', 'Ottoman Turkish'),
                'sna' => __d('languages', 'Shona'),
                'mnw' => __d('languages', 'Mon'),
                'nog' => __d('languages', 'Nogai'),
                'sah' => __d('languages', 'Yakut'),
                'abk' => __d('languages', 'Abkhaz'),
                'tet' => __d('languages', 'Tetun'),
                'tam' => __d('languages', 'Tamil'),
                'udm' => __d('languages', 'Udmurt'),
                'kum' => __d('languages', 'Kumyk'),
                'crh' => __d('languages', 'Crimean Tatar'),
                'nya' => __d('languages', 'Chinyanja'),
                'liv' => __d('languages', 'Livonian'),
                'nav' => __d('languages', 'Navajo'),
                'chr' => __d('languages', 'Cherokee'),
                'guj' => __d('languages', 'Gujarati'),
                'pan' => __d('languages', 'Punjabi (Eastern)'),
                'kha' => __d('languages', 'Khasi'),
                'jav' => __d('languages', 'Javanese'),
                'zza' => __d('languages', 'Zaza'),
                'egl' => __d('languages', 'Emilian'),
                'tir' => __d('languages', 'Tigrinya'),
                'sme' => __d('languages', 'Northern Sami'),
                'max' => __d('languages', 'North Moluccan Malay'),
                'pam' => __d('languages', 'Kapampangan'),
                'dtp' => __d('languages', 'Central Dusun'),
                'cho' => __d('languages', 'Choctaw'),
                'kzj' => __d('languages', 'Coastal Kadazan'),
                'smo' => __d('languages', 'Samoan'),
                'fij' => __d('languages', 'Fijian'),
                'wol' => __d('languages', 'Wolof'),
                'che' => __d('languages', 'Chechen'),
                'sag' => __d('languages', 'Sango'),
                'hif' => __d('languages', 'Fiji Hindi'),
                'ton' => __d('languages', 'Tongan'),
                'ngt' => __d('languages', 'Ngeq '),
                'kam' => __d('languages', 'Kamba'),
                'vec' => __d('languages', 'Venetian'),
                'mya' => __d('languages', 'Burmese'),
                'gil' => __d('languages', 'Gilbertese'),
                'myv' => __d('languages', 'Erzya'),
                'niu' => __d('languages', 'Niuean'),
                'vro' => __d('languages', 'Võro'),
                'glv' => __d('languages', 'Manx'),
                'lin' => __d('languages', 'Lingala'),
                'lfn' => __d('languages', 'Lingua Franca Nova'),
                'pus' => __d('languages', 'Pashto'),
                'kjh' => __d('languages', 'Khakas'),
                'dng' => __d('languages', 'Dungan'),
                'fur' => __d('languages', 'Friulian'),
                'mah' => __d('languages', 'Marshallese'),
                'pfl' => __d('languages', 'Palatine German'),
                'kan' => __d('languages', 'Kannada'),
                'crs' => __d('languages', 'Seychellois Creole'),
                'gsw' => __d('languages', 'Swiss German'),
                'osx' => __d('languages', 'Old Saxon'),
                'sux' => __d('languages', 'Sumerian'),
                'sco' => __d('languages', 'Scots'),
                'moh' => __d('languages', 'Mohawk'),
                'ceb' => __d('languages', 'Cebuano'),
                'lmo' => __d('languages', 'Lombard'),
                'tso' => __d('languages', 'Tsonga'),
                'bua' => __d('languages', 'Buryat'),
                'aym' => __d('languages', 'Aymara'),
                'ilo' => __d('languages', 'Ilocano'),
                'kaa' => __d('languages', 'Karakalpak'),
                'nlv' => __d('languages', 'Orizaba Nahuatl'),
                'ngu' => __d('languages', 'Guerrero Nahuatl'),
                'ady' => __d('languages', 'Adyghe'),
                'brx' => __d('languages', 'Bodo'),
                'gag' => __d('languages', 'Gagauz'),
                'rom' => __d('languages', 'Romani'),
                'lzz' => __d('languages', 'Laz'),
                'fuc' => __d('languages', 'Pulaar'),
                'umb' => __d('languages', 'Umbundu'),
                'tkl' => __d('languages', 'Tokelauan'),
                'sot' => __d('languages', 'Southern Sotho'),
                'alt' => __d('languages', 'Southern Altai'),
                'war' => __d('languages', 'Waray'),
                'snd' => __d('languages', 'Sindhi'),
                'tsn' => __d('languages', 'Setswana'),
                'srd' => __d('languages', 'Sardinian'),
                'pau' => __d('languages', 'Palauan'),
                'gbm' => __d('languages', 'Garhwali'),
                'oji' => __d('languages', 'Ojibwe'),
                'lug' => __d('languages', 'Luganda'),
                'hak' => __d('languages', 'Hakka Chinese'),
                'bam' => __d('languages', 'Bambara'),
                'arg' => __d('languages', 'Aragonese'),
                'asm' => __d('languages', 'Assamese'),
                'fuv' => __d('languages', 'Nigerian Fulfulde'),
                'hoc' => __d('languages', 'Ho'),
                'sun' => __d('languages', 'Sundanese'),
                'apc' => __d('languages', 'North Levantine Arabic'),
                'tyv' => __d('languages', 'Tuvinian'),
                'krc' => __d('languages', 'Karachay-Balkar'),
                'pap' => __d('languages', 'Papiamento'),
                'non' => __d('languages', 'Old Norse'),
                'ori' => __d('languages', 'Odia (Oriya)'),
                'iba' => __d('languages', 'Iban'),
                'oar' => __d('languages', 'Old Aramaic'),
                'ary' => __d('languages', 'Moroccan Arabic'),
                'cyo' => __d('languages', 'Cuyonon'),
                'ibo' => __d('languages', 'Igbo'),
                'csb' => __d('languages', 'Kashubian'),
                'lou' => __d('languages', 'Louisiana Creole'),
                'urh' => __d('languages', 'Urhobo'),
                'mvv' => __d('languages', 'Tagal Murut'),
                'mdf' => __d('languages', 'Moksha'),
                'pag' => __d('languages', 'Pangasinan'),
                'cos' => __d('languages', 'Corsican'),
                'hnj' => __d('languages', 'Hmong Njua (Green)'),
                'rif' => __d('languages', 'Tarifit'),
                'nch' => __d('languages', 'Central Huasteca Nahuatl'),
                'kek' => __d('languages', "Kekchi (Q'eqchi')"),
                'ssw' => __d('languages', 'Swazi'),
                'ban' => __d('languages', 'Balinese'),
                'aii' => __d('languages', 'Assyrian Neo-Aramaic'),
                'tvl' => __d('languages', 'Tuvaluan'),
                'kxi' => __d('languages', 'Keningau Murut'),
                'bvy' => __d('languages', 'Baybayanon'),
                'mfe' => __d('languages', 'Morisyen'),
                'mww' => __d('languages', 'Hmong Daw (White)'),
                'bcl' => __d('languages', 'Central Bikol'),
                'nau' => __d('languages', 'Nauruan'),
                'zlm' => __d('languages', 'Malay (Vernacular)'),
                'nst' => __d('languages', 'Naga (Tangshang)'),
                'quc' => __d('languages', "K'iche'"),
                'afb' => __d('languages', 'Gulf Arabic'),
                'min' => __d('languages', 'Minangkabau'),
                'tmw' => __d('languages', 'Temuan'),
                'cjy' => __d('languages', 'Jin Chinese'),
                'mai' => __d('languages', 'Maithili'),
                'mad' => __d('languages', 'Madurese'),
                'bjn' => __d('languages', 'Banjar'),
                'got' => __d('languages', 'Gothic'),
                'hsn' => __d('languages', 'Xiang Chinese'),
                'gan' => __d('languages', 'Gan Chinese'),
                'bar' => __d('languages', 'Bavarian'),
                'tzl' => __d('languages', 'Talossan'),
                'sgs' => __d('languages', 'Samogitian'),
                'ldn' => __d('languages', 'Láadan'),
                'dws' => __d('languages', 'Dutton World Speedwords'),
                'afh' => __d('languages', 'Afrihili'),
                'krl' => __d('languages', 'Karelian'),
                'vep' => __d('languages', 'Veps'),
                'rue' => __d('languages', 'Rusyn'),
                'tah' => __d('languages', 'Tahitian'),
                'tly' => __d('languages', 'Talysh'),
                'mic' => __d('languages', "Mi'kmaq"),
                'ext' => __d('languages', 'Extremaduran'),
                'swg' => __d('languages', 'Swabian'),
                'izh' => __d('languages', 'Ingrian'),
                'sma' => __d('languages', 'Southern Sami'),
                'jam' => __d('languages', 'Jamaican Patois'),
                'mwl' => __d('languages', 'Mirandese'),
                'kpv' => __d('languages', 'Komi-Zyrian'),
                'cmo' => __d('languages', 'Central Mnong'),
                'koi' => __d('languages', 'Komi-Permyak'),
                'ike' => __d('languages', 'Inuktitut'),
                'kab' => __d('languages', 'Kabyle'),
                'run' => __d('languages', 'Kirundi'),
                'aln' => __d('languages', 'Gheg Albanian'),
                'akl' => __d('languages', 'Aklanon'),
                'mnc' => __d('languages', 'Manchu'),
                'kas' => __d('languages', 'Kashmiri'),
                'otk' => __d('languages', 'Old Turkish'),
                'aoz' => __d('languages', 'Uab Meto'),
                'shy' => __d('languages', 'Tachawit'),
                'fkv' => __d('languages', 'Kven Finnish'),
                'rap' => __d('languages', 'Rapa Nui'),
                'gcf' => __d('languages', 'Guadeloupean Creole French'),
                'gos' => __d('languages', 'Gronings'),
                'lij' => __d('languages', 'Ligurian'),
                'tig' => __d('languages', 'Tigre'),
                'thv' => __d('languages', 'Tahaggart Tamahaq'),
                'div' => __d('languages', 'Dhivehi'),
                'hrx' => __d('languages', 'Hunsrik'),
                'cay' => __d('languages', 'Cayuga'),
                'gaa' => __d('languages', 'Ga'),
                'cpi' => __d('languages', 'Chinese Pidgin English'),
                'bzt' => __d('languages', 'Brithenig'),
                'ltg' => __d('languages', 'Latgalian'),
                'emx' => __d('languages', 'Erromintxela'),
                'gom' => __d('languages', 'Konkani (Goan)'),
                'chg' => __d('languages', 'Chagatai'),
                'xmf' => __d('languages', 'Mingrelian'),
                'osp' => __d('languages', 'Old Spanish'),
                'tmr' => __d('languages', 'Jewish Babylonian Aramaic'),
                'ryu' => __d('languages', 'Okinawan'),
                'evn' => __d('languages', 'Evenki'),
                'bis' => __d('languages', 'Bislama'),
                'stq' => __d('languages', 'Saterland Frisian'),
                'fro' => __d('languages', 'Old French'),
                'syc' => __d('languages', 'Syriac'),
                'frr' => __d('languages', 'North Frisian'),
                'nys' => __d('languages', 'Nyungar'),
                'tts' => __d('languages', 'Isan'),
                'toi' => __d('languages', 'Tonga (Zambezi)'),
                'new' => __d('languages', 'Newari'),
                'jpa' => __d('languages', 'Jewish Palestinian Aramaic'),
                'phn' => __d('languages', 'Phoenician'),
                'rel' => __d('languages', 'Rendille'),
                'iii' => __d('languages', 'Nuosu'),
                'drt' => __d('languages', 'Drents'),
                'laa' => __d('languages', 'Southern Subanen'),
                'chn' => __d('languages', 'Chinook Jargon'),
                'bal' => __d('languages', 'Baluchi'),
                'pli' => __d('languages', 'Pali'),
                'hbo' => __d('languages', 'Ancient Hebrew'),
                'ajp' => __d('languages', 'South Levantine Arabic'),
                'hax' => __d('languages', 'Southern Haida'),
                'hdn' => __d('languages', 'Northern Haida'),
                'xqa' => __d('languages', 'Karakhanid'),
                'crk' => __d('languages', 'Plains Cree'),
                'yua' => __d('languages', 'Yucatec Maya'),
                'pal' => __d('languages', 'Middle Persian (Pahlavi)'),
                'mni' => __d('languages', 'Meitei'),
                'ayl' => __d('languages', 'Libyan Arabic'),
                'ood' => __d('languages', "O'odham"),
                'lut' => __d('languages', 'Lushootseed'),
                'ofs' => __d('languages', 'Old Frisian'),
                'nus' => __d('languages', 'Nuer'),
                'ckb' => __d('languages', 'Central Kurdish (Soranî)'),
                'kmr' => __d('languages', 'Northern Kurdish (Kurmancî)'),
                'sdh' => __d('languages', 'Southern Kurdish'),
                'kiu' => __d('languages', 'Northern Zaza (Kirmanjki)'),
                'diq' => __d('languages', 'Southern Zaza (Dimli)'),
                'zgh' => __d('languages', 'Standard Moroccan Tamazight'),
                'bfz' => __d('languages', 'Mahasu Pahari'),
                'qxq' => __d('languages', 'Qashqai'),
                'klj' => __d('languages', 'Khalaj'),
                'dar' => __d('languages', 'Dargwa'),
                'lbe' => __d('languages', 'Lak'),
                'ava' => __d('languages', 'Avar'),
                'mus' => __d('languages', 'Muskogee (Creek)'),
                'abq' => __d('languages', 'Abaza'),
                'inh' => __d('languages', 'Ingush'),
                'kbd' => __d('languages', 'Kabardian'),
                'srn' => __d('languages', 'Sranan Tongo'),
                'lez' => __d('languages', 'Lezgi'),
                'guc' => __d('languages', 'Wayuu'),
                'lim' => __d('languages', 'Limburgish'),
                'zea' => __d('languages', 'Zeelandic'),
                'mnr' => __d('languages', 'Mono (USA)'),
                'shi' => __d('languages', 'Tashelhit'),
                'bom' => __d('languages', 'Berom'),
                'sat' => __d('languages', 'Santali'),
                'mik' => __d('languages', 'Hitchiti'),
                'szl' => __d('languages', 'Silesian'),
                'igs' => __d('languages', 'Interglossa'),
                'knc' => __d('languages', 'Central Kanuri'),
                'nnb' => __d('languages', 'Nande'),
                'swc' => __d('languages', 'Congo Swahili'),
                'rhg' => __d('languages', 'Rohingya'),
                'mfa' => __d('languages', 'Kelantan-Pattani Malay'),
                'skr' => __d('languages', 'Saraiki'),
                'nap' => __d('languages', 'Neapolitan'),
                'syl' => __d('languages', 'Sylheti'),
                'tum' => __d('languages', 'Tumbuka'),
                'arn' => __d('languages', 'Mapuche'),
                'hyw' => __d('languages', 'Western Armenian'),
                'guw' => __d('languages', 'Gun'),

            );
        }
        return $languages;
    }

    /**
     * Returns the language tag giving an ISO-639-3 code.
     * See http://www.w3.org/International/articles/language-tags/.
     *
     * @param string $code    ISO-639-3 language code.
     * @param string $script  ISO 15924 script.
     *
     * @return string lang HTML attribute compliant string.
     */
    public static function languageTag($code, $script = '')
    {
        $scriptMap = array(
            'lzh' => 'Hant',
        );
        if (empty($script) && isset($scriptMap[$code])) {
            $script = $scriptMap[$code];
        }
        if (!empty($script)) {
            $script = '-'.$script;
        }

        // The rule is to use 2-letters code if available,
        // or 3-letters code otherwise
        $code = self::iso639_3_To_Iso639_1($code);

        return $code.$script;
    }

    /**
     * Get the direction (right to left or left to right) of a language
     *
     * @param string $lang ISO-639-3 code or ISO-639-1 code or locale
     *
     * @return string "rtl" (right to left) or "ltr" (left to right)
     *                or "auto" (depends on used script)
     */
    public static function getLanguageDirection($lang) {

        $direction = "ltr";

        $rightToLeftLangs = array(
            "ara",
            "heb",
            "arz",
            "uig",
            "pes",
            "acm",
            "urd",
            "yid",
            "pnb",
            "oar",
            "ary",
            "aii",
            "afb",
            "pus",
            "snd",
            "div",
            "otk",
            "tmr",
            "syc",
            "phn",
            "jpa",
            "hbo",
            "ajp",
            "ayl",
            "sdh",
            "ckb",
            "skr",
        );

        $autoLangs = array(
            "apc",
            "arq",
            "ota",
            "chg",
            "lad",
            "xqa",
            "qxq",
            "klj",
            "zlm",
            "zsm",
            "knc",
            "swc",
            "rhg",
            "mfa",
            "bal",
        );

        $lang = self::locale_To_Iso639_3($lang);

        if (in_array($lang, $rightToLeftLangs)) {
            $direction = "rtl";
        }

        if (in_array($lang, $autoLangs)) {
            $direction = "auto";
        }

        return $direction;
    }


    public static function filteredLanguagesList($languageCodes)
    {
        return array_intersect_key(
            self::languagesInTatoeba(),
            array_flip($languageCodes)
        );
    }

    public static function languageExists($code)
    {
        $available = self::languagesInTatoeba();
        return isset($available[$code]);
    }

    public static function activeUiLanguages()
    {
        $languages = Cache::remember(
            'active_ui_languages',
            function () {
                return array_filter(
                    Configure::read('UI.languages'),
                    function ($val) { return is_array($val); }
                );
            }
        );

        return $languages;
    }
}
