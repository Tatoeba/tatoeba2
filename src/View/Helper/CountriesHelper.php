<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2015  Gilles Bedel
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

use App\Lib\CountriesList;
use App\View\Helper\AppHelper;

class CountriesHelper extends AppHelper
{
    private $countries = array(); // memoizes the localized country list

    public function getAllCountries() {
        if (!$this->countries) {
            $CountriesList = new CountriesList();
            $this->countries = $CountriesList->list;
            $this->localizedAsort($this->countries);
        }
        return $this->countries;
    }

    public function getCountryNameByCode($code) {
        $countries = $this->getAllCountries();
        return isset($countries[$code]) ? $countries[$code] : false;
    }
}
?>
