<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2016  Gilles Bedel
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

class AudioHelper extends AppHelper
{
    public $helpers = array(
    );

    public function getLicenses() {
        return array(
            /* @translators: refers to the license used for audio recordings */
            '' => __('No license for offsite use', true),
            /* @translators: refers to the license used for audio recordings */
            'Public domain' => __('Public domain', true),
            'CC BY-NC 4.0' => 'CC BY-NC 4.0',
        );
    }
}
?>
