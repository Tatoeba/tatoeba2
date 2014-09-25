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

App::import('Helper');
App::import('Helper', 'Languages');

class CLDRLanguageNamesShell extends Shell {

    private $po_files = array();
    private $tatoeba_languages;
    private $english_lang_to_locale_id;

    private function list_english_lang_to_locale_id() {
        $ldml_file = $this->get_ldml('en');
        if (!$ldml_file) {
            die("The english LDML file is required.\n");
        }
        $localized_translations = $this->parse_ldml_file($ldml_file);
        $this->english_lang_to_locale_id = array_flip($localized_translations);
    }

    private function strip_extension($filename) {
        return substr($filename, 0, strrpos($filename, '.'));
    }

    private function add_po_file($filename) {
        $code = $this->strip_extension($filename);
        $code = substr($code, strrpos($code, '-') + 1);
        $this->po_files[$code] = $filename;
    }

    private function add_po_files_from_dir($dir) {
        $files = scandir($dir);
        foreach($files as $filename) {
            if (pathinfo($filename, PATHINFO_EXTENSION) == 'po') {
                $this->add_po_file($dir.$filename);
            }
        }
    }

    private function get_tatoeba_languages() {
        Configure::write('Config.language', 'eng');
        $languagesHelper = new LanguagesHelper();
        $this->tatoeba_languages = $languagesHelper->onlyLanguagesArray();
    }

    private function get_ldml($locale_id) {
        $filename = $locale_id.'.xml';
        $file = TMP.$filename;
        if (file_exists($file)) {
            echo "Using cached file $file\n";
        } else {
            $url = 'http://unicode.org/cldr/trac/export/HEAD/trunk/common/main/'.$filename;
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
            $translated_into = $lang_translation->attributes()->{'type'};
            $lang_translations["$translated_into"] = "$lang_translation";
        }
        return $lang_translations;
    }

    private function write_po_header($fp) {
        fprintf($fp, 
                'msgid ""'."\n".
                'msgstr ""'."\n".
                '"MIME-Version: 1.0\n"'."\n".
                '"Content-Type: text/plain; charset=utf-8\n"'."\n".
                '"Content-Transfer-Encoding: 8bit\n"'."\n\n");
    }

    private function generate_lang_po_file($existing_po_file, $translations) {
        $po_file = TMP.basename($existing_po_file, '.po').'.langs.po';
        if (!($fp = fopen($po_file, 'w'))) {
            echo "Couldn’t write $po_file!\n";
            return;
        }

        $this->write_po_header($fp);

        $nb_translations = 0;
        foreach ($this->tatoeba_languages as $iso_code => $lang_in_english) {
            if (!isset($this->english_lang_to_locale_id[$lang_in_english])) {
                continue;
            }
            $locale_id = $this->english_lang_to_locale_id[$lang_in_english];

            if (!isset($translations[$locale_id])) {
                continue;
            }
            fprintf($fp, "msgid \"%s\"\nmsgstr \"%s\"\n\n",
                    $lang_in_english, $translations[$locale_id]);
            $nb_translations++;
        }

        fclose($fp);
        echo "Wrote $nb_translations translation(s) into $po_file.\n";
        return $po_file;
    }

    private function die_usage() {
        die("\nThis shell grabs language name translations from the CLDR project, and inserts them into some given .po file(s). The .po file should be named as default-XX.pl where XX is the locale id as CLDR name it. You can get a list of all the locale ids here: http://unicode.org/cldr/trac/browser/trunk/common/main/\n\n".
'Usage: '.basename(__FILE__, '.php')." ( default-XX.po | /path/to/po/files/ )...\n");
    }

    private function parse_args() {
        foreach ($this->args as $arg) {
            if (is_file($arg)) {
                $this->add_po_file($arg);
            } elseif(is_dir($arg)) {
                $this->add_po_files_from_dir($arg);
            } else {
                echo "Can't open '$arg'.\n";
                $this->die_usage();
            }
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

    public function main() {
        $this->check_prerequistes();
        $this->parse_args();
        $this->get_tatoeba_languages();
        $this->list_english_lang_to_locale_id();

        foreach($this->po_files as $locale_id => $po_file) {
            printf("======= Processing $po_file...\n");
            $ldml_file = $this->get_ldml($locale_id);
            if (!$ldml_file) {
                continue;
            }
            $localized_translations = $this->parse_ldml_file($ldml_file);
            $lang_po_file = $this->generate_lang_po_file($po_file, $localized_translations);
            $this->merge_lang_po_file($po_file, $lang_po_file);
        }
    }
}
