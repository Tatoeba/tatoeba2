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
namespace App\View\Helper;

use App\View\Helper\AppHelper;
use App\Model\Entity\LanguageNameTrait;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;

class DownloadsHelper extends AppHelper
{
    use LanguageNameTrait;

    public $helpers = ['Html', 'Languages'];

    /**
     * Get all available per-language files for the given file
     *
     * @param string $basename Name of the file containing all languages
     *                         (without extension)
     *
     * @return array           A mapping of language code => URL for file
     **/
    private function availableFiles($basename) {
        $perLanguageDir = Folder::addPathElement(
            Configure::read('Downloads.path'),
            'per_language'
        );
        $perLanguageURL = Folder::addPathElement(
            Configure::read('Downloads.url'),
            'per_language'
        );

        $dir = new Folder($perLanguageDir);
        $paths = $dir->findRecursive(".*$basename\.tsv\.zst$");
        $map = [];
        foreach ($paths as $path) {
            $path = substr($path, strlen($perLanguageDir) + 1);
            list($code, ) = preg_split('#/#', $path);
            $url = Folder::addPathElement($perLanguageURL, $path);
            $map[$code] = $url;
        }
        return $map;
    }

    /**
     * Create the options for the per-language file selection on the
     * Downloads page
     *
     * @param string $basename Name of the file containing all languages
     *                         (without extension)
     *
     * @return array           Each item is an array with keys 'language'
     *                         and 'url'.
     *                         The array will always contain at least one item
     *                         (for the all-languages file).
     **/
    public function createOptions($basename) {
        $urlForAll = Folder::addPathElement(
            Configure::read('Downloads.url'),
            "$basename.csv.zst"
        );
        $options[0] = [
            'language' => __('All languages'),
            'url' => $urlForAll
        ];

        $urls = $this->availableFiles($basename);
        if (!empty($urls)) {
            $languages = array_intersect_key(
                $this->Languages->unknownLanguagesArray(false),
                $urls
            );


            foreach($languages as $code => $name) {
                $options[] = [
                    'language' => $name,
                    'url' => $urls[$code]
                ];
            };
        }

        return $options;
    }

    /**
     * Create HTML for file format
     *
     * @param array $fields Array of field names
     *
     * @return string
     **/
    public function fileFormat($fields) {
        if (empty($fields)) {
            return '';
        }
        $tab = $this->Html->tag(
            'span',
            /* @translators: tab character, used on the Downloads page,
               see https://en.wikipedia.org/wiki/Tab_key (noun) */
            format('[{}]', __('tab')),
            ['class' => 'symbol']
        );
        $fieldSpan = function ($field) {
            return $this->Html->tag('span', $field, ['class' => 'param']);
        };

        return implode(" $tab ", array_map($fieldSpan, $fields));
    }
}
