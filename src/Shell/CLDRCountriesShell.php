<?php
/**
 *  Tatoeba Project, free collaborative creation of languages corpuses project
 *  Copyright (C) 2015  Gilles Bedel
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace App\Shell;

use App\Lib\CountriesList;
use Cake\Console\Shell;
use Cake\Core\App;

class CLDRCountriesShell extends Shell {
    private $CLDR_copyright = '
# This file is based on modified data files from Unicode, Inc.

# COPYRIGHT AND PERMISSION NOTICE
# Copyright © 1991-2015 Unicode, Inc. All rights reserved.
# Distributed under the Terms of Use in 
# http://www.unicode.org/copyright.html.
# 
# Permission is hereby granted, free of charge, to any person obtaining
# a copy of the Unicode data files and any associated documentation
# (the "Data Files") or Unicode software and any associated documentation
# (the "Software") to deal in the Data Files or Software
# without restriction, including without limitation the rights to use,
# copy, modify, merge, publish, distribute, and/or sell copies of
# the Data Files or Software, and to permit persons to whom the Data Files
# or Software are furnished to do so, provided that
# (a) this copyright and permission notice appear with all copies 
# of the Data Files or Software,
# (b) this copyright and permission notice appear in associated 
# documentation, and
# (c) there is clear notice in each modified Data File or in the Software
# as well as in the documentation associated with the Data File(s) or
# Software that the data or software has been modified.
# 
# THE DATA FILES AND SOFTWARE ARE PROVIDED "AS IS", WITHOUT WARRANTY OF
# ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
# WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
# NONINFRINGEMENT OF THIRD PARTY RIGHTS.
# IN NO EVENT SHALL THE COPYRIGHT HOLDER OR HOLDERS INCLUDED IN THIS
# NOTICE BE LIABLE FOR ANY CLAIM, OR ANY SPECIAL INDIRECT OR CONSEQUENTIAL
# DAMAGES, OR ANY DAMAGES WHATSOEVER RESULTING FROM LOSS OF USE,
# DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR OTHER
# TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
# PERFORMANCE OF THE DATA FILES OR SOFTWARE.
# 
# Except as contained in this notice, the name of a copyright holder
# shall not be used in advertising or otherwise to promote the sale,
# use or other dealings in these Data Files or Software without prior
# written authorization of the copyright holder.';

    private $use_short_names = array(
        'BA', /* Bosnia */
        'HK', /* Hong Kong */
        'MM', /* Myanmar */
        'MO', /* Macau */
        'PS', /* Palestine */
    );

    private function download_ldml($from, $to) {
        echo "Downloading $from... ";
        $ldml = @file_get_contents($from);
        if (!$ldml) {
            echo "failed\n";
            return false;
        }
        if (!@file_put_contents($to, $ldml)) {
            echo "can't write file!\n";
            return false;
        }
        echo "done\n";
        return true;
    }

    private function get_ldml($path) {
        $file = TMP.str_replace('/', '_', $path);
        if (file_exists($file)) {
            echo "Using cached file $file\n";
        } else {
            $url = 'https://raw.githubusercontent.com/unicode-org/cldr/master/'.$path;
            if (!$this->download_ldml($url, $file)) {
                die("Error: the LDML file '$path' is required.\n");
            }
        }
        return $file;
    }

    private function valid_regions_pattern($filename) {
        $tree = simplexml_load_file($filename, 'SimpleXMLElement');
        foreach ($tree->{'idValidity'}->{'id'} as $ids) {
            $attr = $ids->attributes();
            if ($attr->{'type'} == 'region' && $attr->{'idStatus'} == 'regular') {
                $ids = preg_replace("/([A-Z])~([A-Z])/", "[\$1-\$2]", "$ids");
                $patterns = preg_split("/[\s]+/", "$ids", -1, PREG_SPLIT_NO_EMPTY);
                $pattern = '/^('.implode('|', $patterns).')$/';
                return $pattern;
            }
        }
        die("Could not extract valid country codes\n");
    }

    private function countries_from_ldml_file($filename, $regions) {
        $regions_pattern = $this->valid_regions_pattern($regions);
        $countries_trans = array();
        $ldml = simplexml_load_file($filename, 'SimpleXMLElement');
        foreach ($ldml->{'localeDisplayNames'}->{'territories'}->{'territory'}
                 as $country_trans) {
            $translated_into = trim($country_trans->attributes()->{'type'});
            if (preg_match($regions_pattern, $translated_into) === 0 ||
                (
                    !in_array($translated_into, $this->use_short_names) &&
                    $country_trans->attributes()->{'alt'}
                ) ||
                (
                    in_array($translated_into, $this->use_short_names) &&
                    $country_trans->attributes()->{'alt'} != 'short'
                )
               ) {
                continue;
            }
            $countries_trans["$translated_into"] = "$country_trans";
        }
        return $countries_trans;
    }

    private function get_localized_countries($lang) {
        $lang_file = $this->get_ldml("common/main/$lang.xml");
        $regions_file = $this->get_ldml("common/validity/region.xml");
        return $this->countries_from_ldml_file($lang_file, $regions_file);
    }

    private function countries_array_to_php($countries) {
        $lines = array();
        foreach ($countries as $code => $name) {
            $name = preg_replace("/'/", "\\'", $name);
            $lines[] = sprintf("'%s' => __d('countries', '%s')", $code, $name);
        }
        return implode(",\n            ", $lines);
    }

    private function CLDR_to_PHP_array($lang) {
        $countries = $this->get_localized_countries($lang);
        $countries_as_php = $this->countries_array_to_php($countries);

        $php_file = APP.'Lib'.DS.'CountriesList.php';
        $fh = fopen($php_file, 'w');
        $our_name = get_class($this);
        fprintf($fh, "%s", "<?php
{$this->CLDR_copyright}

namespace App\Lib;

/**
 * This file has been autogenerated by $our_name.
 * Consider using it if you want to update this file.
 */
class CountriesList {
    public \$list;

    public function __construct() {
        \$this->list = array(
            $countries_as_php
        );
    }
}
");
        fclose($fh);
        print("Wrote $php_file.\n");
    }

    private function get_tatoeba_countries() {
        $countries_list = new CountriesList();
        return $countries_list->list;
    }

    private function write_po_header($fh, $locale) {
        $orig_po_file = APP.'Locale'.DS.$locale.DS.'countries.po';
        $orig_header = shell_exec("sed -n '0,/^$/p' '$orig_po_file'");
        fprintf($fh, "%s", $orig_header);
    }

    private function write_po_translation($fh, $english, $translation) {
        $english     = preg_replace('/"/', '\\"', $english);
        $translation = preg_replace('/"/', '\\"', $translation);
        fprintf($fh, "\nmsgid \"%s\"\nmsgstr \"%s\"\n", $english, $translation);
    }

    private function check_if_gettext_available($command) {
        exec("which $command", $output, $retval);
        if ($retval != 0) {
            die("Please install the gettext utility '$command'.\nIt should be part of the gettext pacakge.\n");
        }
    }

    private function write_po($tatoeba_countries, $countries_targ, $locale) {
        $tmp_po_file = tempnam(TMP, 'countries');
        if (!$tmp_po_file) {
            die("Error: couldn’t create temporary file!\n");
        }
        if (!($fh = fopen($tmp_po_file, 'w'))) {
            die("Error: couldn’t write $tmp_po_file!\n");
        }

        $this->write_po_header($fh, $locale);
        foreach ($tatoeba_countries as $code => $english) {
            if (isset($countries_targ[$code])) {
                $this->write_po_translation($fh, $english, $countries_targ[$code]);
            }
        }
        fclose($fh);
        return $tmp_po_file;
    }

    private function merge_new_translations($new_trans, $locale) {
        $this->check_if_gettext_available('msgmerge');
        $po_file = APP.'Locale'.DS.$locale.DS.'countries.po';
        exec("msgmerge --no-wrap --no-fuzzy-matching -o '$po_file' '$new_trans' '$po_file'", $output, $retval);
        unlink($new_trans);
        if ($retval != 0) {
            die("Error: a problem occurred while generating '$po_file'.\n");
        }
        print("Wrote $po_file.\n");
    }

    private function CLDR_to_po($locale) {
        $tatoeba_countries = $this->get_tatoeba_countries();
        if (!$tatoeba_countries) {
            die("Error: no English countries found.\nTry running this script with 'en' as parameter.");
        }

        $countries_targ = $this->get_localized_countries($locale);

        $tmp_po_file = $this->write_po($tatoeba_countries, $countries_targ, $locale);
        $this->merge_new_translations($tmp_po_file, $locale);
    }

    private function die_usage() {
        $this_script = basename(__FILE__, '.php');
        die("\nThis script generates the country list in English (as PHP code) when given the 'en' parameter. Given any other language code, it generates its translation into this language based on data from the CLDR project (as PO file).\n\n".
"  Usage: $this_script <2-letters-locale-code>\n".
"Example: $this_script es\n");
    }

    public function main() {
        if (count($this->args) < 1) {
            $this->die_usage();
        }

        $locale = $this->args[0];

        if ($locale == 'en') {
            $this->CLDR_to_PHP_array($locale);
        } else {
            $this->CLDR_to_po($locale);
        }
    }
}
