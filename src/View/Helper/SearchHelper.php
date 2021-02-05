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

use App\Model\CurrentUser;
use App\View\Helper\AppHelper;


class SearchHelper extends AppHelper
{
    public $helpers = array('Form', 'Languages');

    private $langs;

    public function selectLang($fieldName, $selectedLanguage, $options = array()) {
        if (!$this->langs) {
            $this->langs = $this->Languages->getSearchableLanguagesArray();
        }

        $options = array_merge(
            array(
                'name' => $fieldName,
                'languages' => $this->langs,
                'initialSelection' => $selectedLanguage,
                'placeholder' => __('Any language'),
            ),
            $options
        );
        return $this->_View->element(
            'language_dropdown',
            $options
        );
    }

    public function highlightMatches($highlight, $text) {
        list($markers, $excerpts) = $highlight;
        foreach ($excerpts as $excerpt) {
            $excerpt = h($excerpt);
            $from = str_replace($markers, '', $excerpt);
            $to = str_replace(
                $markers,
                array('<span class="match">', '</span>'),
                $excerpt
            );
            $text = str_replace($from, $to, $text);
        }
        return $text;
    }
}
?>
