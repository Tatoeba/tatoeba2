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
    
    /**
     * Return array of languages in Tatoeba
     *
     * @return array
     */

    public function onlyLanguagesArray()
    {
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
            'ell' => __('Modern Greek', true),
            'tur' => __('Turkish', true),
            'swe' => __('Swedish', true),
            'nob' => __('Norwegian (Bokmål)', true),
            'zsm' => __('Malay', true),
            'est' => __('Estonian', true),
            'kat' => __('Georgian', true),
            'pol' => __('Polish', true), 
            'swh' => __('Swahili', true), 
            'lat' => __('Latin', true), 
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
            'tat' => __('Tartar', true),
            'yid' => __('Yiddish', true),
            'pes' => __('Persian', true),
            'nan' => __('Teochew', true),
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
            // TODO to change when shanghainese will not be the only wu dialect
        );
        asort($languages);
        
        return $languages;
    }

    /** 
     * Return array of languages in Tatoeba. + all languages
     *
     * @return array
     */
    public function languagesArray()
    {
        $languages = $this->onlyLanguagesArray();
        
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
     * Return array of language + "auto"
     * used to know if the user want the language of a contribution
     * to be manualy set or auto detect
     *
     * @return array
     */

    public function translationsArray()
    {
        $languages = $this->onlyLanguagesArray();
        
        array_unshift($languages, array('auto' => __('Auto detect', true)));
        return $languages;
    }
    
    
    /**
     * Return array of languages, with "None" and "All languages" options.
     *
     * @return array
     */
    public function languagesArrayForLists()
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
            'ell' => __('Modern Greek', true),
            'tur' => __('Turkish', true),
            'swe' => __('Swedish', true),
            'nob' => __('Norwegian (Bokmål)', true),
            'zsm' => __('Malay', true),
            'est' => __('Estonian', true),
            'kat' => __('Georgian', true),
            'pol' => __('Polish', true), 
            'swh' => __('Swahili', true), 
            'lat' => __('Latin', true), 
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
            'tat' => __('Tartar', true),
            'yid' => __('Yiddish', true),
        );
        asort($languages);
        array_unshift($languages, array('und' => __('Any', true)));

        return $languages;
    }
    
    /** 
     * Return name of the language from the ISO code.
     *
     * @param string $code ISO-639-3 code.
     *
     * @return string
     */
    public function codeToName($code)
    {
        $languages = $this->languagesArray();
        if (isset($languages["$code"])) {
            return $languages["$code"];
        } else {
            return null;
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
            "uig",
            "pes",
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
    function stat($langCode, $numberOfSentences)
    {
        $flagImage = $this->Html->image(
            IMG_PATH . 'flags/'.$langCode .'.png',
            array(
                'width' => 30,
                'height' => 20
            )
        );
        $numberOfSentencesHtml = '<span class="total">'.$numberOfSentences.'</span>';
        
        $linkToAllSentences = $this->Html->link(
            $flagImage . $numberOfSentencesHtml,
            array(
                "controller" => "sentences",
                "action" => "show_all_in",
                $langCode,
                'none',
                'none',
                'indifferent',
            ),
            array(),
            null,
            false
        );
        
        ?>
        <li class="stat" title="<?php echo $this->codeToName($langCode); ?>">
        <?php echo $linkToAllSentences; ?>
        </li>
        <?php
    }
}
?>
