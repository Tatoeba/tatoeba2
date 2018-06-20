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

App::uses('AppHelper', 'View/Helper');

class LicenseHelper extends AppHelper
{
    public $helpers = array(
        'Html',
    );

    private $licenses;

    public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
        $this->licenses = array(
            '' => array('name' => __('No license for offsite use')),
            /* @translators: refers to the license used for sentence or audio recordings */
            'Public domain' => array('name' => __('Public domain')),
            'CC0 1.0' => array(
                'url' => 'https://creativecommons.org/publicdomain/zero/1.0/',
            ),
            'CC BY 2.0 FR' => array(
                'url' => 'https://creativecommons.org/licenses/by/2.0/fr/',
            ),
            'CC BY 4.0' => array(
                'url' => 'https://creativecommons.org/licenses/by/4.0/',
            ),
            'CC BY-NC 4.0' => array(
                'url' => 'https://creativecommons.org/licenses/by-nc/4.0/',
            ),
            'CC BY-SA 4.0' => array(
                'url' => 'https://creativecommons.org/licenses/by-sa/4.0/',
            ),
            'CC BY-NC-ND 3.0' => array(
                'url' => 'https://creativecommons.org/licenses/by-nc-nd/3.0/',
            ),
        );
    }

    public function getLicenseName($license) {
        if (empty($license) || !isset($this->licenses[$license])) {
            $license = __x('license', 'for Tatoeba only');
        } elseif (isset($this->licenses[$license]['url'])) {
            $license = $this->licenseLink($license);
        } elseif (isset($this->licenses[$license]['name'])) {
            // TODO: html quote
            $license = $this->licenses[$license]['name'];
        }
        return $license;
    }

    public function getLicenseOptions($default = null) {
        $keyToName = array();
        foreach ($this->licenses as $key => $val) {
            $keyToName[$key] = isset($val['name']) ? $val['name'] : $key;
        }
        return $keyToName;
    }

    public function isKnownLicense($license) {
        return isset($this->licenses[$license]);
    }

    public function licenseLink($license) {
        $name = isset($this->licenses[$license]['name']) ?
                $this->licenses[$license]['name'] :
                $license;
        return $this->Html->link(
            $name,
            $this->licenses[$license]['url']
        );
    }
}
?>
