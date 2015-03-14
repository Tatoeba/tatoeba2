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

App::import('Model', 'CurrentUser');

/**
 * Helper for languages
 *
 * @category Default
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class LanguagesHelper extends AppHelper
{
    public $helpers = array('Html');
    
    // Note that many languages with an ISO 639-3 code do not have an ISO 639-1 code.
    // Example: Khasi
    private $iso639_3_to_iso639_1 = array(
        'abk' => 'ab', // Abkhaz
        'afr' => 'af', // Afrikaans
        'amh' => 'am', // Amharic
        'ara' => 'ar', // Arabic
        'aze' => 'az', // Azerbaijani
        'bak' => 'ba', // Bashkir
        'bel' => 'be', // Belarusian
        'ben' => 'bn', // Bengali
        'bod' => 'bo', // Tibetan
        'bos' => 'bs', // Bosnian
        'bre' => 'br', // Breton
        'bul' => 'bg', // Bulgarian
        'cat' => 'ca', // Catalan
        'ces' => 'cs', // Czech
        'cha' => 'ch', // Chamorro
        'chv' => 'cv', // Chuvash
        'cor' => 'kw', // Cornish
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
        'fin' => 'fi', // Finnish
        'fra' => 'fr', // French
        'fry' => 'fy', // Frisian
        'gla' => 'gd', // Scottish Gaelic
        'gle' => 'ga', // Irish
        'glg' => 'gl', // Galician
        'grn' => 'gn', // Guarani
        'guj' => 'gu', // Gujarati
        'hat' => 'ht', // Haitian Creole
        'hau' => 'ha', // Hausa
        'heb' => 'he', // Hebrew
        'hin' => 'hi', // Hindi
        'hrv' => 'hr', // Croatian
        'hun' => 'hu', // Hungarian
        'hye' => 'hy', // Armenian
        'ido' => 'io', // Ido
        'ile' => 'ie', // Interlingue
        'ina' => 'ia', // Interlingua
        'ind' => 'id', // Indonesian
        'isl' => 'is', // Icelandic
        'ita' => 'it', // Italian
        'jav' => 'jv', // Javanese
        'jpn' => 'ja', // Japanese
        'kal' => 'kl', // Greenlandic
        'kat' => 'ka', // Georgian
        'kaz' => 'kk', // Kazakh
        'khm' => 'km', // Khmer
        'kin' => 'rw', // Kinyarwanda
        'kir' => 'ky', // Kyrgyz
        'kor' => 'ko', // Korean
        'kur' => 'ku', // Kurdish
        'lao' => 'lo', // Lao
        'lat' => 'la', // Latin
        'lit' => 'lt', // Lithuanian
        'ltz' => 'lb', // Luxembourgish
        'mal' => 'ml', // Malayalam
        'mar' => 'mr', // Marathi
        'mkd' => 'mk', // Macedonian
        'mlg' => 'mg', // Malagasy
        'mlt' => 'mt', // Maltese
        'mon' => 'mn', // Mongolian
        'mri' => 'mi', // Maori
        'nav' => 'nv', // Navajo
        'nld' => 'nl', // Dutch
        'nob' => 'nb', // Norwegian (Bokmål)
        'nya' => 'ny', // Chinyanja
        'oci' => 'oc', // Occitan
        'oss' => 'os', // Ossetian
        'pan' => 'pa', // Punjabi (Eastern)
        'pol' => 'pl', // Polish
        'por' => 'pt', // Portuguese
        'que' => 'qu', // Quechua
        'roh' => 'rm', // Romansh
        'ron' => 'ro', // Romanian
        'rus' => 'ru', // Russian
        'san' => 'sa', // Sanskrit
        'sin' => 'si', // Sinhala
        'slk' => 'sk', // Slovak
        'slv' => 'sl', // Slovenian
        'sna' => 'sn', // Shona
        'som' => 'so', // Somali
        'spa' => 'es', // Spanish
        'sqi' => 'sq', // Albanian
        'srp' => 'sr', // Serbian
        'swe' => 'sv', // Swedish
        'tam' => 'ta', // Tamil
        'tat' => 'tt', // Tatar
        'tel' => 'te', // Telugu
        'tgk' => 'tg', // Tajik
        'tgl' => 'tl', // Tagalog
        'tha' => 'th', // Thai
        'tuk' => 'tk', // Turkmen
        'tur' => 'tr', // Turkish
        'uig' => 'ug', // Uyghur
        'ukr' => 'uk', // Ukrainian
        'urd' => 'ur', // Urdu
        'uzb' => 'uz', // Uzbek
        'vie' => 'vi', // Vietnamese
        'vol' => 'vo', // Volapük
        'wln' => 'wa', // Walloon
        'xho' => 'xh', // Xhosa
        'yid' => 'yi', // Yiddish
        'yor' => 'yo', // Yoruba
        'zul' => 'zu', // Zulu

        /* "Temporary" hack for Firefox.
         * Firefox only renders fonts as Chinese when lang="zh",
         * but not when lang="cmn", whereas "zh" is actually a
         * macrolanguage that includes, among other languages,
         * "cmn". The same goes for other Chinese languages.
         */
        'cmn' => 'zh',
        'yue' => 'zh',
        'wuu' => 'zh',
        'lzh' => 'zh',
    );

    /* Memoization of languages code and their localized names */
    private $__languages_to_format;
    private $__languages_alone;

    private function langAsAlone($name)
    {
        return format(
        /* @translators: this special string allows you to tweak how language
           names are displayed when they are not used inside another string.
           For instance, in language lists, on flag mouseover or on the stats
           page. You may translate this string using a declension modifier,
           for instance {language.alone} */
            __('{language}', true),
            array('language' => $name)
        );
    }

    /**
     * Return array of languages in Tatoeba
     *
     * @return array
     */
    private function _onlyLanguagesArray()
    {
        if (!$this->__languages_to_format) {
            $this->__languages_to_format = array(
                'ara' => __('Arabic', true),
                'eng' => __('English', true),
                'jpn' => __('Japanese', true),
                'fra' => __('French', true),
                'deu' => __('German', true),
                'spa' => __('Spanish', true),
                'ita' => __('Italian', true),
                'vie' => __('Vietnamese', true),
                'rus' => __('Russian', true),
                'cmn' => __('Chinese', true),
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
                'non' => __('Norwegian (Nynorsk)', true),
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
            'jav' => __('Javanese',true), //@lang 
            );
        }
        return $this->__languages_to_format;
    }

    public function onlyLanguagesArray()
    {
        if (!$this->__languages_alone) {
            $this->__languages_alone = array_map(
                array($this, 'langAsAlone'),
                $this->_onlyLanguagesArray()
            );
            if (class_exists('Collator')) {
                $i18nLang = Configure::read('Config.language');
                $coll = new Collator($this->i18nCodeToISO($i18nLang));
                $coll->asort($this->__languages_alone);
            } else {
                asort($this->__languages_alone);
            }
        }
        return $this->__languages_alone;
    }


    /**
     * Returns array of languages set in the user's options.
     */
    public function userLanguagesArray()
    {
        $languages = $this->onlyLanguagesArray();

        if (CurrentUser::isMember()) {
            $userLangs = CurrentUser::getLanguages();
            if (!empty($userLangs)) {
                $filteredLangs = array();
                foreach($userLangs as $langCode) {
                    $filteredLangs[$langCode] = $languages[$langCode];
                }
                $languages = $filteredLangs;
            }
        }

        return $languages;
    }

    /**
     * Return array of languages in Tatoeba + all languages, formatted
     * like it's displayed when alone on the UI (on lists or flags).
     *
     * @return array
     */
    public function languagesArrayAlone()
    {
        $languages = $this->onlyLanguagesArray();
        array_unshift($languages, array(
            'und' => $this->langAsAlone(__('All languages', true))
        ));
        return $languages;
    }

    /**
     * Return array of languages in Tatoeba + all languages, ready
     * to be used inside a format() call. You MUST use the return
     * value as a variable inside a format() call. If not,
     * use languagesArrayAlone() instead.
     * 
     * @return array
     */
    public function languagesArrayToFormat()
    {
        $languages = $this->_onlyLanguagesArray();

        // Can't use 'any' as it's the code for anyin language.
        // Only 'und' is used for "undefined".
        array_unshift($languages, array('und' => __('All languages', true)));

        return $languages;
    }

    /**
     * Return array of languages in Tatoeba. + 'unknown language'
     *
     * @return array
     */
    public function unknownLanguagesArray()
    {
        $languages = $this->onlyLanguagesArray();

        // Can't use 'any' as it's the code for anyin language.
        // Only 'und' is used for "undefined".
        // TODO xxx to be remplace by the code for 'unknown'
        array_unshift($languages, array('unknown' => __('unknown language', true)));

        return $languages;
    }


    /**
     * Return array of languages in Tatoeba + 'other language'. 'other language' is
     * used to set the language to null, in case there was a misdetection and the
     * language in which the user is writing is not supported yet.
     *
     * @return array
     */
    public function otherLanguagesArray()
    {
        $languages = $this->onlyLanguagesArray();

        array_unshift($languages, array('' => __('other language', true)));

        return $languages;
    }


    /**
     * Return array of language + "auto"
     * used to know if the user want the language of a contribution
     * to be manualy set or auto detect
     *
     * @return array
     */

    public function translationsArray()
    {
        $languages = $this->userLanguagesArray();

        array_unshift($languages, array('auto' => __('Auto detect', true)));
        return $languages;
    }


    /**
     * Return array of languages, with "None" and "All languages" options.
     * Applies to a positive phrase (for example, "Show translations in"). 
     *
     * @return array
     */
    public function languagesArrayForPositiveLists()
    {
        $languages = $this->onlyLanguagesArray();

        array_unshift(
            $languages,
            array(
                'none' => __('None', true),
                'und' => __('All languages', true)
            )
        );

        return $languages;
    }


    /**
    * Return array of languages, with "--" and "Any languages" options.
    * Applies to a negative phrase (for example, "Not directly translated into"). 
    *
    * @return array
    */
    public function languagesArrayForNegativeLists()
    {
        $languages = $this->onlyLanguagesArray();
        
        array_unshift(
            $languages, 
            array(
                'none' => '—',
                'und' => __('Any language', true)
            )
        );
        
        return $languages;
    }
    
    
    /**
     * Return array of languages with, "None" option.
     *
     * @return array
     */
    public function languagesArrayWithNone()
    {
        $languages = $this->onlyLanguagesArray();

        array_unshift(
            $languages,
            array(
                'none' => __('None', true)
            )
        );

        return $languages;
    }


    /**
     * Return array of languages in which you can search.
     *
     * @return array
     */
    public function getSearchableLanguagesArray()
    {
        $languages = $this->onlyLanguagesArray();
        array_unshift($languages, array('und' => __('Any', true)));

        return $languages;
    }

    /**
     * Return name of the language from the ISO code, formatted
     * like it's displayed when alone on the UI (on lists or flags).
     *
     * @param string $code ISO-639-3 code.
     *
     * @return string
     */
    public function codeToNameAlone($code) {
        return $this->langAsAlone($this->codeToNameToFormat($code));
    }

    /**
     * Return name of the language from the ISO code, ready to
     * be used inside a format() call. You MUST use the return
     * value as a variable inside a format() call. If not,
     * use codeToNameAlone() instead.
     *
     * @param string $code ISO-639-3 code.
     *
     * @return string
     */
    public function codeToNameToFormat($code)
    {
        $languages = $this->languagesArrayToFormat();
        if (isset($languages["$code"])) {
            return $languages["$code"];
        } else {
            return __('unknown', true);
        }
    }

    /**
     * Return number of languages
     *
     * @return int
     */

    public function getNumberOfLanguages()
    {
        $languages = $this->onlyLanguagesArray();
        $numberOfLanguages = count($languages);
        return $numberOfLanguages;
    }


    /**
     * Get the direction (right to left or left to right) of a language
     *
     * @param string $lang ISO-639-3 code
     *
     * @return string "rtl" (right to left) or "ltr" (left to right)
     */
    public function getLanguageDirection($lang) {

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
            "ota"
        );

        if (in_array($lang, $rightToLeftLangs)) {
            $direction = "rtl";
        }

        return $direction;
    }


    /**
     * Display flag and number of sentences in the "sentences stats" block.
     *
     * @param string $langCode          Language code.
     * @param int    $numberOfSentences Number of sentences.
     *
     * @return void
     */
    function stat($langCode, $numberOfSentences, $link)
    {
        $flagImage = $this->icon(
            $langCode,
            array(
                'width' => 30,
                'height' => 20
            )
        );
        $numberOfSentencesHtml = '<span class="total">'.$numberOfSentences.'</span>';

        if (empty($langCode)) {
            $langCode = 'unknown';
        }
        $linkToAllSentences = $this->Html->link(
            $flagImage . $numberOfSentencesHtml,
            $link,
            array(
                "escape" => false
            ),
            null
        );

        ?>
        <li class="stat" title="<?php echo $this->codeToNameAlone($langCode); ?>">
        <?php echo $linkToAllSentences; ?>
        </li>
        <?php
    }


    /**
     * Convert language interface code into ISO code.
     *
     * @param string $code Interface language code.
     *
     * @return void
     */
    public function i18nCodeToISO ($code) {
        $languages = array(
            'bel' => 'bel',
            'chi' => 'cmn',
            'deu' => 'deu',
            'eng' => 'eng',
            'epo' => 'epo',
            'fre' => 'fra',
            'ita' => 'ita',
            'jpn' => 'jpn',
            'pol' => 'pol',
            'pt_BR' => 'por',
            'gre'  => 'ell',
            'rus' => 'rus',
            'spa' => 'spa',
            'ara' => 'ara',
            'eus' => 'eus',
            'fin' => 'fin',
            'hun' => 'hun',
            'tgl' => 'tgl',
            'mar' => 'mar'
        );

        if (isset($languages["$code"])) {
            return $languages["$code"];
        } else {
            return 'unknown';
        }
    }


    /**
     * Display language icon.
     *
     * @param string $lang    Language code.
     * @param array  $options Options for Html::image().
     *
     * @return void
     */
    public function icon($lang, $options)
    {
        if (empty($lang)) {
            $lang = 'unknown';
        }

        $options["title"] = $this->codeToNameAlone($lang);
        $options["alt"] = $lang;

        return $this->Html->image(
            IMG_PATH . 'flags/'.$lang.'.png',
            $options
        );
    }

    /**
     * Returns the lang HTML attribute giving an ISO-639-3 code.
     *
     * @param string $code    ISO-639-3 language code.
     * @param string $script  ISO 15924 script.
     *
     * @return string lang HTML attribute compliant string.
     */
    public function langAttribute($code, $script = '')
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
        if (isset($this->iso639_3_to_iso639_1[$code])) {
            $code = $this->iso639_3_to_iso639_1[$code];
        }
        return $code.$script;
    }

    public function tagWithLang($tag, $lang, $text, $options = array(), $script = '')
    {
        $direction = empty($lang) ? 'auto' : $this->getLanguageDirection($lang);
        $options = array_merge(
            array(
                'lang' => $this->langAttribute($lang, $script),
                'dir'  => $direction,
                'escape' => true,
            ),
            $options
        );
        return $this->Html->tag($tag, $text, $options);
    }
}
