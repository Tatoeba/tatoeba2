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

    const forSentences = ['', 'CC0 1.0', 'CC BY 2.0 FR'];
    const forAudio = ['', 'CC BY 4.0', 'CC BY-NC 4.0', 'CC BY-SA 4.0', 'CC BY-NC-ND 3.0'];

    public static function allLicenses() {
        static $licenses = [];

        if (empty($licenses)) {
            $licenses = [
                '' => [
                    'name' => __('Unknown license'),
                    'admin_only' => true,
                ],
                /* @translators: refers to the license used for sentence or audio recordings */
                'Public domain' => ['name' => __('Public domain')],
                'CC0 1.0' => [
                    'url' => 'https://creativecommons.org/publicdomain/zero/1.0/',
                ],
                'CC BY 2.0 FR' => [
                    'url' => 'https://creativecommons.org/licenses/by/2.0/fr/',
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
}
