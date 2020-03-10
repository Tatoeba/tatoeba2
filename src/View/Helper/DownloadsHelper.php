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

class DownloadsHelper extends AppHelper
{
    use LanguageNameTrait;

    public $helpers = ['Html', 'Languages'];

    const PER_LANGUAGE_DIR = '/var/www-downloads/exports/per_language';
    const DOWNLOAD_URL = 'https://downloads.tatoeba.org/exports';

    /**
     * Get all available per-language files for the given file
     *
     * @param string $basename Name of the file containing all languages
     *                         (without extension)
     *
     * @return array           A mapping of language code => URL for file
     **/
    private function availableFiles($basename) {
        $command = 'find ' . self::PER_LANGUAGE_DIR . ' -type f ' .
                   "-name '*$basename.tsv.bz2' -printf '%P\\n' " .
                   '2> /dev/null';
        $stdout = trim(shell_exec($command));
        if (empty($stdout)) {
            return [];
        }
        $paths = explode("\n", $stdout);

        $map = [];
        foreach ($paths as $path) {
            list($code, ) = preg_split('#/#', $path);
            $url = self::DOWNLOAD_URL . DS . 'per_language' . DS . $path;
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
        $urlForAll = self::DOWNLOAD_URL . DS . $basename . '.tar.bz2';
        $options[0] = [
            'language' => __('All languages'),
            'url' => $urlForAll
        ];

        $urls = $this->availableFiles($basename);
        if (!empty($urls)) {
            $languages = array_intersect_key(
                $this->Languages->onlyLanguagesArray(false) +
                ['unknown' => __x('dropdown-list', 'Unknown language')],
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
            format('[{}]', __('tab')),
            ['class' => 'symbol']
        );
        $fieldSpan = function ($field) {
            return $this->Html->tag('span', $field, ['class' => 'param']);
        };

        return implode($tab, array_map($fieldSpan, $fields));
    }
}
