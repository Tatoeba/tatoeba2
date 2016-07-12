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
            'afr' => 'af', // Afrikaans
            'amh' => 'am', // Amharic
            'ara' => 'ar', // Arabic
            'arg' => 'an', // Aragonese
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
            'bul' => 'bg', // Bulgarian
            'cat' => 'ca', // Catalan
            'ces' => 'cs', // Czech
            'cha' => 'ch', // Chamorro
            'che' => 'ce', // Chechen
            'chv' => 'cv', // Chuvash
            'cor' => 'kw', // Cornish
            'cos' => 'co', // Corsican
            'cym' => 'cy', // Welsh
            'dan' => 'da', // Danish
            'deu' => 'de', // German
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
            'gla' => 'gd', // Scottish Gaelic
            'gle' => 'ga', // Irish
            'glg' => 'gl', // Galician
            'glv' => 'gv', // Manx
            'grn' => 'gn', // Guarani
            'guj' => 'gu', // Gujarati
            'hat' => 'ht', // Haitian Creole
            'hau' => 'ha', // Hausa
            'heb' => 'he', // Hebrew
            'hin' => 'hi', // Hindi
            'hrv' => 'hr', // Croatian
            'hun' => 'hu', // Hungarian
            'hye' => 'hy', // Armenian
            'ibo' => 'ig', // Igbo
            'ido' => 'io', // Ido
            'ile' => 'ie', // Interlingue
            'ina' => 'ia', // Interlingua
            'ind' => 'id', // Indonesian
            'isl' => 'is', // Icelandic
            'ita' => 'it', // Italian
            'jav' => 'jv', // Javanese
            'jpn' => 'ja', // Japanese
            'kal' => 'kl', // Greenlandic
            'kan' => 'kn', // Kannada
            'kat' => 'ka', // Georgian
            'kaz' => 'kk', // Kazakh
            'khm' => 'km', // Khmer
            'kin' => 'rw', // Kinyarwanda
            'kir' => 'ky', // Kyrgyz
            'kor' => 'ko', // Korean
            'kur' => 'ku', // Kurdish
            'lao' => 'lo', // Lao
            'lat' => 'la', // Latin
            'lin' => 'ln', // Lingala
            'lit' => 'lt', // Lithuanian
            'ltz' => 'lb', // Luxembourgish
            'lug' => 'lg', // Luganda
            'mah' => 'mh', // Marshallese
            'mal' => 'ml', // Malayalam
            'mar' => 'mr', // Marathi
            'mkd' => 'mk', // Macedonian
            'mlg' => 'mg', // Malagasy
            'mlt' => 'mt', // Maltese
            'mon' => 'mn', // Mongolian
            'mri' => 'mi', // Maori
            'mya' => 'my', // Burmese
            'nau' => 'na', // Nauruan
            'nav' => 'nv', // Navajo
            'nld' => 'nl', // Dutch
            'nob' => 'nb', // Norwegian (Bokmål)
            'nya' => 'ny', // Chinyanja
            'oci' => 'oc', // Occitan
            'oji' => 'oj', // Ojibwe
            'ori' => 'or', // Odia (Oriya)
            'oss' => 'os', // Ossetian
            'pan' => 'pa', // Punjabi (Eastern)
            'pus' => 'ps', // Pashto
            'pol' => 'pl', // Polish
            'por' => 'pt', // Portuguese
            'que' => 'qu', // Quechua
            'roh' => 'rm', // Romansh
            'ron' => 'ro', // Romanian
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
            'ssw' => 'ss', // Swazi
            'sot' => 'st', // Southern Sotho
            'spa' => 'es', // Spanish
            'sqi' => 'sq', // Albanian
            'srd' => 'sc', // Sardinian
            'srp' => 'sr', // Serbian
            'sun' => 'su', // Sundanese
            'swe' => 'sv', // Swedish
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
            'uig' => 'ug', // Uyghur
            'ukr' => 'uk', // Ukrainian
            'urd' => 'ur', // Urdu
            'uzb' => 'uz', // Uzbek
            'vie' => 'vi', // Vietnamese
            'vol' => 'vo', // Volapük
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
                'ara' => __d('languages', 'Arabic', true),
                'eng' => __d('languages', 'English', true),
                'jpn' => __d('languages', 'Japanese', true),
                'fra' => __d('languages', 'French', true),
                'deu' => __d('languages', 'German', true),
                'spa' => __d('languages', 'Spanish', true),
                'ita' => __d('languages', 'Italian', true),
                'vie' => __d('languages', 'Vietnamese', true),
                'rus' => __d('languages', 'Russian', true),
                'cmn' => __d('languages', 'Chinese (Mandarin)', true),
                'kor' => __d('languages', 'Korean', true),
                'nld' => __d('languages', 'Dutch', true),
                'heb' => __d('languages', 'Hebrew', true),
                'ind' => __d('languages', 'Indonesian', true),
                'por' => __d('languages', 'Portuguese', true),
                'fin' => __d('languages', 'Finnish', true),
                'bul' => __d('languages', 'Bulgarian', true),
                'ukr' => __d('languages', 'Ukrainian', true),
                'ces' => __d('languages', 'Czech', true),
                'epo' => __d('languages', 'Esperanto', true),
                'ell' => __d('languages', 'Greek', true),
                'tur' => __d('languages', 'Turkish', true),
                'swe' => __d('languages', 'Swedish', true),
                'nob' => __d('languages', 'Norwegian (Bokmål)', true),
                'zsm' => __d('languages', 'Malay', true),
                'est' => __d('languages', 'Estonian', true),
                'kat' => __d('languages', 'Georgian', true),
                'pol' => __d('languages', 'Polish', true),
                'swh' => __d('languages', 'Swahili', true),
                'lat' => __d('languages', 'Latin', true),
                // TODO to change when shanghainese will not be the only wu dialect
                'wuu' => __d('languages', 'Shanghainese', true),
                'arz' => __d('languages', 'Egyptian Arabic', true),
                'bel' => __d('languages', 'Belarusian', true),
                'hun' => __d('languages', 'Hungarian', true),
                'isl' => __d('languages', 'Icelandic', true),
                'sqi' => __d('languages', 'Albanian', true),
                'yue' => __d('languages', 'Cantonese', true),
                'afr' => __d('languages', 'Afrikaans', true),
                'fao' => __d('languages', 'Faroese', true),
                'fry' => __d('languages', 'Frisian', true),
                'bre' => __d('languages', 'Breton', true),
                'ron' => __d('languages', 'Romanian', true),
                'uig' => __d('languages', 'Uyghur', true),
                'uzb' => __d('languages', 'Uzbek', true),
                'nno' => __d('languages', 'Norwegian (Nynorsk)', true),
                'srp' => __d('languages', 'Serbian', true),
                'tat' => __d('languages', 'Tatar', true),
                'yid' => __d('languages', 'Yiddish', true),
                'pes' => __d('languages', 'Persian', true),
                'nan' => __d('languages', 'Min Nan Chinese', true),
                'eus' => __d('languages', 'Basque', true),
                'slk' => __d('languages', 'Slovak', true),
                'dan' => __d('languages', 'Danish', true),
                'hye' => __d('languages', 'Armenian', true),
                'acm' => __d('languages', 'Iraqi Arabic', true),
                'san' => __d('languages', 'Sanskrit', true),
                'urd' => __d('languages', 'Urdu', true),
                'hin' => __d('languages', 'Hindi', true),
                'ben' => __d('languages', 'Bengali', true),
                'cycl' => __d('languages', 'CycL', true),
                'cat' => __d('languages', 'Catalan', true),
                'kaz' => __d('languages', 'Kazakh', true),
                'lvs' => __d('languages', 'Latvian', true),
                'bos' => __d('languages', 'Bosnian', true),
                'hrv' => __d('languages', 'Croatian', true),
                'orv' => __d('languages', 'Old East Slavic', true),
                'cha' => __d('languages', 'Chamorro', true),
                'tgl' => __d('languages', 'Tagalog', true),
                'que' => __d('languages', 'Quechua', true),
                'mon' => __d('languages', 'Mongolian', true),
                'lit' => __d('languages', 'Lithuanian', true),
                'glg' => __d('languages', 'Galician', true),
                'gle' => __d('languages', 'Irish', true),
                'ina' => __d('languages', 'Interlingua', true),
                'jbo' => __d('languages', 'Lojban', true),
                'toki' => __d('languages', 'Toki Pona', true),
                'ain' => __d('languages', 'Ainu', true),
                'scn' => __d('languages', 'Sicilian', true),
                'mal' => __d('languages', 'Malayalam', true),
                'nds' => __d('languages', 'Low Saxon', true),
                'tlh' => __d('languages', 'Klingon', true),
                'slv' => __d('languages', 'Slovenian', true),
                'tha' => __d('languages', 'Thai', true),
                'lzh' => __d('languages', 'Literary Chinese', true),
                'oss' => __d('languages', 'Ossetian', true),
                'roh' => __d('languages', 'Romansh', true),
                'vol' => __d('languages', 'Volapük', true),
                'gla' => __d('languages', 'Scottish Gaelic', true),
                'ido' => __d('languages', 'Ido', true),
                'ast' => __d('languages', 'Asturian', true),
                'ile' => __d('languages', 'Interlingue', true),
                'oci' => __d('languages', 'Occitan', true),
                'xal' => __d('languages', 'Kalmyk', true),
                'ang' => __d('languages', 'Old English', true),
                'kur' => __d('languages', 'Kurdish', true),
                'dsb' => __d('languages', 'Lower Sorbian', true),
                'hsb' => __d('languages', 'Upper Sorbian', true),
                'ksh' => __d('languages', 'Kölsch', true),
                'cym' => __d('languages', 'Welsh', true),
                'ewe' => __d('languages', 'Ewe', true),
                'sjn' => __d('languages', 'Sindarin', true),
                'tel' => __d('languages', 'Telugu', true),
                'tpi' => __d('languages', 'Tok Pisin', true),
                'qya' => __d('languages', 'Quenya', true),
                'nov' => __d('languages', 'Novial', true),
                'mri' => __d('languages', 'Maori', true),
                'lld' => __d('languages', 'Ladin', true),
                'ber' => __d('languages', 'Berber', true),
                'xho' => __d('languages', 'Xhosa', true),
                'pnb' => __d('languages', 'Punjabi (Western)', true),
                'mlg' => __d('languages', 'Malagasy', true),
                'grn' => __d('languages', 'Guarani', true),
                'lad' => __d('languages', 'Ladino', true),
                'pms' => __d('languages', 'Piedmontese', true),
                'avk' => __d('languages', 'Kotava', true),
                'mar' => __d('languages', 'Marathi', true),
                'tpw' => __d('languages', 'Old Tupi', true),
                'tgk' => __d('languages', 'Tajik', true),
                'prg' => __d('languages', 'Old Prussian', true),
                'npi' => __d('languages', 'Nepali', true),
                'mlt' => __d('languages', 'Maltese', true),
                'ckt' => __d('languages', 'Chukchi', true),
                'cor' => __d('languages', 'Cornish', true),
                'aze' => __d('languages', 'Azerbaijani', true),
                'khm' => __d('languages', 'Khmer', true),
                'lao' => __d('languages', 'Lao', true),
                'bod' => __d('languages', 'Tibetan', true),
                'hil' => __d('languages', 'Hiligaynon', true),
                'arq' => __d('languages', 'Algerian Arabic', true),
                'pcd' => __d('languages', 'Picard', true),
                'grc' => __d('languages', 'Ancient Greek', true),
                'amh' => __d('languages', 'Amharic', true),
                'awa' => __d('languages', 'Awadhi', true),
                'bho' => __d('languages', 'Bhojpuri', true),
                'cbk' => __d('languages', 'Chavacano', true),
                'enm' => __d('languages', 'Middle English', true),
                'frm' => __d('languages', 'Middle French', true),
                'hat' => __d('languages', 'Haitian Creole', true),
                'jdt' => __d('languages', 'Juhuri (Judeo-Tat)', true),
                'kal' => __d('languages', 'Greenlandic', true),
                'mhr' => __d('languages', 'Meadow Mari', true),
                'nah' => __d('languages', 'Nahuatl', true),
                'pdc' => __d('languages', 'Pennsylvania German', true),
                'sin' => __d('languages', 'Sinhala', true),
                'tuk' => __d('languages', 'Turkmen', true),
                'wln' => __d('languages', 'Walloon', true),
                'bak' => __d('languages', 'Bashkir', true),
                'hau' => __d('languages', 'Hausa', true),
                'ltz' => __d('languages', 'Luxembourgish', true),
                'mgm' => __d('languages', 'Mambae', true),
                'som' => __d('languages', 'Somali', true),
                'zul' => __d('languages', 'Zulu', true),
                'haw' => __d('languages', 'Hawaiian', true),
                'kir' => __d('languages', 'Kyrgyz', true),
                'mkd' => __d('languages', 'Macedonian', true),
                'mrj' => __d('languages', 'Hill Mari', true),
                'ppl' => __d('languages', 'Pipil', true),
                'yor' => __d('languages', 'Yoruba', true),
                'kin' => __d('languages', 'Kinyarwanda', true),
                'shs' => __d('languages', 'Shuswap', true),
                'chv' => __d('languages', 'Chuvash', true),
                'lkt' => __d('languages', 'Lakota', true),
                'ota' => __d('languages', 'Ottoman Turkish', true),
                'sna' => __d('languages', 'Shona', true),
                'mnw' => __d('languages', 'Mon', true),
                'nog' => __d('languages', 'Nogai', true),
                'sah' => __d('languages', 'Yakut', true),
                'abk' => __d('languages', 'Abkhaz', true),
                'tet' => __d('languages', 'Tetun', true),
                'tam' => __d('languages', 'Tamil', true),
                'udm' => __d('languages', 'Udmurt', true),
                'kum' => __d('languages', 'Kumyk', true),
                'crh' => __d('languages', 'Crimean Tatar', true),
                'nya' => __d('languages', 'Chinyanja', true),
                'liv' => __d('languages', 'Livonian', true),
                'nav' => __d('languages', 'Navajo', true),
                'chr' => __d('languages', 'Cherokee', true),
                'guj' => __d('languages', 'Gujarati', true),
                'pan' => __d('languages', 'Punjabi (Eastern)', true),
                'kha' => __d('languages', 'Khasi', true),
                'jav' => __d('languages', 'Javanese', true),
                'zza' => __d('languages', 'Zaza', true),
                'egl' => __d('languages', 'Emilian', true),
                'tir' => __d('languages', 'Tigrinya', true),
                'sme' => __d('languages', 'Northern Sami', true),
                'max' => __d('languages', 'North Moluccan Malay', true),
                'pam' => __d('languages', 'Kapampangan', true),
                'dtp' => __d('languages', 'Central Dusun', true),
                'cho' => __d('languages', 'Choctaw', true),
                'kzj' => __d('languages', 'Coastal Kadazan', true),
                'smo' => __d('languages', 'Samoan', true),
                'fij' => __d('languages', 'Fijian', true),
                'wol' => __d('languages', 'Wolof', true),
                'che' => __d('languages', 'Chechen', true),
                'sag' => __d('languages', 'Sango', true),
                'hif' => __d('languages', 'Fiji Hindi', true),
                'ton' => __d('languages', 'Tongan', true),
                'ngt' => __d('languages', 'Ngeq ', true),
                'kam' => __d('languages', 'Kamba', true),
                'vec' => __d('languages', 'Venetian', true),
                'mya' => __d('languages', 'Burmese', true),
                'gil' => __d('languages', 'Gilbertese', true),
                'myv' => __d('languages', 'Erzya', true),
                'niu' => __d('languages', 'Niuean', true),
                'vro' => __d('languages', 'Võro', true),
                'glv' => __d('languages', 'Manx', true),
                'lin' => __d('languages', 'Lingala', true),
                'lfn' => __d('languages', 'Lingua Franca Nova', true),
                'pus' => __d('languages', 'Pashto', true),
                'kjh' => __d('languages', 'Khakas', true),
                'dng' => __d('languages', 'Dungan', true),
                'fur' => __d('languages', 'Friulian', true),
                'mah' => __d('languages', 'Marshallese', true),
                'pfl' => __d('languages', 'Palatine German', true),
                'kan' => __d('languages', 'Kannada', true),
                'crs' => __d('languages', 'Seychellois Creole', true),
                'gsw' => __d('languages', 'Swiss German', true),
                'osx' => __d('languages', 'Old Saxon', true),
                'sux' => __d('languages', 'Sumerian', true),
                'sco' => __d('languages', 'Scots', true),
                'moh' => __d('languages', 'Mohawk', true),
                'ceb' => __d('languages', 'Cebuano', true),
                'lmo' => __d('languages', 'Lombard', true),
                'tso' => __d('languages', 'Tsonga', true),
                'bua' => __d('languages', 'Buryat', true),
                'aym' => __d('languages', 'Aymara', true),
                'ilo' => __d('languages', 'Ilocano', true),
                'kaa' => __d('languages', 'Karakalpak', true),
                'nlv' => __d('languages', 'Orizaba Nahuatl', true),
                'ngu' => __d('languages', 'Guerrero Nahuatl', true),
                'ady' => __d('languages', 'Adyghe', true),
                'brx' => __d('languages', 'Bodo', true),
                'gag' => __d('languages', 'Gagauz', true),
                'rom' => __d('languages', 'Romani', true),
                'lzz' => __d('languages', 'Laz', true),
                'fuc' => __d('languages', 'Pulaar', true),
                'umb' => __d('languages', 'Umbundu', true),
                'tkl' => __d('languages', 'Tokelauan', true),
                'sot' => __d('languages', 'Southern Sotho', true),
                'alt' => __d('languages', 'Southern Altai', true),
                'war' => __d('languages', 'Waray', true),
                'snd' => __d('languages', 'Sindhi', true),
                'tsn' => __d('languages', 'Setswana', true),
                'srd' => __d('languages', 'Sardinian', true),
                'pau' => __d('languages', 'Palauan', true),
                'gbm' => __d('languages', 'Garhwali', true),
                'oji' => __d('languages', 'Ojibwe', true),
                'lug' => __d('languages', 'Luganda', true),
                'hak' => __d('languages', 'Hakka Chinese', true),
                'bam' => __d('languages', 'Bambara', true),
                'arg' => __d('languages', 'Aragonese', true),
                'asm' => __d('languages', 'Assamese', true),
                'fuv' => __d('languages', 'Nigerian Fulfulde', true),
                'hoc' => __d('languages', 'Ho', true),
                'sun' => __d('languages', 'Sundanese', true),
                'apc' => __d('languages', 'North Levantine Arabic', true),
                'tyv' => __d('languages', 'Tuvinian', true),
                'krc' => __d('languages', 'Karachay-Balkar', true),
                'pap' => __d('languages', 'Papiamento', true),
                'non' => __d('languages', 'Old Norse', true),
                'ori' => __d('languages', 'Odia (Oriya)', true),
                'iba' => __d('languages', 'Iban', true),
                'oar' => __d('languages', 'Old Aramaic', true),
                'ary' => __d('languages', 'Moroccan Arabic', true),
                'cyo' => __d('languages', 'Cuyonon', true),
                'ibo' => __d('languages', 'Igbo', true),
                'csb' => __d('languages', 'Kashubian', true),
                'lou' => __d('languages', 'Louisiana Creole', true),
                'urh' => __d('languages', 'Urhobo', true),
                'mvv' => __d('languages', 'Tagal Murut', true),
                'mdf' => __d('languages', 'Moksha', true),
                'pag' => __d('languages', 'Pangasinan', true),
                'cos' => __d('languages', 'Corsican', true),
                'hnj' => __d('languages', 'Hmong Njua (Green)', true),
                'rif' => __d('languages', 'Tarifit', true),
                'nch' => __d('languages', 'Central Huasteca Nahuatl', true),
                'kek' => __d('languages', "Kekchi (Q'eqchi')", true),
                'ssw' => __d('languages', 'Swazi', true),
                'ban' => __d('languages', 'Balinese', true),
                'aii' => __d('languages', 'Assyrian Neo-Aramaic', true),
                'tvl' => __d('languages', 'Tuvaluan', true),
                'kxi' => __d('languages', 'Keningau Murut', true),
                'bvy' => __d('languages', 'Baybayanon', true),
                'mfe' => __d('languages', 'Morisyen', true),
                'mww' => __d('languages', 'Hmong Daw (White)', true),
                'bcl' => __d('languages', 'Bikol (Central)', true),
                'nau' => __d('languages', 'Nauruan', true),
                'zlm' => __d('languages', 'Malay (Vernacular)', true),
                'nst' => __d('languages', 'Naga (Tangshang)', true),
                'quc' => __d('languages', "K'iche'", true),
                'afb' => __d('languages', 'Arabic (Gulf)', true),
                'min' => __d('languages', 'Minangkabau', true),
                'tmw' => __d('languages', 'Temuan', true),
                'cjy' => __d('languages', 'Chinese (Jin)', true),
                'mai' => __d('languages', 'Maithili', true),
                'mad' => __d('languages', 'Madurese', true),
                'bjn' => __d('languages', 'Banjar', true),
                'got' => __d('languages', 'Gothic', true),
                'hsn' => __d('languages', 'Xiang Chinese', true),
                'gan' => __d('languages', 'Gan Chinese', true),
                'bar' => __d('languages', 'Bavarian', true),
                'tzl' => __d('languages', 'Talossan', true),
                'sgs' => __d('languages', 'Samogitian', true),
                'ldn' => __d('languages', 'Láadan', true),
                'dws' => __d('languages', 'Dutton World Speedwords', true),
                'afh' => __d('languages', 'Afrihili', true),
                'krl' => __d('languages', 'Karelian', true),
                'vep' => __d('languages', 'Veps', true),
                'rue' => __d('languages', 'Rusyn', true),
                'tah' => __d('languages', 'Tahitian', true),
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
            "aii",
            "afb",
            "pus"
            "snd"
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
