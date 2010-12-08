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
            case 'af' :     return 'afr';
            case 'ar' :     return 'ara';
            case 'be' :     return 'bel';
            case 'bg' :     return 'bul';
            case 'bn' :     return 'ben';
            case 'br' :     return 'bre';
            case 'bs' :     return 'bos';
            case 'ca' :     return 'cat';
            case 'cs' :     return 'ces';
            case 'da' :     return 'dan';
            case 'de' :     return 'deu';
            case 'el' :     return 'ell';
            case 'en' :     return 'eng';
            case 'eo' :     return 'epo';
            case 'es' :     return 'spa';
            case 'et' :     return 'est';
            case 'eu' :     return 'eus';
            case 'fi' :     return 'fin';
            case 'fa' :     return 'pes';
            case 'fr' :     return 'fra';
            case 'fo' :     return 'fao';
            case 'fy' :     return 'fry';
            case 'ga' :     return 'gle';
            case 'gl' :     return 'glg';
            case 'hi' :     return 'hin';
            case 'hr' :     return 'hrv';
            case 'hu' :     return 'hun';
            case 'hy' :     return 'hye';
            case 'iw' :     return 'heb';
            case 'is' :     return 'isl';
            case 'it' :     return 'ita';
            case 'id' :     return 'ind';
            case 'ja' :     return 'jpn';
            case 'ka' :     return 'kat';
            case 'kk' :     return 'kaz';
            case 'ko' :     return 'kor';
            case 'lt' :     return 'lit';
            case 'lv' :     return 'lvs';
            case 'ms' :     return 'zsm';
            case 'ml' :     return 'mal';
            case 'mn' :     return 'mon';
            case 'nl' :     return 'nld';
            case 'pl' :     return 'pol';
            case 'pt' :     return 'por';
            case 'pt-BR' :  return 'por';
            case 'pt-PT' :  return 'por';
            case 'qu' :     return 'que';
            case 'ro' :     return 'ron';
            case 'ru' :     return 'rus';
            case 'sk' :     return 'slk';
            case 'sa' :     return 'san';
            case 'sq' :     return 'sqi';
            case 'sv' :     return 'swe';
            case 'sr' :     return 'srp';
            case 'tr' :     return 'tur';
            case 'ug' :     return 'uig';
            case 'ur' :     return 'urd';
            case 'uk' :     return 'ukr';
            case 'uz' :     return 'uzb';
            case 'vi' :     return 'vie';
            case 'sw' :     return 'swh';
            case 'zh-CN' :  return 'cmn';
            case 'zh-TW' :  return 'cmn';
        }
        return null;
    }
}
?>
