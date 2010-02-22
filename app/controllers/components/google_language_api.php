<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 * Component for Google language detection.
 *
 * @category Default
 * @package  Components
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class GoogleLanguageApiComponent extends Object
{
    /**
     * This function uses cURL : http://de.php.net/manual/en/curl.installation.php
     *
     * The language detection is done by Google's language detect API.
     * Google documentation : 
     *   http://code.google.com/apis/ajaxlanguage/documentation/reference.html
     *
     * The function returns an associative array with the indices :
     *   "language", "isReliable" and "confidence".     
     * which is after transform into a tatoeba language code
     *
     * @param string $text Sentence to detect.
     *
     * @return string 
     */
    public function detectLang($text)
    {
        $textToAnalyze = urlencode($text);
        
        $langDetectUrl = "http://ajax.googleapis.com/ajax/services/language/detect?";
        $version = "v=1.0";
        $url = $langDetectUrl . $version . "&q=" . $textToAnalyze; 
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, "http://tatoeba.fr");
        $body = curl_exec($ch);
        curl_close($ch);

        // now, process the JSON string
        $json = json_decode($body, true);

        if ($json['responseStatus'] != 200) {
            // if something goes wrong
            return 'und';
        }
        $googleLang = $json['responseData']['language']; 
        return $this->google2TatoebaCode($googleLang);
    }
    
    /** 
     * Converts Google code into ISO-639-3 code.
     *
     * @param string $googleCode Language code used by Google.
     *
     * @return string
     */
    public function google2TatoebaCode($googleCode)
    {
        switch ($googleCode) {
            case 'ar' :     return 'ara';
            case 'bg' :     return 'bul';
            case 'cs' :     return 'ces';
            case 'de' :     return 'deu';
            case 'el' :     return 'ell';
            case 'en' :     return 'eng';
            case 'eo' :     return 'epo';
            case 'es' :     return 'spa';
            case 'et' :     return 'est';
            case 'fi' :     return 'fin';
            case 'fr' :     return 'fra';
            case 'iw' :     return 'heb';
            case 'it' :     return 'ita';
            case 'id' :     return 'ind';
            case 'ja' :     return 'jpn';
            case 'ka' :     return 'kat';
            case 'ko' :     return 'kor';
            case 'ms' :     return 'zsm';
            case 'nl' :     return 'nld';
            case 'no' :     return 'nob';
            case 'pl' :     return 'pol';
            case 'pt' :     return 'por';
            case 'pt-BR' :  return 'por';
            case 'pt-PT' :  return 'por';
            case 'ru' :     return 'rus';
            case 'sv' :     return 'swe';
            case 'tr' :     return 'tur';
            case 'uk' :     return 'ukr';
            case 'vi' :     return 'vie';
            case 'zh-CN' :  return 'cmn';
            case 'zh-TW' :  return 'cmn';
        }
        return null;
    }
}
?>
