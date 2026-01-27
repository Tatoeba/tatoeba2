<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2020 Tatoeba Project
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
 */
namespace App\Lib;

class Licenses {
    /**
     * Return all valid audio licenses
     *
     * @return array
     **/
    public static function getAudioLicenses() {
        static $licenses = [];

        if (empty($licenses)) {
            $licenses = [
                '' => ['name' => __('No license for offsite use')],
                'CC0 1.0' => [
                    'url' => 'https://creativecommons.org/publicdomain/zero/1.0/',
                ],
                'CC BY 4.0' => [
                    'url' => 'https://creativecommons.org/licenses/by/4.0/',
                ],
                'CC BY-NC 4.0' => [
                    'url' => 'https://creativecommons.org/licenses/by-nc/4.0/',
                ],
                'CC BY-SA 4.0' => [
                    'url' => 'https://creativecommons.org/licenses/by-sa/4.0/',
                ],
                'CC BY-NC-ND 3.0' => [
                    'url' => 'https://creativecommons.org/licenses/by-nc-nd/3.0/',
                ],
            ];
        }
        return $licenses;
    }

    /**
     * Return all valid sentence licenses
     *
     * @return array
     **/
    public static function getSentenceLicenses() {
        static $license = [];

        if (empty($licenses)) {
            $licenses = [
                // Keep the following in the same order as sentences.license field enum
                // so that `SELECT license+0 FROM sentences` yields the same array index
                '' => [
                    'name' => __('Licensing issue'),
                    'admin_only' => true,
                ],
                'CC BY 2.0 FR' => [
                    'url' => 'https://creativecommons.org/licenses/by/2.0/fr/',
                ],
                'CC0 1.0' => [
                    'url' => 'https://creativecommons.org/publicdomain/zero/1.0/',
                ],
            ];
        }

        return $licenses;
    }

    /**
     * Map license display names to keys
     *
     * @param array $licenses  Array as returned by getSentenceLicenses() or
     *                         getAudioLicenses()
     *
     * @return array
     **/
    public static function nameToKeys($licenses) {
        $map = [];
        foreach($licenses as $key => $license) {
            $name = $license['name'] ?? $key;
            $map[$name] = $key;
        }
        return $map;
    }
}
