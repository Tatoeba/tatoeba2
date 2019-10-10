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
 * @link     https://tatoeba.org
 */
namespace App\Controller\Component;

use Cake\Controller\Component;


/**
 * Component for language detection.
 *
 * @category Default
 * @package  Components
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class LanguageDetectionComponent extends Component
{
    /**
     * This function uses cURL : http://de.php.net/manual/en/curl.installation.php
     *
     * The language detection is done by https://github.com/Tatoeba/Tatodetect
     *
     * @param string $text Sentence to detect.
     * @param string $user User who's adding the sentence.
     *
     * @return string
     */
    public function detectLang($text, $user = "")
    {
        $textToAnalyze = urlencode($text);
        $url = "http://127.0.0.1:4242/api/detects/simple?query=" . $textToAnalyze;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, "https://tatoeba.org");

        $body = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode != 200) {
            return null;
        }

        if ($body == "unknown") {
            return null;
        } else {
            return $body;
        }
    }
}
