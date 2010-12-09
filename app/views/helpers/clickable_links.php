<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  SIMON Allan <allan.simon@supinfo.com>
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
 * Helper for links
 *
 * @category Utilities
 * @package  Helpers
 * @author   SIMON Allan <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

class ClickableLinksHelper extends AppHelper
{

    /**
     * Replace URLs by clickable URLs.
     * Inspired from :
     * http://prajapatinilesh.wordpress.com/2007/08/08/php-make-urls-clickable-and-short-down/
     *
     * @param array $text Text to process.
     * 
     * @return string
     */
    public function clickableURL($text)
    {
        // get rid of \r
        $text = preg_replace('#\r#u', '', $text);
        
        $pattern = '/((ht|f)tps?:\/\/([\w\.]+\.)?[\w-]+(\.[a-zA-Z]{2,4})?[^\s\r\n\(\)"\'\,\!<]+)/siu';
        $match = preg_match_all($pattern, $text, $urls);
        
        if ($match) {
            $maxUrlLength = 50;
            $offset1 = ceil(0.65*$maxUrlLength) - 2;
            $offset2 = ceil(0.30*$maxUrlLength) - 1;
            
            
            foreach (array_unique($urls[1]) as $url) {
                if (strlen($url) > $maxUrlLength) {
                    $urlText = substr($url, 0, $offset1) 
                        . '...' 
                        . substr($url, -$offset2);
                } else {
                    $urlText = $url;
                }
                
                // Checking last character and taking it out if it's a puncturation
                $unwantedLastCharacters = array('?', '!', '.', ',', ')', ';', ':');
                $lastCharacter = substr($url, -1, 1);
                if (in_array($lastCharacter, $unwantedLastCharacters)) {
                    $url = substr($url, 0, -1);
                    $urlText = substr($urlText, 0, -1);
                }
                
                // There was a problem when one URL is be included in another one.
                // For instance, http://tatoeba.org is included in 
                // http://tatoeba.org/wall.
                // Because of the presence of http://tatoeba.org, the other URLS
                // beginning with http://tatoeba.org would be messed up.
                // That's why we need to do replace only if there's a stop character.
                $escapedUrl = quotemeta($url);  // meta characters
                $escapedUrl = str_replace('/', '\/', $escapedUrl); // identifier
                $escapedUrl = str_replace('|', '\|', $escapedUrl); // pipe
                $pattern2 = '/('.$escapedUrl.'([\?!\.,\);:< \n]))|('.$escapedUrl.'$)/u';
                $text = preg_replace(
                    $pattern2, 
                    '<a href="'. $url .'">'. $urlText .'</a>$2', 
                    $text
                );
            }
        }
        
        return $text;
    }

}
?>