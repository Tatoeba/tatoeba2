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

App::import('Vendor', array('CountriesList', 'LanguagesLib'));

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

    private function get_ldml($locale_id) {
        $filename = $locale_id.'.xml';
        $file = TMP.$filename;
        if (file_exists($file)) {
            echo "Using cached file $file\n";
        } else {
            $url = 'http://unicode.org/cldr/trac/export/HEAD/trunk/common/main/'.$filename;
            if (!$this->download_ldml($url, $file)) {
                die("Error: the '$locale_id' LDML file is required.\n");
            }
        }
        return $file;
    }

    private function countries_from_ldml_file($filename) {
        $countries_trans = array();
        $ldml = simplexml_load_file($filename, 'SimpleXMLElement');
        foreach ($ldml->{'localeDisplayNames'}->{'territories'}->{'territory'}
                 as $country_trans) {
            $translated_into = trim($country_trans->attributes()->{'type'});
            if (is_numeric($translated_into) ||
                $translated_into == 'ZZ' ||
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

    private function countries_array_to_php($countries) {
        $lines = array();
        foreach ($countries as $code => $name) {
            $name = preg_replace("/'/", "\\'", $name);
            $lines[] = sprintf("'%s' => __d('countries', '%s', true)", $code, $name);
        }
        return implode(",\n            ", $lines);
    }

    private function CLDR_to_PHP_array($lang) {
        $ldml_file = $this->get_ldml($lang);
        $countries = $this->countries_from_ldml_file($ldml_file);
        $countries_as_php = $this->countries_array_to_php($countries);

        $php_file = APP.'vendors'.DS."countries_list.php";
        $fh = fopen($php_file, 'w');
        $our_name = get_class($this);
        fprintf($fh, "<?php
{$this->CLDR_copyright}

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

    private function write_po_header($fh) {
        fprintf($fh,
                $this->CLDR_copyright."\n".
                'msgid ""'."\n".
                'msgstr ""'."\n".
                '"MIME-Version: 1.0\n"'."\n".
                '"Content-Type: text/plain; charset=utf-8\n"'."\n".
                '"Content-Transfer-Encoding: 8bit\n"'."\n");
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

    private function write_po($tatoeba_countries, $countries_targ) {
        $tmp_po_file = tempnam(TMP, 'countries');
        if (!$tmp_po_file) {
            die("Error: couldn’t create temporary file!\n");
        }
        if (!($fh = fopen($tmp_po_file, 'w'))) {
            die("Error: couldn’t write $tmp_po_file!\n");
        }

        $this->write_po_header($fh);
        foreach ($tatoeba_countries as $code => $english) {
            $translation = isset($countries_targ[$code]) ? $countries_targ[$code] : '';
            $this->write_po_translation($fh, $english, $translation);
        }
        fclose($fh);
        return $tmp_po_file;
    }

    private function merge_po_with_pot($tmp_po_file, $tatoeba_code) {
        $this->check_if_gettext_available('msgmerge');
        $po_file  = APP.'locale'.DS.$tatoeba_code.DS.'LC_MESSAGES'.DS.'countries.po';
        $pot_file = APP.'locale'.DS.'countries.pot';
        exec("msgmerge -o '$po_file' '$tmp_po_file' '$pot_file'", $output, $retval);
        unlink($tmp_po_file);
        if ($retval != 0) {
            die("Error: a problem occured while generating '$po_file'.\n");
        }
        print("Wrote $po_file.\n");
    }

    private function CLDR_to_po($cldr_code, $tatoeba_code) {
        $tatoeba_countries = $this->get_tatoeba_countries();
        if (!$tatoeba_countries) {
            die("Error: no English countries found.\nTry running this script with 'eng' as parameter.");
        }

        $ldml_file_targ = $this->get_ldml($cldr_code);
        $countries_targ = $this->countries_from_ldml_file($ldml_file_targ);

        $tmp_po_file = $this->write_po($tatoeba_countries, $countries_targ);
        $this->merge_po_with_pot($tmp_po_file, $tatoeba_code);
    }

    private function die_usage() {
        $this_script = basename(__FILE__, '.php');
        die("\nThis script generates the country list in English (as PHP code) when given the 'eng' parameter. Given any other language code, it generates its translation into this language based on data from the CLDR project (as PO file).\n\n".
"  Usage: $this_script <3-letters-tatoeba-ui-code>\n".
"Example: $this_script spa\n");
    }

    public function main() {
        if (count($this->args) < 1) {
            $this->die_usage();
        }

        $tatoeba_code = $this->args[0];
        $cldr_code = LanguagesLib::iso639_3_To_Iso639_1($tatoeba_code);

        if ($cldr_code == 'en') {
            $this->CLDR_to_PHP_array($cldr_code);
        } else {
            $this->CLDR_to_po($cldr_code, $tatoeba_code);
        }
    }
}
