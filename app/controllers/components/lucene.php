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
 * Component for Lucene.
 *
 * @category Default
 * @package  Components
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class LuceneComponent extends Object
{
    /**
     * Send search request to search engine and retrieve results.
     *
     * @param string $query    Search query.
     * @param string $langSrc  Source language.
     * @param string $langDest Target language.
     * @param int    $page     Page of the search.
     *
     * @return array
     */
    public function search($query, $langSrc = null, $langDest = null, $page = null)
    {
        $query = $this->_processQuery($query);
        $query = urlencode($query);

        // URL of active search engine. Port is either 18080 or 28080.
        $luceneUrl = "http://88.191.96.22:28080/tatoeba/search.jsp?query=";

        $url = $luceneUrl . $query;

        if ($langSrc != null AND $langSrc != 'und') {
            $url .= "&lang_src=" . $langSrc;
        }
        if ($langDest != null AND $langDest != 'und') {
            $url .= "&lang_dest=" . $langDest;
        }
        if ($page != null) {
            $url .= "&page=" . ($page-1); // because page 1 is at index 0
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, "http://tatoeba.org");
        $body = curl_exec($ch);
        curl_close($ch);

        // now, process the JSON string
        $json = json_decode($body, true);

        if ($json['responseStatus'] != 200) {
            return false;
        }

        $response = $json['responseData'];

        return $response;
    }

    /**
     * Process the query into something that the search engine can handle. It
     * should be done on the search engine side, but I don't know the code well
     * enough.
     *
     * @param string $query Search query.
     *
     * @return string
     */
    private function _processQuery($query)
    {
        $query = trim($query);
        $query = preg_replace("!\[!", "", $query);
        $query = preg_replace("!\]!", "", $query);
        if (!preg_match('!^"!', $query) AND !preg_match('!"$!', $query)) {

            // deleting little words at the beginning
            $query = preg_replace("!^[a-z]{1,3} !i", " ", $query);

            // deleting little words at the end
            $query = preg_replace("! [a-z]{1,3}\.?$!i", " ", $query);

            // deleting little words in the middle
            $query = preg_replace(
                "! [a-z]{1,3} (([a-z]{1,3} )?){1,5}!i", " ", $query
            );

            if (trim($query) == '') {
                $query = '"'.trim($query).'"';
            }

        }
        return $query;
    }
}
?>