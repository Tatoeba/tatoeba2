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
namespace App\View\Helper;

use App\View\Helper\AppHelper;
use Cake\View\View;
use App\Lib\Licenses;

class LicenseHelper extends AppHelper
{
    public $helpers = array(
        'Html',
    );

    /**
     * The licenses used in the current context (sentences or audio files)
     *
     * Should be set in SentenceLicenseHelper and AudioLicenseHelper
     */
    protected $licenses = [];

    /**
     * Get the displayable name for a license
     *
     * If there is an URL available, returns optionally a clickable link.
     *
     * @param string $license   Key for the license in $licenses
     *
     * @return string
     */
    public function getLicenseName($license, $link = true) {
        if (empty($license) || !isset($this->licenses[$license])) {
            $name = $this->licenses['']['name'];
        } else {
            $name = $this->licenses[$license]['name'] ?? $license;
        }

        if ($link && isset($this->licenses[$license]['url'])) {
            return $this->Html->link($name, $this->licenses[$license]['url']);
        } else {
            return $name;
        }
    }

    /**
     * Get the options for a selection control
     *
     * @param boolean $admin  Whether to get the options for regular users
     *                        or for an admin
     *
     * @return array
     **/
    public function getLicenseOptions($admin = false) {
        foreach ($this->licenses as $key => $license) {
            if (!isset($license['admin_only']) || ($license['admin_only'] && $admin)) {
                $keyToName[$key] = $this->getLicenseName($key, false);
            }
        }
        return $keyToName;
    }

    /**
     * Checks whether a license is known.
     *
     * @param string $license   Key for the license in $licenses
     *
     * @return boolean
     */
    public function isKnownLicense($license) {
        return isset($this->licenses[$license]);
    }
}
?>
