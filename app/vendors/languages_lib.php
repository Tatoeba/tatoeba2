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
 * @link     http://tatoeba.org
 */

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
     * Returns Tatoeba's map from ISO-639-3 to ISO-639-1.
     */
    public static function get_Iso639_3_To_Iso639_1_Map() {
        // Note that many languages with an ISO 639-3 code do not have an ISO 639-1 code.
        // Example: Khasi
        static $map = array(
            'abk' => 'ab', // Abkhaz
            'ady' => 'ad', // Adyghe
            'afr' => 'af', // Afrikaans
            'alt' => 'al', // Southern Altai
            'amh' => 'am', // Amharic
            'ara' => 'ar', // Arabic
            'arg' => 'ag', // Aragonese
            'ary' => 'ay', // Moroccan Arabic
            'asm' => 'as', // Assamese
            'aym' => 'ay', // Aymara
            'aze' => 'az', // Azerbaijani
            'bak' => 'ba', // Bashkir
            'bam' => 'bm', // Bambara
            'bel' => 'be', // Belarusian
            'ben' => 'bn', // Bengali
            'bod' => 'bo', // Tibetan
            'bos' => 'bs', // Bosnian
            'bre' => 'br', // Breton
            'brx' => 'bx', // Bodo
            'bua' => 'bu', // Buryat
            'bul' => 'bg', // Bulgarian
            'cat' => 'ca', // Catalan
            'ceb' => 'cb', // Cebuano
            'ces' => 'cs', // Czech
            'cha' => 'ch', // Chamorro
            'che' => 'ce', // Chechen
            'cho' => 'ch', // Choctaw
            'chv' => 'cv', // Chuvash
            'cor' => 'kw', // Cornish
            'cos' => 'cc', // Corsican
            'crs' => 'cr', // Seychellois Creole
            'csb' => 'cb', // Kashubian
            'cyo' => 'co', // Cuyonon
            'cym' => 'cy', // Welsh
            'dan' => 'da', // Danish
            'deu' => 'de', // German
            'dng' => 'dg', // Dungan
            'dtp' => 'dt', // Central Dusun
            'ell' => 'el', // Greek
            'eng' => 'en', // English
            'epo' => 'eo', // Esperanto
            'est' => 'et', // Estonian
            'eus' => 'eu', // Basque
            'ewe' => 'ee', // Ewe
            'fao' => 'fo', // Faroese
            'fij' => 'fj', // Fijian
            'fin' => 'fi', // Finnish
            'fra' => 'fr', // French
            'fry' => 'fy', // Frisian
            'fuc' => 'fc', // Pulaar
            'fur' => 'fu', // Friulian
            'fuv' => 'fv', // Nigerian Fulfulde
            'gag' => 'gg', // Gagauz
            'gil' => 'gi', // Gilbertese
            'gbm' => 'gb', // Garhwali
            'gla' => 'gd', // Scottish Gaelic
            'gle' => 'ga', // Irish
            'glg' => 'gl', // Galician
            'glv' => 'gv', // Manx
            'grn' => 'gn', // Guarani
            'gsw' => 'gs', // Swiss German
            'guj' => 'gu', // Gujarati
            'hak' => 'hk', // Hakka Chinese
            'hat' => 'ht', // Haitian Creole
            'hau' => 'ha', // Hausa
            'heb' => 'he', // Hebrew
            'hif' => 'hf', // Fiji Hindi
            'hin' => 'hi', // Hindi
            'hnj' => 'hn', // Hmong Njua
            'hoc' => 'ho', // Ho
            'hrv' => 'hr', // Croatian
            'hun' => 'hu', // Hungarian
            'hye' => 'hy', // Armenian
            'iba' => 'ib', // Iban
            'ibo' => 'ii', // Igbo
            'ido' => 'io', // Ido
            'ile' => 'ie', // Interlingue
            'ilo' => 'il', // Ilocano
            'ina' => 'ia', // Interlingua
            'ind' => 'id', // Indonesian
            'isl' => 'is', // Icelandic
            'ita' => 'it', // Italian
            'jav' => 'jv', // Javanese
            'jpn' => 'ja', // Japanese
            'kaa' => 'kz', // Karakalpak
            'kal' => 'kl', // Greenlandic
            'kam' => 'kk', // Kamba
            'kan' => 'kn', // Kannada
            'kat' => 'ka', // Georgian
            'kaz' => 'kk', // Kazakh
            'khm' => 'km', // Khmer
            'kin' => 'rw', // Kinyarwanda
            'kir' => 'ky', // Kyrgyz
            'kjh' => 'kj', // Khakas
            'kor' => 'ko', // Korean
            'krc' => 'kr', // Karachay-Balkar
            'kur' => 'ku', // Kurdish
            'kzj' => 'kz', // Coastal Kadazan
            'lao' => 'lo', // Lao
            'lat' => 'la', // Latin
            'lfn' => 'lf', // Lingua Franca Nova
            'lin' => 'li', // Lingala
            'lit' => 'lt', // Lithuanian
            'lmo' => 'lm', // Lombard
            'lou' => 'lu', // Louisiana Creole
            'ltz' => 'lb', // Luxembourgish
            'lug' => 'lg', // Luganda
            'lzz' => 'lz', // Laz
            'mah' => 'mh', // Marshallese
            'mal' => 'ml', // Malayalam
            'mar' => 'mr', // Marathi
            'max' => 'mx', // North Moluccan Malay
            'mdf' => 'md', // Moksha
            'mkd' => 'mk', // Macedonian
            'mlg' => 'mg', // Malagasy
            'mlt' => 'mt', // Maltese
            'moh' => 'mo', // Mohawk
            'mon' => 'mn', // Mongolian
            'mri' => 'mi', // Maori
            'mvv' => 'mv', // Tagal Murut
            'mya' => 'ma', // Burmese
            'myv' => 'my', // Erzya
            'nav' => 'nv', // Navajo
            'ngu' => 'ng', // Guerrero Nahuatl
            'niu' => 'ni', // Niuean
            'nld' => 'nl', // Dutch
            'nlv' => 'nn', // Orizaba Nahuatl
            'nob' => 'nb', // Norwegian (Bokmål)
            'non' => 'no', // Old Norse
            'nya' => 'ny', // Chinyanja
            'oar' => 'oa', // Old Aramaic
            'oci' => 'oc', // Occitan
            'oji' => 'oj', // Ojibwe
            'ori' => 'or', // Odia (Oriya)
            'oss' => 'os', // Ossetian
            'osx' => 'ox', // Old Saxon
            'pag' => 'pg', // Pangasinan
            'pan' => 'pa', // Punjabi (Eastern)
            'pam' => 'pa', // Kapampangan
            'pap' => 'po', // Papiamento
            'pau' => 'pp', // Palauan
            'pfl' => 'pf', // Palatine German
            'pus' => 'pu', // Pashto
            'pol' => 'pl', // Polish
            'por' => 'pt', // Portuguese
            'que' => 'qu', // Quechua
            'rif' => 'ri', // Tarifit
            'roh' => 'rm', // Romansh
            'rom' => 'rr', // Romani
            'ron' => 'ro', // Romanian
            'rus' => 'ru', // Russian
            'sag' => 'sg', // Sango
            'san' => 'sa', // Sanskrit
            'sco' => 'sc', // Scots
            'sin' => 'si', // Sinhala
            'slk' => 'sk', // Slovak
            'slv' => 'sl', // Slovenian
            'sme' => 'sm', // Northern Sami
            'smo' => 'so', // Samoan
            'sna' => 'sn', // Shona
            'snd' => 'sd', // Sindhi
            'som' => 'so', // Somali
            'sot' => 'st', // Southern Sotho
            'spa' => 'es', // Spanish
            'sqi' => 'sq', // Albanian
            'srd' => 'ss', // Sardinian
            'srp' => 'sr', // Serbian
            'sun' => 'su', // Sundanese
            'sux' => 'sx', // Sumerian
            'swe' => 'sv', // Swedish
            'tam' => 'ta', // Tamil
            'tat' => 'tt', // Tatar
            'tel' => 'te', // Telugu
            'tgk' => 'tg', // Tajik
            'tgl' => 'tl', // Tagalog
            'tha' => 'th', // Thai
            'tkl' => 'tz', // Tokelauan
            'tir' => 'ti', // Tigrinya
            'ton' => 'to', // Tongan
            'tsn' => 'tn', // Setswana
            'tso' => 'ts', // Tsonga
            'tuk' => 'tk', // Turkmen
            'tur' => 'tr', // Turkish
            'tyv' => 'tv', // Tuvinian
            'uig' => 'ug', // Uyghur
            'ukr' => 'uk', // Ukrainian
            'umb' => 'um', // Umbundu
            'urd' => 'ur', // Urdu
            'urh' => 'uh', // Urhobo
            'uzb' => 'uz', // Uzbek
            'vec' => 've', // Venetian
            'vie' => 'vi', // Vietnamese
            'vol' => 'vo', // Volapük
            'vro' => 'vr', // Võro
            'war' => 'wr', // Waray
            'wol' => 'wo', // Wolof
            'wln' => 'wa', // Walloon
            'xho' => 'xh', // Xhosa
            'yid' => 'yi', // Yiddish
            'yor' => 'yo', // Yoruba
            'zul' => 'zu', // Zulu




            // Macrolanguages
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
     * soon in the CakePHP process, or Configure::read('Config.language')
     * won't be set and it will defeat the purpose of the memoizer $languages,
     * hitting performance down.
     *
     * @return array
     */
    public static function languagesInTatoeba()
    {
        static $languages;
        static $lastLang;

        if (class_exists('Configure')) {
            $currentLang = Configure::read('Config.language');
        } else {
            $currentLang = null;
        }
        if (!function_exists('__')) {
            function __($string) {
                return $string;
            }
        }
        if (!$languages || $currentLang != $lastLang) {
            $lastLang = $currentLang;
            $languages = array(
                'ara' => __('Arabic', true),
                'eng' => __('English', true),
                'jpn' => __('Japanese', true),
                'fra' => __('French', true),
                'deu' => __('German', true),
                'spa' => __('Spanish', true),
                'ita' => __('Italian', true),
                'vie' => __('Vietnamese', true),
                'rus' => __('Russian', true),
                'cmn' => __('Chinese (Mandarin)', true),
                'kor' => __('Korean', true),
                'nld' => __('Dutch', true),
                'heb' => __('Hebrew', true),
                'ind' => __('Indonesian', true),
                'por' => __('Portuguese', true),
                'fin' => __('Finnish', true),
                'bul' => __('Bulgarian', true),
                'ukr' => __('Ukrainian', true),
                'ces' => __('Czech', true),
                'epo' => __('Esperanto', true),
                'ell' => __('Greek', true),
                'tur' => __('Turkish', true),
                'swe' => __('Swedish', true),
                'nob' => __('Norwegian (Bokmål)', true),
                'zsm' => __('Malay', true),
                'est' => __('Estonian', true),
                'kat' => __('Georgian', true),
                'pol' => __('Polish', true), 
                'swh' => __('Swahili', true), 
                'lat' => __('Latin', true), 
                // TODO to change when shanghainese will not be the only wu dialect
                'wuu' => __('Shanghainese', true),
                'arz' => __('Egyptian Arabic', true),
                'bel' => __('Belarusian', true),
                'hun' => __('Hungarian', true),
                'isl' => __('Icelandic', true),
                'sqi' => __('Albanian', true),
                'yue' => __('Cantonese', true),
                'afr' => __('Afrikaans', true),
                'fao' => __('Faroese', true),
                'fry' => __('Frisian', true),
                'bre' => __('Breton', true),
                'ron' => __('Romanian', true),
                'uig' => __('Uyghur', true),
                'uzb' => __('Uzbek', true),
                'nno' => __('Norwegian (Nynorsk)', true),
                'srp' => __('Serbian', true),
                'tat' => __('Tatar', true),
                'yid' => __('Yiddish', true),
                'pes' => __('Persian', true),
                'nan' => __('Min Nan Chinese', true),
                'eus' => __('Basque', true),
                'slk' => __('Slovak', true),
                'dan' => __('Danish', true),
                'hye' => __('Armenian', true),
                'acm' => __('Iraqi Arabic', true),
                'san' => __('Sanskrit', true),
                'urd' => __('Urdu', true),
                'hin' => __('Hindi', true),
                'ben' => __('Bengali', true),
                'cycl' => __('CycL', true),
                'cat' => __('Catalan', true),
                'kaz' => __('Kazakh', true),
                'lvs' => __('Latvian', true),
                'bos' => __('Bosnian', true),
                'hrv' => __('Croatian', true),
                'orv' => __('Old East Slavic', true),
                'cha' => __('Chamorro', true),
                'tgl' => __('Tagalog', true),
                'que' => __('Quechua', true),
                'mon' => __('Mongolian', true),
                'lit' => __('Lithuanian', true),
                'glg' => __('Galician', true),
                'gle' => __('Irish', true),
                'ina' => __('Interlingua', true),
                'jbo' => __('Lojban', true),
                'toki' => __('Toki Pona', true),
                'ain' => __('Ainu', true),
                'scn' => __('Sicilian', true),
                'mal' => __('Malayalam', true),
                'nds' => __('Low Saxon', true),
                'tlh' => __('Klingon', true),
                'slv' => __('Slovenian', true),
                'tha' => __('Thai', true),
                'lzh' => __('Literary Chinese', true),
                'oss' => __('Ossetian', true),
                'roh' => __('Romansh', true),
                'vol' => __('Volapük', true),
                'gla' => __('Scottish Gaelic', true),
                'ido' => __('Ido', true),
                'ast' => __('Asturian', true),
                'ile' => __('Interlingue', true),
                'oci' => __('Occitan', true),
                'xal' => __('Kalmyk', true),
                'ang' => __('Old English', true),
                'kur' => __('Kurdish', true),
                'dsb' => __('Lower Sorbian', true),
                'hsb' => __('Upper Sorbian', true),
                'ksh' => __('Kölsch', true),
                'cym' => __('Welsh', true),
                'ewe' => __('Ewe', true),
                'sjn' => __('Sindarin', true),
                'tel' => __('Telugu', true),
                'tpi' => __('Tok Pisin', true),
                'qya' => __('Quenya', true),
                'nov' => __('Novial', true),
                'mri' => __('Maori', true),
                'lld' => __('Ladin', true),
                'ber' => __('Berber', true),
                'xho' => __('Xhosa', true),
                'pnb' => __('Punjabi (Western)', true),
                'mlg' => __('Malagasy', true),
                'grn' => __('Guarani', true),
                'lad' => __('Ladino', true),
                'pms' => __('Piedmontese', true),
                'avk' => __('Kotava', true),
                'mar' => __('Marathi', true),
                'tpw' => __('Old Tupi', true),
                'tgk' => __('Tajik', true),
                'prg' => __('Old Prussian',true), 
                'npi' => __('Nepali',true), 
                'mlt' => __('Maltese',true), 
                'ckt' => __('Chukchi',true), 
                'cor' => __('Cornish',true), 
                'aze' => __('Azerbaijani',true), 
                'khm' => __('Khmer',true), 
                'lao' => __('Lao',true), 
                'bod' => __('Tibetan',true), 
                'hil' => __('Hiligaynon',true), 
                'arq' => __('Algerian Arabic',true), 
                'pcd' => __('Picard',true), 
                'grc' => __('Ancient Greek',true), 
                'amh' => __('Amharic',true), 
                'awa' => __('Awadhi',true), 
                'bho' => __('Bhojpuri',true), 
                'cbk' => __('Chavacano',true), 
                'enm' => __('Middle English',true), 
                'frm' => __('Middle French',true), 
                'hat' => __('Haitian Creole',true), 
                'jdt' => __('Juhuri (Judeo-Tat)',true), 
                'kal' => __('Greenlandic',true), 
                'mhr' => __('Meadow Mari',true), 
                'nah' => __('Nahuatl',true), 
                'pdc' => __('Pennsylvania German',true), 
                'sin' => __('Sinhala',true), 
                'tuk' => __('Turkmen',true), 
                'wln' => __('Walloon',true), 
                'bak' => __('Bashkir',true), 
                'hau' => __('Hausa',true), 
                'ltz' => __('Luxembourgish',true), 
                'mgm' => __('Mambae',true), 
                'som' => __('Somali',true), 
                'zul' => __('Zulu',true), 
                'haw' => __('Hawaiian',true), 
                'kir' => __('Kyrgyz',true), 
                'mkd' => __('Macedonian',true), 
                'mrj' => __('Hill Mari',true), 
                'ppl' => __('Pipil',true), 
                'yor' => __('Yoruba',true), 
                'kin' => __('Kinyarwanda',true), 
                'shs' => __('Shuswap',true), 
                'chv' => __('Chuvash',true), 
                'lkt' => __('Lakota',true), 
                'ota' => __('Ottoman Turkish',true), 
                'sna' => __('Shona',true), 
                'mnw' => __('Mon',true), 
                'nog' => __('Nogai',true), 
                'sah' => __('Yakut',true), 
                'abk' => __('Abkhaz',true), 
                'tet' => __('Tetun',true), 
                'tam' => __('Tamil',true), 
                'udm' => __('Udmurt',true),
                'kum' => __('Kumyk',true),
                'crh' => __('Crimean Tatar',true),
                'nya' => __('Chinyanja',true),
                'liv' => __('Livonian',true),
                'nav' => __('Navajo',true),
                'chr' => __('Cherokee',true),
                'guj' => __('Gujarati',true),
                'pan' => __('Punjabi (Eastern)',true),
                'kha' => __('Khasi',true),
                'jav' => __('Javanese',true),
                'zza' => __('Zaza',true),
                'egl' => __('Emilian',true),
                'tir' => __('Tigrinya',true),
                'sme' => __('Northern Sami',true),
                'max' => __('North Moluccan Malay',true),
                'pam' => __('Kapampangan', true),
                'dtp' => __('Central Dusun',true),
                'cho' => __('Choctaw',true),
                'kzj' => __('Coastal Kadazan',true),
                'smo' => __('Samoan',true),
                'fij' => __('Fijian',true),
                'wol' => __('Wolof',true),
                'che' => __('Chechen',true),
                'sag' => __('Sango',true),
                'hif' => __('Fiji Hindi',true),
                'ton' => __('Tongan',true),
                'ngt' => __('Ngeq ',true),
                'kam' => __('Kamba',true),
                'vec' => __('Venetian',true),
                'mya' => __('Burmese',true),
                'gil' => __('Gilbertese',true),
                'myv' => __('Erzya',true),
                'niu' => __('Niuean',true),
                'vro' => __('Võro',true),
                'glv' => __('Manx',true),
                'lin' => __('Lingala',true),
                'lfn' => __('Lingua Franca Nova',true),
                'pus' => __('Pashto',true),
                'kjh' => __('Khakas',true),
                'dng' => __('Dungan',true),
                'fur' => __('Friulian',true),
                'mah' => __('Marshallese',true),
                'pfl' => __('Palatine German',true),
                'kan' => __('Kannada',true),
                'crs' => __('Seychellois Creole',true),
                'gsw' => __('Swiss German',true),
                'osx' => __('Old Saxon',true),
                'sux' => __('Sumerian',true),
                'sco' => __('Scots',true),
                'moh' => __('Mohawk',true),
                'ceb' => __('Cebuano',true),
                'lmo' => __('Lombard',true),
                'tso' => __('Tsonga',true),
                'bua' => __('Buryat',true),
                'aym' => __('Aymara',true),
                'ilo' => __('Ilocano',true),
                'kaa' => __('Karakalpak',true),
                'nlv' => __('Orizaba Nahuatl',true),
                'ngu' => __('Guerrero Nahuatl',true),
                'ady' => __('Adyghe',true),
                'brx' => __('Bodo',true),
                'gag' => __('Gagauz',true),
                'rom' => __('Romani',true),
                'lzz' => __('Laz',true),
                'fuc' => __('Pulaar',true),
                'umb' => __('Umbundu',true),
                'tkl' => __('Tokelauan',true),
                'sot' => __('Southern Sotho',true),
                'alt' => __('Southern Altai',true),
                'war' => __('Waray',true),
                'snd' => __('Sindhi',true),
                'tsn' => __('Setswana',true),
                'srd' => __('Sardinian',true),
                'pau' => __('Palauan',true),
                'gbm' => __('Garhwali',true),
                'oji' => __('Ojibwe',true),
                'lug' => __('Luganda',true),
                'hak' => __('Hakka Chinese',true),
                'bam' => __('Bambara',true),
                'arg' => __('Aragonese',true),
                'asm' => __('Assamese',true),
                'fuv' => __('Nigerian Fulfulde',true),
                'hoc' => __('Ho',true),
                'sun' => __('Sundanese',true),
                'apc' => __('North Levantine Arabic',true),
                'tyv' => __('Tuvinian',true),
                'krc' => __('Karachay-Balkar',true),
                'pap' => __('Papiamento',true),
                'non' => __('Old Norse',true),
                'ori' => __('Odia (Oriya)',true),
                'iba' => __('Iban',true),
                'oar' => __('Old Aramaic',true),
                'ary' => __('Moroccan Arabic',true),
                'cyo' => __('Cuyonon',true),
                'ibo' => __('Igbo',true),
                'csb' => __('Kashubian',true),
                'lou' => __('Louisiana Creole',true),
                'urh' => __('Urhobo',true),
                'mvv' => __('Tagal Murut',true),
                'mdf' => __('Moksha',true),
                'pag' => __('Pangasinan',true),
                'cos' => __('Corsican',true),
                'hnj' => __('Hmong Njua',true),
                'rif' => __('Tarifit',true),
                

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
     * @param string $lang ISO-639-3 code
     *
     * @return string "rtl" (right to left) or "ltr" (left to right)
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
            "ota",
            "apc",
            "oar",
            "ary",
            
        );

        if (in_array($lang, $rightToLeftLangs)) {
            $direction = "rtl";
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
        $available =& self::languagesInTatoeba();
        return isset($available[$code]);
    }
}
