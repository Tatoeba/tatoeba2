<?php
/**
 *  Tatoeba Project, free collaborative creation of languages corpuses project
 *  Copyright (C) 2014  Gilles Bedel
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

use App\Lib\LanguagesLib;
use Cake\Console\Shell;
use Cake\I18n\I18n;

class LanguageNamesShell extends Shell {

    private $po_files = array();
    private $tatoeba_languages;

    private function list_english_translations($source) {
        $english_translations = array();
        foreach ($this->get_localized_translations('en', $source) as $iso_code => $lang_in_english) {
            $tatoeba_name = $this->tatoeba_languages[$iso_code];
            if ($tatoeba_name != $lang_in_english) {
                printf("ISO code \"%s\" is called \"%s\" by Tatoeba, but \"%s\" in $source. Skipping translation.\n",
                    $iso_code,
                    $tatoeba_name,
                    $lang_in_english);
                continue;
            }
            $english_translations[$iso_code] = $lang_in_english;
        }

        return $english_translations;
    }

    private function strip_extension($filename) {
        return substr($filename, 0, strrpos($filename, '.'));
    }

    private function add_po_file_or_dir($path) {
        if (is_file($path)) {
            $this->add_po_file($path);
        } elseif(is_dir($path)) {
            $this->add_po_files_from_dir($path);
        } else {
            echo "Can't open '$path'.\n";
            $this->die_usage();
        }
    }

    private function add_po_file($filename) {
        if (pathinfo($filename, PATHINFO_BASENAME) == 'languages.po') {
            $dir = pathinfo($filename, PATHINFO_DIRNAME);
            $code = pathinfo($dir, PATHINFO_BASENAME);
            $this->po_files[$code] = $filename;
        }
    }

    private function add_po_files_from_dir($dir) {
        $files = scandir($dir);
        foreach($files as $filename) {
            if($filename[0] == '.') continue; // Ignore if special or hidden.
            $this->add_po_file_or_dir($dir.'/'.$filename);
        }
    }

    private function get_tatoeba_languages() {
        I18n::setLocale('en');
        $this->tatoeba_languages = LanguagesLib::languagesInTatoeba();
    }

    private function get_localized_translations($locale_id, $source) {
        $localized_translations = array();

        if (substr($source, 0, 4) == 'cldr') {
            $alt_tag = substr($source, 4);
            $ldml_file = $this->get_ldml($locale_id);
            if (!$ldml_file) {
                echo "Couldn't get the LDML file for $locale_id.\n";
                return $localized_translations;
            }
            $ldml_data = $this->parse_ldml_file($ldml_file);
            $alt_codes = $this->parse_ldml_aliases();
            foreach ($this->tatoeba_languages as $iso_code => $lang_in_english) {
                $translation = $this->ldml_lookup($ldml_data, $iso_code.$alt_tag);
                if (!isset($translation)) {
                    // There's no name for this code in the CLDR.
                    if (!array_key_exists($iso_code, $alt_codes)) {
                        continue;
                    } else {
                        $alt_code = $alt_codes[$iso_code].$alt_tag;
                        $translation = $this->ldml_lookup($ldml_data, $alt_code);
                        if (!isset($translation)) {
                            continue;
                        }
                    }
                }
                $localized_translations[$iso_code] = $translation;
            }
        } elseif ($source == 'wikidata') {
            $query = "
                SELECT DISTINCT ?iso_code ?name
                WHERE {
                  ?language wdt:P220 ?iso_code;
                            rdfs:label ?name;
                  FILTER(lang(?name) = '$locale_id')
                  VALUES ?iso_code {";
            foreach ($this->tatoeba_languages as $iso_code => $lang_in_english) {
                $query .= "'$iso_code'";
            }
            $query .= "}}";
            $query = http_build_query(array(
                "query" => $query,
                "format" => "json",
            ));

            // Wikidata likes to know who queries their endpoint
            ini_set("user_agent", "LanguageTranslationBot/0.0 (Contact: https://tatoeba.org/contact)");

            $bindings = json_decode(file_get_contents(
                "https://query.wikidata.org/sparql?".$query
            ))->results->bindings;

            $localized_translations = array();
            foreach ($bindings as $binding) {
                $localized_translations[$binding->iso_code->value] = $binding->name->value;
            }
        } else {
            die("Unknown translation source '$source'. Only 'cldr' (with optional suffixes) and 'wikidata' are supported.\n");
        }
        return $localized_translations;
    }

    private function get_ldml($locale_id) {
        $filename = $locale_id.'.xml';
        return $this->get_ldml_file('main', $filename);
    }

    private function get_ldml_metadata() {
        return $this->get_ldml_file('supplemental', 'supplementalMetadata.xml');
    }

    private function get_ldml_file($directory, $filename) {
        $file = TMP.$filename;
        if (file_exists($file)) {
            echo "Using cached file $file\n";
        } else {
            $url = "https://raw.githubusercontent.com/unicode-org/cldr/master/common/$directory/$filename";
            echo "Downloading $url... ";
            $ldml = @file_get_contents($url);
            if (!$ldml) {
                echo "failed\n";
                return null;
            }
            if (!@file_put_contents($file, $ldml)) {
                echo "can't write file!\n";
                return null;
            }
            echo "done\n";
        }
        return $file;
    }
    
    private function parse_ldml_file($filename) {
        $lang_translations = array();
        $ldml = simplexml_load_file($filename, 'SimpleXMLElement');
        foreach ($ldml->{'localeDisplayNames'}->{'languages'}->{'language'}
                 as $lang_translation) {
            $iso_code = $lang_translation->attributes()->{'type'};
            $alt_tag = $lang_translation->attributes()->{'alt'};
            if (isset($alt_tag)) {
                $iso_code .= "-$alt_tag";
            }
            $lang_translations["$iso_code"] = "$lang_translation";
        }
        return $lang_translations;
    }

    private function parse_ldml_aliases() {
        $file = $this->get_ldml_metadata();
        $aliases = array();
        $ldml = simplexml_load_file($file, 'SimpleXMLElement');
        foreach ($ldml->{'metadata'}->{'alias'}->{'languageAlias'} as $alias) {
            $iso_code = $alias->attributes()->{'type'};
            $alt_code = $alias->attributes()->{'replacement'};
            $aliases["$iso_code"] = "$alt_code";
        }
        return $aliases;
    }

    private function ldml_lookup($ldml_data, $iso_code) {
        if (isset($ldml_data[$iso_code])) {
            return $ldml_data[$iso_code];
        } else {
            // Try removing extra tags from the code.
            $dash_pos = strrpos($iso_code, '-');
            if ($dash_pos !== false) {
                $iso_code = substr($iso_code, 0, $dash_pos);
                return $this->ldml_lookup($ldml_data, $iso_code);
            }
        }
        return NULL;
    }

    private function write_po_header($fp) {
        fprintf($fp, 
                'msgid ""'."\n".
                'msgstr ""'."\n".
                '"MIME-Version: 1.0\n"'."\n".
                '"Content-Type: text/plain; charset=utf-8\n"'."\n".
                '"Content-Transfer-Encoding: 8bit\n"'."\n\n");
    }

    private function generate_lang_po_file($existing_po_file, $translation_pairs) {
        $po_file = TMP.basename($existing_po_file, '.po').'.langs.po';
        if (!($fp = fopen($po_file, 'w'))) {
            echo "Couldn’t write $po_file!\n";
            return;
        }

        $this->write_po_header($fp);

        $nb_translations = 0;
        foreach ($translation_pairs as $iso_code => $translation_pair) {
            fprintf($fp, "msgid \"%s\"\nmsgstr \"%s\"\n\n",
                    $translation_pair[0], $translation_pair[1]);
            $nb_translations++;
        }

        fclose($fp);
        echo "Wrote $nb_translations translation(s) into $po_file.\n";
        return $po_file;
    }

    private function die_usage() {
        die("\nThis shell grabs language name translations from the CLDR project and/or Wikidata, and inserts them into some given .po file(s). The .po file should have a path like XX/languages.po where XX is the locale id. You can get a list of all the locale ids here:\n\tCLDR: https://www.unicode.org/cldr/charts/latest/summary/root.html\n\tWikidata: https://en.wikipedia.org/wiki/List_of_Wikipedias\nWhen selecting CLDR as a translation source, any suffix attached to the 'cldr' will select the corresponding alternative names, e.g. 'cldr-long' for long names.\n".
'Usage: '.basename(__FILE__, '.php')." <comma-separated list of translation sources, e.g. 'wikidata,cldr-long,cldr-menu,cldr'> ( XX/languages.po | /path/to/po/files/ )...\n");
    }

    private function parse_args() {
        $this->sources = explode(',', array_shift($this->args));
        foreach ($this->args as $arg) {
            $this->add_po_file_or_dir($arg);
        }
        if (!$this->po_files) {
            $this->die_usage();
        }
    }

    private function check_if_gettext_available($command) {
        exec("which $command", $output, $retval);
        if ($retval != 0) {
            die("Please install the gettext utility '$command'.\nIt should be part of the gettext pacakge.\n");
        }
    }

    private function check_prerequistes() {
        $this->check_if_gettext_available("msgcat");
        $this->check_if_gettext_available("msgfmt");
        $this->check_if_gettext_available("msgunfmt");
    }

    private function merge_lang_po_file($po_file, $lang_po_file) {
        $merged_po_file = $this->strip_extension($po_file).".withlangs.po";
        
exec("msgcat --use-first '$po_file' '$lang_po_file' -o '$merged_po_file'", $output, $retval);
        if ($retval == 0) {
            $original = exec("msgfmt -o - '$po_file' | msgunfmt | grep -c ^msgid");
            $new      = exec("msgfmt -o - '$merged_po_file' | msgunfmt | grep -c ^msgid");
            $new_translations = $new - $original;
            if ($new_translations > 0) {
                printf("Successfully wrote %s with %s new translation(s).\n",
                       $merged_po_file, $new - $original);
                printf("Use that obscure command to print the new translation(s):\n".
                       "diff --suppress-common-lines <(msgfmt -o - '%s' | msgunfmt) ".
                       "<(msgfmt -o - '%s' | msgunfmt)\n",
                       $po_file, $merged_po_file);
            } else {
                printf("Wrote '%s' without a single new translation. What a waste of time.\n", $merged_po_file);
            }
        }
    }

    /**
     * Turn two arrays into an array of pairs. The result will only have keys
     * shared by both arrays. This function differs from array_map in that it
     * does not force keys to be sequential integers.
     */
    private function array_zip($array1, $array2) {
        $result = array();
        foreach ($array1 as $key => $value1) {
            if (!isset($array2[$key])) continue;
            $result[$key] = [$value1, $array2[$key]];
        }
        return $result;
    }

    public function main() {
        $this->check_prerequistes();
        $this->parse_args();
        $this->get_tatoeba_languages();
        $english_translations = array();
        foreach($this->sources as $source) {
            $english_translations[$source] = $this->list_english_translations($source);
        }

        foreach($this->po_files as $locale_id => $po_file) {
            printf("======= Processing $po_file...\n");
            $translation_pairs = array();
            foreach($this->sources as $source) {
                $translations = $this->get_localized_translations($locale_id, $source);
                $translation_pairs = array_merge(
                    $translation_pairs,
                    $this->array_zip(
                        $english_translations[$source],
                        $translations));
            }
            $lang_po_file = $this->generate_lang_po_file($po_file, $translation_pairs);
            $this->merge_lang_po_file($po_file, $lang_po_file);
        }
    }
}
